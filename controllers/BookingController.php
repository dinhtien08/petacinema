<?php

class BookingController
{
    // GET ?action=booking_list
    public function list()
    {
        $bookingModel = new BookingModel();
        $bookings = $bookingModel->getAll();
        $flash = get_flash();
        $view = 'admin/booking/list';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    // GET ?action=booking_add
    public function add()
    {
        $userModel = new UserModel();
        $bookingModel = new BookingModel();
        $users = $userModel->getAll();
        $showtimes = $bookingModel->getShowtimeOptions();
        $errors = [];
        $old = [
            'status' => 'pending',
        ];
        $view = 'admin/booking/add';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    public function addPost()
    {
        $old = [
            'user_id'      => trim($_POST['user_id'] ?? ''),
            'showtime_id'  => trim($_POST['showtime_id'] ?? ''),
            'total_amount' => trim($_POST['total_amount'] ?? ''),
            'status'       => $_POST['status'] ?? 'pending',
        ];
        $errors = $this->validate($old);
        if (!empty($errors)) {
            $userModel = new UserModel();
            $bookingModel = new BookingModel();
            $users = $userModel->getAll();
            $showtimes = $bookingModel->getShowtimeOptions();
            $view = 'admin/booking/add';
            require_once PATH_VIEW . 'admin/layout/layout.php';
            return;
        }
        $bookingModel = new BookingModel();
        $bookingModel->addBooking([
            'booking_code' => $bookingModel->generateBookingCode(),
            'user_id'      => $old['user_id'],
            'showtime_id'  => $old['showtime_id'],
            'total_amount' => $old['total_amount'],
            'status'       => $old['status'],
        ]);

        set_flash('success', 'Thêm booking thành công.');
        header('Location: ?action=booking_list');
        exit;
    }

    // GET ?action=booking_edit&id=
    public function edit()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $bookingModel = new BookingModel();
        $booking = $bookingModel->getById($id);
        if (!$booking) {
            set_flash('error', 'Không tìm thấy booking.');
            header('Location: ?action=booking_list');
            exit;
        }
        $userModel = new UserModel();
        $users = $userModel->getAll();
        $showtimes = $bookingModel->getShowtimeOptions();
        $errors = [];
        $old = $booking;
        $view = 'admin/booking/edit';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    // POST ?action=booking_editPost&id=
    public function editPost()
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $bookingModel = new BookingModel();
        $booking = $bookingModel->getById($id);
        if (!$booking) {
            set_flash('error', 'Không tìm thấy booking.');
            header('Location: ?action=booking_list');
            exit;
        }
        $old = [
            'id'           => $id,
            'booking_code' => $booking['booking_code'],
            'user_id'      => trim($_POST['user_id'] ?? ''),
            'showtime_id'  => trim($_POST['showtime_id'] ?? ''),
            'total_amount' => trim($_POST['total_amount'] ?? ''),
            'status'       => $_POST['status'] ?? 'pending',
        ];
        $errors = $this->validate($old);
        if (!empty($errors)) {
            $userModel = new UserModel();
            $users = $userModel->getAll();
            $showtimes = $bookingModel->getShowtimeOptions();
            $view = 'admin/booking/edit';
            require_once PATH_VIEW . 'admin/layout/layout.php';
            return;
        }
        $bookingModel->editBooking($id, $old);
        set_flash('success', 'Cập nhật booking thành công.');
        header('Location: ?action=booking_list');
        exit;
    }

    // POST ?action=booking_delete&id=
    public function delete()
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $bookingModel = new BookingModel();
        $bookingModel->deleteBooking($id);

        set_flash('success', 'Xóa booking thành công.');
        header('Location: ?action=booking_list');
        exit;
    }

    private function validate($data)
    {
        $errors = [];
        if ($data['user_id'] === '' || !ctype_digit((string) $data['user_id']) || (int) $data['user_id'] <= 0) {
            $errors['user_id'] = 'Vui lòng chọn khách hàng.';
        }
        if ($data['showtime_id'] === '' || !ctype_digit((string) $data['showtime_id']) || (int) $data['showtime_id'] <= 0) {
            $errors['showtime_id'] = 'Vui lòng chọn suất chiếu.';
        }
        if ($data['total_amount'] === '' || !is_numeric($data['total_amount']) || (float) $data['total_amount'] < 0) {
            $errors['total_amount'] = 'Tổng tiền phải là số và không được âm.';
        }
        if (!in_array($data['status'], ['pending', 'paid', 'cancelled'], true)) {
            $errors['status'] = 'Trạng thái không hợp lệ.';
        }
        return $errors;
    }
}
