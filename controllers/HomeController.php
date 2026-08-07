<?php

class HomeController
{
    /**
     * Bước 1: Trang chủ - Danh sách phim đang chiếu và sắp chiếu
     */
    public function index() 
    {
        $movieModel = new MovieModel();
        
        $nowShowing = $movieModel->getNowShowingMovies();
        $comingSoon = $movieModel->getComingSoonMovies();

        $title = "Petacinema - Trang chủ rạp chiếu phim";
        $view = 'home';

        require_once PATH_VIEW . 'main.php';
    }

    /**
     * Bước 2: Chi tiết phim
     */
    public function movieDetail()
    {
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            header("Location: " . BASE_URL);
            exit;
        }

        $movieModel = new MovieModel();
        $movie = $movieModel->getById($id);

        if (!$movie) {
            header("Location: " . BASE_URL);
            exit;
        }

        $title = $movie['title'] . " - Chi tiết phim | Petacinema";
        $view = 'movie_detail';

        require_once PATH_VIEW . 'main.php';
    }

    /**
     * Bước 3: Đặt vé - Chọn ngày chiếu và xem các suất chiếu còn hiệu lực
     */
    public function bookingDate()
    {
        $movieId = (int)($_REQUEST['movie_id'] ?? 0);

        if ($movieId <= 0) {
            header("Location: " . BASE_URL);
            exit;
        }

        $movieModel = new MovieModel();
        $movie = $movieModel->getById($movieId);

        if (!$movie) {
            header("Location: " . BASE_URL);
            exit;
        }

        // Dọn booking pending quá 5 phút để giải phóng ghế trước khi render sơ đồ.
        try {
            (new BookingModel())->expirePendingBookings();
        } catch (Throwable $e) {
            // Không chặn trang khách nếu tác vụ dọn dẹp tạm thời lỗi.
        }

        $showtimeModel = new ShowtimeModel();
        
        // Lấy danh sách các ngày có suất chiếu còn hiệu lực
        $availableDates = $showtimeModel->getAvailableDatesByMovie($movieId);

        // Ngày được người dùng chọn (từ GET hoặc POST form)
        $selectedDate = trim($_REQUEST['date'] ?? '');

        // Nếu người dùng chưa chọn ngày, tự động chọn ngày chiếu đầu tiên có sẵn (nếu có)
        if (empty($selectedDate) && !empty($availableDates)) {
            $selectedDate = $availableDates[0];
        }

        $showtimes = [];
        if (!empty($selectedDate)) {
            $showtimes = $showtimeModel->getValidShowtimesByMovieAndDate($movieId, $selectedDate);
        }

        $title = "Đặt vé: " . $movie['title'] . " | Petacinema";
        $view = 'booking_date';

        require_once PATH_VIEW . 'main.php';
    }
}
