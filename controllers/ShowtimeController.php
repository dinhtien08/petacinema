<?php

class ShowtimeController
{
    private $showtimeModel;
    private $movieModel;
    private $roomModel;

    public function __construct()
    {
        $this->showtimeModel = new ShowtimeModel();
        $this->movieModel = new MovieModel();
        $this->roomModel = new RoomModel();
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

        $view = "admin/showtime/list";

        require PATH_VIEW . "admin/layout/layout.php";
    }

    // Hiển thị form thêm
    public function create()
    {
        $title = "Thêm suất chiếu";

        $movies = $this->movieModel->getMovieList();

        $rooms = $this->roomModel->getRoomList();

        $errors = [];

        $old = [];

        $view = "admin/showtime/create";

        require PATH_VIEW . "admin/layout/layout.php";
    }

    // Lưu
    public function store()
    {
        $title = "Thêm suất chiếu";

        $movies = $this->movieModel->getMovieList();

        $rooms = $this->roomModel->getRoomList();

        $errors = [];

        $old = $_POST;

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {

            $view = "admin/showtime/create";

            require PATH_VIEW . "admin/layout/layout.php";

            return;
        }

        $movieId = (int)($_POST['movie_id'] ?? 0);
        $roomId = (int)($_POST['room_id'] ?? 0);
        $startTime = trim($_POST['start_time'] ?? '');
        $basePrice = (float)($_POST['base_price'] ?? 0);

        // Validate
        if ($movieId <= 0) {
            $errors['movie_id'] = "Vui lòng chọn phim.";
        }

        if ($roomId <= 0) {
            $errors['room_id'] = "Vui lòng chọn phòng.";
        }

        if ($startTime == '') {
            $errors['start_time'] = "Vui lòng chọn thời gian bắt đầu.";
        }

        if ($basePrice <= 0) {
            $errors['base_price'] = "Giá cơ sở phải lớn hơn 0.";
        }

        if (!empty($errors)) {

            $view = "admin/showtime/create";

            require PATH_VIEW . "admin/layout/layout.php";

            return;
        }

        // Lấy phim
        $movie = $this->movieModel->findById($movieId);

        if (!$movie) {

            $errors['movie_id'] = "Phim không tồn tại.";

            $view = "admin/showtime/create";

            require PATH_VIEW . "admin/layout/layout.php";

            return;
        }

        if ($movie['status'] == 'ended') {

            $errors['movie_id'] = "Phim đã kết thúc.";

            $view = "admin/showtime/create";

            require PATH_VIEW . "admin/layout/layout.php";

            return;
        }

        // Kiểm tra thời gian
        $start = new DateTime($startTime);

        if ($start < new DateTime()) {

            $errors['start_time'] = "Không thể tạo suất chiếu trong quá khứ.";

            $view = "admin/showtime/create";

            require PATH_VIEW . "admin/layout/layout.php";

            return;
        }

        // Tính giờ kết thúc
        $end = clone $start;
        $end->modify('+' . ($movie['duration'] + SHOWTIME_CLEANING_TIME) . ' minutes');

        $startDB = $start->format("Y-m-d H:i:s");
        $endDB = $end->format("Y-m-d H:i:s");

        // Kiểm tra trùng lịch
        if ($this->showtimeModel->checkConflict($roomId, $startDB, $endDB)) {

            $errors['start_time'] = "Phòng đã có suất chiếu trong khoảng thời gian này.";

            $view = "admin/showtime/create";

            require PATH_VIEW . "admin/layout/layout.php";

            return;
        }

        // Lưu
        $data = [
            'movie_id'   => $movieId,
            'room_id'    => $roomId,
            'start_time' => $startDB,
            'end_time'   => $endDB,
            'base_price' => $basePrice
        ];

        if ($this->showtimeModel->insert($data)) {

            header("Location: ?action=showtimes");
            exit;
        }

        $errors['general'] = "Có lỗi xảy ra khi thêm suất chiếu.";

        $view = "admin/showtime/create";

        require PATH_VIEW . "admin/layout/layout.php";
    }
    public function show()
    {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header('Location: ?action=showtimes');
            exit;
        }

        $id = (int) $_GET['id'];

        $showtime = $this->showtimeModel->getDetail($id);

        if (!$showtime) {
            header('Location: ?action=showtimes');
            exit;
        }

        $title = 'Chi tiết suất chiếu';

        $view = 'admin/showtime/show';

        require PATH_VIEW . 'admin/layout/layout.php';
    }
    public function edit()
    {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header('Location: ?action=showtimes');
            exit;
        }

        $id = (int)$_GET['id'];

        $showtime = $this->showtimeModel->findById($id);

        if (!$showtime) {
            header('Location: ?action=showtimes');
            exit;
        }

        $movies = $this->movieModel->getMovieList();
        $rooms = $this->roomModel->getRoomList();

        $errors = [];
        $old = $showtime;

        $title = 'Cập nhật suất chiếu';
        $view = 'admin/showtime/edit';

        require PATH_VIEW . 'admin/layout/layout.php';
    }
    public function update()
    {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header('Location: ?action=showtimes');
            exit;
        }

        $id = (int) $_GET['id'];

        $showtime = $this->showtimeModel->findById($id);

        if (!$showtime) {
            header('Location: ?action=showtimes');
            exit;
        }

        $movies = $this->movieModel->getMovieList();
        $rooms = $this->roomModel->getRoomList();

        $title = 'Cập nhật suất chiếu';

        $errors = [];
        $old = $_POST;

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {

            $old = $showtime;

            $view = 'admin/showtime/edit';
            require PATH_VIEW . 'admin/layout/layout.php';
            return;
        }

        //=========================
        // Lấy dữ liệu
        //=========================

        $movieId = (int) ($_POST['movie_id'] ?? 0);
        $roomId = (int) ($_POST['room_id'] ?? 0);
        $startTime = trim($_POST['start_time'] ?? '');
        $basePrice = (float) ($_POST['base_price'] ?? 0);

        //=========================
        // Validate
        //=========================

        if ($movieId <= 0) {
            $errors['movie_id'] = 'Vui lòng chọn phim.';
        }

        if ($roomId <= 0) {
            $errors['room_id'] = 'Vui lòng chọn phòng.';
        }

        if (empty($startTime)) {
            $errors['start_time'] = 'Vui lòng chọn thời gian bắt đầu.';
        }

        if ($basePrice <= 0) {
            $errors['base_price'] = 'Giá cơ sở phải lớn hơn 0.';
        }

        if (!empty($errors)) {

            $view = 'admin/showtime/edit';
            require PATH_VIEW . 'admin/layout/layout.php';
            return;
        }

        //=========================
        // Kiểm tra phim
        //=========================

        $movie = $this->movieModel->findById($movieId);

        if (!$movie) {

            $errors['movie_id'] = 'Phim không tồn tại.';

            $view = 'admin/showtime/edit';
            require PATH_VIEW . 'admin/layout/layout.php';
            return;
        }

        if ($movie['status'] == 'ended') {

            $errors['movie_id'] = 'Phim đã kết thúc.';

            $view = 'admin/showtime/edit';
            require PATH_VIEW . 'admin/layout/layout.php';
            return;
        }

        //=========================
        // Tính thời gian kết thúc
        //=========================

        $start = new DateTime($startTime);

        if ($start < new DateTime()) {

            $errors['start_time'] = 'Không thể chọn thời gian trong quá khứ.';

            $view = 'admin/showtime/edit';
            require PATH_VIEW . 'admin/layout/layout.php';
            return;
        }

        $end = clone $start;
        $end->modify('+' . ($movie['duration'] + SHOWTIME_CLEANING_TIME) . ' minutes');

        $startDB = $start->format('Y-m-d H:i:s');
        $endDB = $end->format('Y-m-d H:i:s');

        //=========================
        // Kiểm tra trùng lịch
        //=========================

        $conflict = $this->showtimeModel->checkConflictExcept(
            $id,
            $roomId,
            $startDB,
            $endDB
        );

        if ($conflict) {

            $errors['start_time'] = 'Phòng đã có suất chiếu trong khoảng thời gian này.';

            $view = 'admin/showtime/edit';
            require PATH_VIEW . 'admin/layout/layout.php';
            return;
        }

        //=========================
        // Cập nhật
        //=========================

        $data = [
            'movie_id'   => $movieId,
            'room_id'    => $roomId,
            'start_time' => $startDB,
            'end_time'   => $endDB,
            'base_price' => $basePrice
        ];

        if ($this->showtimeModel->update($id, $data)) {

            header('Location: ?action=showtimes');
            exit;
        }

        $errors['general'] = 'Cập nhật thất bại.';

        $view = 'admin/showtime/edit';
        require PATH_VIEW . 'admin/layout/layout.php';
    }
    public function delete()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id || $id <= 0) {
            header('Location: ?action=showtimes&error=invalid_id');
            exit;
        }

        $showtime = $this->showtimeModel->findById($id);

        if (!$showtime) {
            header('Location: ?action=showtimes&error=not_found');
            exit;
        }

        if ($this->showtimeModel->hasBooking($id)) {
            header('Location: ?action=showtimes&error=has_booking');
            exit;
        }

        try {
            $deleted = $this->showtimeModel->delete($id);

            if (!$deleted) {
                header('Location: ?action=showtimes&error=delete_failed');
                exit;
            }

            header('Location: ?action=showtimes&success=deleted');
            exit;

        } catch (PDOException $e) {
            header('Location: ?action=showtimes&error=delete_failed');
            exit;
        }
    }
}