<?php

class StaffBookingController
{
    public function __construct()
    {
        // Require staff or admin role for all booking actions
        if (empty($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['staff', 'admin'])) {
            $action = $_GET['action'] ?? '';
            $ajaxActions = ['staff_checkin_process'];

            if (in_array($action, $ajaxActions)) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => 'Quyền truy cập bị từ chối. Vui lòng đăng nhập tài khoản nhân viên.']);
                exit;
            } else {
                set_flash('error', 'Bạn không có quyền truy cập trang này.');
                header('Location: ?action=login');
                exit;
            }
        }
    }

    public function list()
    {
        $keyword = trim($_GET['keyword'] ?? '');
        $bookingModel = new BookingModel();
        $bookings = $bookingModel->searchAndFilter($keyword);
        $flash = get_flash();
        $view = 'staff/booking/list';
        require_once PATH_VIEW . 'staff/layout/layout.php';
    }

    public function show()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $code = trim($_GET['code'] ?? '');
        $bookingModel = new BookingModel();

        if ($id <= 0 && !empty($code)) {
            $found = $bookingModel->getBookingByCode($code);
            if ($found) {
                $id = (int) $found['id'];
            }
        }

        $booking = null;
        if ($id > 0) {
            $booking = $bookingModel->getById($id);
        }

        if (!$booking) {
            set_flash('error', 'Không tìm thấy booking.');
            header('Location: ?action=staff_checkin');
            exit;
        }

        $tickets = $bookingModel->getBookingTickets($id);
        $foodOrders = $bookingModel->getBookingFoodOrders($id);
        $ticketTotal = array_sum(array_map(fn($ticket) => (float) $ticket['ticket_price'], $tickets));
        $foodTotal = array_sum(array_map(
            fn($food) => (float) $food['price_at_booking'] * (int) $food['quantity'],
            $foodOrders
        ));

        $flash = get_flash();
        $view = 'staff/booking/show';
        require_once PATH_VIEW . 'staff/layout/layout.php';
    }

    public function checkinScanView()
    {
        $view = 'staff/booking/checkin';
        require_once PATH_VIEW . 'staff/layout/layout.php';
    }

    public function checkinProcess()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận phương thức POST.']);
            exit;
        }

        $code = trim($_POST['code'] ?? '');
        if (empty($code)) {
            echo json_encode(['success' => false, 'message' => 'Mã check-in trống.']);
            exit;
        }

        $bookingModel = new BookingModel();
        $staffId = $_SESSION['user']['id'];

        // 1. Check if it's a ticket code
        $ticket = $bookingModel->getTicketByCode($code);
        if ($ticket) {
            if ($ticket['checkin_status'] === 'checked_in') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Ticket already checked in'
                ]);
                exit;
            }

            $bookingModel->updateTicketCheckIn($ticket['id'], $staffId, 'checked_in');
            echo json_encode([
                'success' => true,
                'type' => 'ticket',
                'ticket_id' => $ticket['id']
            ]);
            exit;
        }

        // 2. Check if it's a booking code
        $booking = $bookingModel->getBookingByCode($code);
        if ($booking) {
            $bookingId = $booking['id'];
            $tickets = $bookingModel->getBookingTickets($bookingId);
            if (empty($tickets)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Không tìm thấy vé cho booking này.'
                ]);
                exit;
            }

            // Chặn check-in lại: nếu TẤT CẢ vé trong booking đã checked_in thì báo lỗi
            $allCheckedIn = true;
            foreach ($tickets as $t) {
                if (($t['checkin_status'] ?? '') !== 'checked_in') {
                    $allCheckedIn = false;
                    break;
                }
            }
            if ($allCheckedIn) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Booking already checked in'
                ]);
                exit;
            }

            $bookingModel->updateBookingCheckInAll($bookingId, $staffId, 'checked_in');
            $ticketIds = array_map(fn($t) => $t['ticket_id'], $tickets);

            echo json_encode([
                'success' => true,
                'type' => 'booking',
                'ticket_ids' => $ticketIds
            ]);
            exit;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Mã không hợp lệ (Không tìm thấy vé hoặc booking).'
        ]);
        exit;
    }

    public function printTicket()
    {
        $ids = [];
        if (!empty($_GET['id'])) {
            $ids[] = (int) $_GET['id'];
        } elseif (!empty($_GET['ids'])) {
            $ids = array_filter(array_map('intval', explode(',', $_GET['ids'])));
        }

        if (empty($ids)) {
            set_flash('error', 'Không tìm thấy vé để in.');
            header('Location: ?action=staff_checkin');
            exit;
        }

        $bookingModel = new BookingModel();
        $tickets = [];
        foreach ($ids as $id) {
            $ticketDetail = $bookingModel->getTicketDetails($id);
            if ($ticketDetail) {
                $tickets[] = $ticketDetail;
            }
        }

        if (empty($tickets)) {
            set_flash('error', 'Không tìm thấy chi tiết vé để in.');
            header('Location: ?action=staff_checkin');
            exit;
        }

        // Render print view directly (no staff layout layout wrapper)
        require_once PATH_VIEW . 'staff/booking/print.php';
    }

    public function checkInTicket()
    {
        $ticketId = (int) ($_GET['id'] ?? 0);
        $bookingId = (int) ($_GET['booking_id'] ?? 0);
        $print = (int) ($_GET['print'] ?? 0);

        if ($ticketId <= 0) {
            set_flash('error', 'ID vé không hợp lệ.');
            header('Location: ?action=staff_booking_list');
            exit;
        }

        $bookingModel = new BookingModel();
        $staffId = $_SESSION['user']['id'];

        // Chặn check-in lại vé đã checked_in (đường đi qua GET link, không chỉ AJAX)
        $existingTicket = $bookingModel->getTicketDetails($ticketId);
        if ($existingTicket && ($existingTicket['checkin_status'] ?? '') === 'checked_in') {
            set_flash('error', 'Vé này đã được check-in trước đó.');
            if ($bookingId > 0) {
                header("Location: ?action=staff_booking_detail&id=" . $bookingId);
            } else {
                header('Location: ?action=staff_booking_list');
            }
            exit;
        }

        $bookingModel->updateTicketCheckIn($ticketId, $staffId, 'checked_in');
        set_flash('success', 'Vé đã được check-in thành công.');

        if ($print === 1) {
            header("Location: ?action=staff_ticket_print&id=" . $ticketId);
        } else {
            if ($bookingId > 0) {
                header("Location: ?action=staff_booking_detail&id=" . $bookingId);
            } else {
                header('Location: ?action=staff_booking_list');
            }
        }
        exit;
    }

    public function checkInBookingAll()
    {
        $bookingId = (int) ($_GET['booking_id'] ?? 0);

        if ($bookingId <= 0) {
            set_flash('error', 'ID booking không hợp lệ.');
            header('Location: ?action=staff_booking_list');
            exit;
        }

        $bookingModel = new BookingModel();
        $staffId = $_SESSION['user']['id'];

        // Chặn check-in lại nếu tất cả vé đã checked_in
        $tickets = $bookingModel->getBookingTickets($bookingId);
        $allCheckedIn = !empty($tickets);
        foreach ($tickets as $t) {
            if (($t['checkin_status'] ?? '') !== 'checked_in') {
                $allCheckedIn = false;
                break;
            }
        }
        if ($allCheckedIn) {
            set_flash('error', 'Booking này đã được check-in toàn bộ trước đó.');
            header("Location: ?action=staff_booking_detail&id=" . $bookingId);
            exit;
        }

        $bookingModel->updateBookingCheckInAll($bookingId, $staffId, 'checked_in');
        set_flash('success', 'Tất cả vé trong booking đã được check-in.');

        // In vé cho tất cả các ghế đã đặt trong đơn
        $ticketIds = array_map(fn($t) => $t['ticket_id'], $tickets);
        if (!empty($ticketIds)) {
            header("Location: ?action=staff_ticket_print&ids=" . implode(',', $ticketIds));
        } else {
            header("Location: ?action=staff_booking_detail&id=" . $bookingId);
        }
        exit;
    }

    public function bookingCheckIn()
    {
        $bookingId = (int) ($_GET['booking_id'] ?? 0);
        if ($bookingId <= 0) {
            set_flash('error', 'ID booking không hợp lệ.');
            header('Location: ?action=staff_checkin');
            exit;
        }

        $bookingModel = new BookingModel();
        $staffId = $_SESSION['user']['id'];

        $tickets = $bookingModel->getBookingTickets($bookingId);
        if (empty($tickets)) {
            set_flash('error', 'Không tìm thấy vé cho booking này.');
            header("Location: ?action=staff_booking_detail&id=" . $bookingId);
            exit;
        }

        // Thực hiện check-in cho toàn bộ vé
        $bookingModel->updateBookingCheckInAll($bookingId, $staffId);
        set_flash('success', 'Đã check-in toàn bộ vé thành công.');

        // Lấy lại danh sách ticket để lấy danh sách ID đầy đủ
        $ticketIds = array_map(fn($t) => $t['ticket_id'], $tickets);
        
        // Chuyển hướng đến trang in vé của toàn bộ ghế, kèm theo booking_id để quay lại
        header("Location: ?action=staff_ticket_print&ids=" . implode(',', $ticketIds) . "&booking_id=" . $bookingId);
        exit;
    }

    public function confirmFoodDelivery()
    {
        $bookingId = (int) ($_GET['booking_id'] ?? 0);
        $redirect = $_GET['redirect'] ?? '';
        if ($bookingId <= 0) {
            set_flash('error', 'ID booking không hợp lệ.');
            header('Location: ?action=staff_checkin');
            exit;
        }

        $bookingModel = new BookingModel();
        $staffId = $_SESSION['user']['id'];

        $bookingModel->confirmFoodDelivered($bookingId, $staffId);
        set_flash('success', 'Đã xác nhận giao đồ ăn thành công.');

        if ($redirect === 'food_delivery') {
            $booking = $bookingModel->getById($bookingId);
            $code = $booking['booking_code'] ?? '';
            header("Location: ?action=staff_food_delivery&code=" . urlencode($code));
        } else {
            header("Location: ?action=staff_booking_detail&id=" . $bookingId);
        }
        exit;
    }

    public function foodDeliveryView()
    {
        $code = trim($_GET['code'] ?? '');
        $bookingModel = new BookingModel();
        $booking = null;
        $foodOrders = [];
        $hasFoodOrders = false;
        $allFoodDelivered = false;
        $deliveryTime = null;
        $deliveredBy = null;

        if (!empty($code)) {
            $found = $bookingModel->getBookingByCode($code);
            if ($found) {
                $bookingId = (int) $found['id'];
                $booking = $bookingModel->getById($bookingId);
                if ($booking) {
                    $foodOrders = $bookingModel->getBookingFoodOrders($bookingId);
                    $hasFoodOrders = !empty($foodOrders);
                    $allFoodDelivered = $hasFoodOrders;
                    foreach ($foodOrders as $fo) {
                        if (($fo['delivery_status'] ?? 'pending') !== 'delivered') {
                            $allFoodDelivered = false;
                        } else {
                            if (empty($deliveryTime) && !empty($fo['delivered_at'])) {
                                $deliveryTime = date('d/m/Y H:i:s', strtotime($fo['delivered_at']));
                                $deliveredBy = $fo['delivered_by_name'];
                            }
                        }
                    }
                }
            }

            if (!$booking) {
                set_flash('error', 'Không tìm thấy booking hoặc mã đặt vé không hợp lệ.');
                header('Location: ?action=staff_food_delivery');
                exit;
            }
        }

        $flash = get_flash();
        $view = 'staff/booking/food_delivery';
        require_once PATH_VIEW . 'staff/layout/layout.php';
    }
}