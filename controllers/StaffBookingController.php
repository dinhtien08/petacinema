<?php

class StaffBookingController
{
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
        $bookingModel = new BookingModel();
        $booking = $bookingModel->getById($id);

        if (!$booking) {
            set_flash('error', 'Không tìm thấy booking.');
            header('Location: ?action=staff_booking_list');
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
}
