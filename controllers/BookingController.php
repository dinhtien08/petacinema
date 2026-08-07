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


    /**
     * Vé của tôi: chỉ hiển thị các booking đã thanh toán của khách đang đăng nhập.
     */
    public function myTickets()
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $role = $_SESSION['role'] ?? ($_SESSION['user']['role'] ?? 'user');

        if ($userId <= 0) {
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        if (!in_array($role, ['user', 'client'], true)) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $bookingModel = new BookingModel();
        $bookingModel->expirePendingBookings();
        $bookings = $bookingModel->getPaidBookingsByUser($userId);

        foreach ($bookings as &$booking) {
            $booking['foods'] = $bookingModel->getBookingFoodOrders((int) $booking['id']);
        }
        unset($booking);

        $title = 'Vé của tôi | Petacinema';
        $view = 'my_tickets';
        require_once PATH_VIEW . 'main.php';
    }


    /**
     * Client checkout: tạo booking pending + tickets để giữ ghế, sau đó sang VNPAY Sandbox.
     */
    public function checkout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }

        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $movieId = (int) ($_POST['movie_id'] ?? 0);
        $showtimeId = (int) ($_POST['showtime_id'] ?? 0);
        $selectedDate = trim((string) ($_POST['date'] ?? ''));
        $seatNumbersRaw = strtoupper(trim((string) ($_POST['seat_numbers'] ?? '')));
        $seatNumbers = $this->parseSeatNumbers($seatNumbersRaw);
        $foodQuantities = $this->normalizeFoodQuantities($_POST['food_quantities'] ?? []);

        $backParams = [
            'action' => 'booking_date',
            'movie_id' => $movieId,
            'date' => $selectedDate,
            'showtime_id' => $showtimeId,
            'selected_seats' => implode(',', $seatNumbers),
        ];
        $backUrl = BASE_URL . '?' . http_build_query($backParams);

        if ($userId <= 0) {
            $_SESSION['booking_return_url'] = $backUrl;
            set_flash('error', 'Vui lòng đăng nhập để tiếp tục thanh toán.');
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        if ($movieId <= 0 || $showtimeId <= 0 || empty($seatNumbers)) {
            set_flash('error', 'Thông tin đặt vé không hợp lệ. Vui lòng chọn lại suất và ghế.');
            header('Location: ' . BASE_URL);
            exit;
        }

        if (!VnpayService::isConfigured()) {
            set_flash('error', 'VNPAY Sandbox chưa được cấu hình. Hãy điền VNPAY_TMN_CODE và VNPAY_HASH_SECRET trong configs/env.php.');
            header('Location: ' . $backUrl);
            exit;
        }

        $bookingModel = new BookingModel();

        try {
            $booking = $bookingModel->createPendingCheckout([
                'user_id' => $userId,
                'showtime_id' => $showtimeId,
                'seat_numbers' => $seatNumbers,
                'food_quantities' => $foodQuantities,
            ]);

            // Từ thời điểm booking pending được tạo, tickets đã giữ ghế trong tối đa 5 phút.
            $_SESSION['active_payment_booking_code'] = $booking['booking_code'];

            $paymentUrl = VnpayService::createPaymentUrl($booking);
            header('Location: ' . $paymentUrl);
            exit;
        } catch (InvalidArgumentException $e) {
            set_flash('error', $e->getMessage());
            header('Location: ' . $backUrl);
            exit;
        } catch (Throwable $e) {
            set_flash('error', 'Không thể tạo booking thanh toán: ' . $e->getMessage());
            header('Location: ' . $backUrl);
            exit;
        }
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
}
