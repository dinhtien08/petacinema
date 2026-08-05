<?php

class BookingController
{
    public function list()
    {
        $keyword = trim($_GET['keyword'] ?? '');
        $bookingModel = new BookingModel();
        $bookings = $bookingModel->searchAndFilter($keyword);
        $flash = get_flash();
        $view = 'admin/booking/list';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    public function add()
    {
        $userModel = new UserModel();
        $bookingModel = new BookingModel();

        $users = $userModel->getAll();
        $showtimes = $bookingModel->getShowtimeOptions();
        $foodVariants = $bookingModel->getFoodVariantOptions();
        $errors = [];
        $old = [
            'status' => 'pending',
            'seat_numbers' => '',
            'food_quantities' => [],
        ];

        $view = 'admin/booking/add';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    public function addPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?action=booking_add');
            exit;
        }

        $old = [
            'user_id' => trim($_POST['user_id'] ?? ''),
            'showtime_id' => trim($_POST['showtime_id'] ?? ''),
            'seat_numbers' => strtoupper(trim($_POST['seat_numbers'] ?? '')),
            'food_quantities' => $this->normalizeFoodQuantities($_POST['food_quantities'] ?? []),
            'status' => $_POST['status'] ?? 'pending',
        ];

        $errors = $this->validate($old);

        if (empty($errors)) {
            $bookingModel = new BookingModel();

            try {
                $bookingId = $bookingModel->createBookingWithTicketsAndFoods([
                    'booking_code' => $bookingModel->generateBookingCode(),
                    'user_id' => (int) $old['user_id'],
                    'showtime_id' => (int) $old['showtime_id'],
                    'seat_numbers' => $this->parseSeatNumbers($old['seat_numbers']),
                    'food_quantities' => $old['food_quantities'],
                    'status' => $old['status'],
                ]);

                set_flash('success', 'Thêm booking, vé và đồ ăn test thành công.');
                header('Location: ?action=booking_show&id=' . $bookingId);
                exit;
            } catch (InvalidArgumentException $e) {
                $errors['general'] = $e->getMessage();
            } catch (Throwable $e) {
                $errors['general'] = 'Không thể thêm booking: ' . $e->getMessage();
            }
        }

        $userModel = new UserModel();
        $bookingModel = new BookingModel();
        $users = $userModel->getAll();
        $showtimes = $bookingModel->getShowtimeOptions();
        $foodVariants = $bookingModel->getFoodVariantOptions();
        $view = 'admin/booking/add';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    public function show()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $bookingModel = new BookingModel();
        $booking = $bookingModel->getById($id);

        if (!$booking) {
            set_flash('error', 'Không tìm thấy booking.');
            header('Location: ?action=booking_list');
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
        $view = 'admin/booking/show';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    public function edit()
    {
        set_flash('error', 'Chức năng chỉnh sửa booking đã bị tắt (Read-only).');
        header('Location: ?action=booking_list');
        exit;
    }

    public function editPost()
    {
        set_flash('error', 'Chức năng chỉnh sửa booking đã bị tắt (Read-only).');
        header('Location: ?action=booking_list');
        exit;
    }

    public function delete()
    {
        set_flash('error', 'Chức năng xóa booking đã bị tắt (Read-only).');
        header('Location: ?action=booking_list');
        exit;
    }

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['user_id'] === '' || !ctype_digit($data['user_id']) || (int) $data['user_id'] <= 0) {
            $errors['user_id'] = 'Vui lòng chọn khách hàng.';
        }

        if ($data['showtime_id'] === '' || !ctype_digit($data['showtime_id']) || (int) $data['showtime_id'] <= 0) {
            $errors['showtime_id'] = 'Vui lòng chọn suất chiếu.';
        }

        if (empty($this->parseSeatNumbers($data['seat_numbers']))) {
            $errors['seat_numbers'] = 'Vui lòng nhập ít nhất một ghế, ví dụ A1,A2.';
        }

        if (!in_array($data['status'], ['pending', 'paid', 'cancelled'], true)) {
            $errors['status'] = 'Trạng thái không hợp lệ.';
        }

        return $errors;
    }

    private function parseSeatNumbers(string $value): array
    {
        $seatNumbers = preg_split('/[\s,;]+/', strtoupper(trim($value)));
        $seatNumbers = array_filter(array_map('trim', $seatNumbers));
        return array_values(array_unique($seatNumbers));
    }

    private function normalizeFoodQuantities($quantities): array
    {
        if (!is_array($quantities)) {
            return [];
        }

        $normalized = [];
        foreach ($quantities as $variantId => $quantity) {
            $variantId = (int) $variantId;
            $quantity = (int) $quantity;
            if ($variantId > 0 && $quantity > 0) {
                $normalized[$variantId] = min($quantity, 99);
            }
        }
        return $normalized;
    }

    public function myTickets()
    {
        if (!isset($_SESSION['user_id'])) {
            set_flash('danger', 'Vui lòng đăng nhập để sử dụng chức năng này.');
            header("Location: " . BASE_URL . "?action=login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $bookingModel = new BookingModel();
        $myTickets = $bookingModel->getByUserId($userId);

        $title = "Vé của tôi - PETACINEMA";
        $view  = "client/my_tickets";

        require_once PATH_VIEW . 'main.php';
    }
}
