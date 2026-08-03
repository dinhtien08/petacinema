<?php

class StaffShowtimeController
{
    private $showtimeModel;
    private $movieModel;
    private $roomModel;
    private $seatModel;

    public function __construct()
    {
        $this->showtimeModel = new ShowtimeModel();
        $this->movieModel = new MovieModel();
        $this->roomModel = new RoomModel();
        $this->seatModel = new SeatModel();
    }

    // Danh sách
    public function list()
    {
        $title = "Danh sách suất chiếu";

        $keyword = trim($_GET['keyword'] ?? '');
        $movieId = trim($_GET['movie_id'] ?? '');
        $roomId  = trim($_GET['room_id'] ?? '');
        $status  = trim($_GET['status'] ?? '');
        $date    = trim($_GET['date'] ?? '');

        $showtimes = $this->showtimeModel->searchAndFilter($keyword, $movieId, $roomId, $status, $date);
        $movies    = $this->movieModel->getMovieList();
        $rooms     = $this->roomModel->getRoomList();

        $view = "staff/showtime_list";

        require PATH_VIEW . "staff/layout/layout.php";
    }
    public function show()
    {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header('Location: ?action=staff_showtimes');
            exit;
        }

        $id = (int) $_GET['id'];

        $showtime = $this->showtimeModel->getDetail($id);

        if (!$showtime) {
            header('Location: ?action=staff_showtimes');
            exit;
        }

        $title = 'Chi tiết suất chiếu';

        $view = 'staff/showtime_show';

        require PATH_VIEW . 'staff/layout/layout.php';
    }

    public function seats()
    {
        $id = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$id || $id <= 0) {
            $_SESSION['error'] = 'ID suất chiếu không hợp lệ.';
            header('Location: ' . BASE_URL . '?action=staff_showtimes');
            exit;
        }

        $showtime = $this->showtimeModel->getDetail($id);

        if (!$showtime) {
            $_SESSION['error'] = 'Không tìm thấy suất chiếu.';
            header('Location: ' . BASE_URL . '?action=staff_showtimes');
            exit;
        }

        $roomId = (int)$showtime['room_id'];

        $seats = $this->seatModel->getSeatsForShowtime(
            $id,
            $roomId
        );

        $availableCount = 0;
        $bookedCount = 0;
        $maintenanceCount = 0;

        foreach ($seats as $seat) {
            switch ($seat['display_status']) {
                case 'booked':
                    $bookedCount++;
                    break;

                case 'maintenance':
                    $maintenanceCount++;
                    break;

                default:
                    $availableCount++;
                    break;
            }
        }

        $totalSeats = count($seats);

        $bookings = $this->showtimeModel->getBookingsByShowtime($id);
        $validBookingCount = 0;

        foreach ($bookings as $b) {
            if (in_array($b['status'], ['pending', 'paid'], true)) {
                $validBookingCount++;
            }
        }

        $title = 'Sơ đồ ghế theo suất chiếu';
        $view = 'staff/showtime_seats';

        require PATH_VIEW . 'staff/layout/layout.php';
    }
}