<?php

class StaffBookingController
{
    public function __construct()
    {
        if (empty($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['staff', 'admin'], true)) {
            $action = $_GET['action'] ?? '';
            $ajaxActions = ['staff_checkin_process'];

            if (in_array($action, $ajaxActions, true)) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => false,
                    'message' => 'Quyền truy cập bị từ chối. Vui lòng đăng nhập tài khoản nhân viên.',
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            set_flash('error', 'Bạn không có quyền truy cập trang này.');
            header('Location: ?action=login');
            exit;
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
        $code = strtoupper(trim($_GET['code'] ?? ''));
        $bookingModel = new BookingModel();

        if ($id <= 0 && $code !== '') {
            $found = $bookingModel->getBookingByCode($code);
            if ($found) {
                $id = (int) $found['id'];
            }
        }

        $booking = $id > 0 ? $bookingModel->getById($id) : null;

        if (!$booking) {
            set_flash('error', 'Không tìm thấy booking. Vui lòng kiểm tra lại mã đặt vé.');
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
        $flash = get_flash();
        $view = 'staff/booking/checkin';
        require_once PATH_VIEW . 'staff/layout/layout.php';
    }

    /**
     * Endpoint JSON dành cho máy quét/AJAX: chỉ nhận booking_code.
     * Trạng thái check-in được lưu ở bảng bookings.
     */
    public function checkinProcess()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận phương thức POST.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $code = strtoupper(trim($_POST['code'] ?? ''));
        if ($code === '') {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Mã booking không được để trống.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $bookingModel = new BookingModel();
        $booking = $bookingModel->getBookingByCode($code);

        if (!$booking) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy booking với mã này.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $tickets = $bookingModel->getBookingTickets((int) $booking['id']);
        if (empty($tickets)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Booking không có vé/ghế để check-in.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $result = $bookingModel->checkInBooking((int) $booking['id'], (int) $_SESSION['user']['id']);

        if (!$result['success']) {
            http_response_code(409);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            exit;
        }

        $result['booking_id'] = (int) $booking['id'];
        $result['booking_code'] = $booking['booking_code'];
        $result['ticket_ids'] = array_map(fn($ticket) => (int) $ticket['ticket_id'], $tickets);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function printTicket()
    {
        $ids = [];
        if (!empty($_GET['id'])) {
            $ids[] = (int) $_GET['id'];
        } elseif (!empty($_GET['ids'])) {
            $ids = array_values(array_filter(array_map('intval', explode(',', $_GET['ids']))));
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

        require_once PATH_VIEW . 'staff/booking/print.php';
    }

    /**
     * Route cũ được giữ để tránh link cũ bị 404.
     * Dự án hiện check-in theo booking, không check-in từng ticket.
     */
    public function checkInTicket()
    {
        set_flash('error', 'Hệ thống hiện check-in theo booking, không check-in riêng từng vé.');
        $bookingId = (int) ($_GET['booking_id'] ?? 0);
        header('Location: ' . ($bookingId > 0
            ? '?action=staff_booking_detail&id=' . $bookingId
            : '?action=staff_checkin'));
        exit;
    }

    public function checkInBookingAll()
    {
        $this->bookingCheckIn();
    }

    /**
     * Check-in chính từ trang chi tiết booking.
     */
    public function bookingCheckIn()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            set_flash('error', 'Thao tác check-in phải được thực hiện bằng nút xác nhận.');
            header('Location: ?action=staff_checkin');
            exit;
        }

        $bookingId = (int) ($_POST['booking_id'] ?? 0);
        if ($bookingId <= 0) {
            set_flash('error', 'ID booking không hợp lệ.');
            header('Location: ?action=staff_checkin');
            exit;
        }

        $bookingModel = new BookingModel();
        $booking = $bookingModel->getById($bookingId);
        if (!$booking) {
            set_flash('error', 'Không tìm thấy booking.');
            header('Location: ?action=staff_checkin');
            exit;
        }

        $tickets = $bookingModel->getBookingTickets($bookingId);
        if (empty($tickets)) {
            set_flash('error', 'Booking không có vé/ghế để check-in.');
            header('Location: ?action=staff_booking_detail&id=' . $bookingId);
            exit;
        }

        $result = $bookingModel->checkInBooking($bookingId, (int) $_SESSION['user']['id']);
        if (!$result['success']) {
            set_flash('error', $result['message']);
            header('Location: ?action=staff_booking_detail&id=' . $bookingId);
            exit;
        }

        set_flash('success', 'Check-in booking thành công.');

        $ticketIds = array_map(fn($ticket) => (int) $ticket['ticket_id'], $tickets);
        header(
            'Location: ?action=staff_ticket_print&ids=' . implode(',', $ticketIds)
            . '&booking_id=' . $bookingId
        );
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
            header('Location: ?action=staff_food_delivery&code=' . urlencode($code));
        } else {
            header('Location: ?action=staff_booking_detail&id=' . $bookingId);
        }
        exit;
    }

    public function foodDeliveryView()
    {
        $code = strtoupper(trim($_GET['code'] ?? ''));
        $bookingModel = new BookingModel();
        $booking = null;
        $foodOrders = [];
        $hasFoodOrders = false;
        $allFoodDelivered = false;
        $deliveryTime = null;
        $deliveredBy = null;

        if ($code !== '') {
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
                        } elseif (empty($deliveryTime) && !empty($fo['delivered_at'])) {
                            $deliveryTime = date('d/m/Y H:i:s', strtotime($fo['delivered_at']));
                            $deliveredBy = $fo['delivered_by_name'];
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
