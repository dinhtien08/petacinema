<?php
class MovieController
{
    public function list()
    {
        $movieModel = new MovieModel();
        $movies = $movieModel->getAll();
        // debug($movies);
        $view = 'admin/movie/list';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }
    public function delete()
    {
        if (!isset($_GET['id'])) {
            header('Location: ' . BASE_URL . '?action=movie_list');
            exit;
        }

        $id = (int)$_GET['id'];

        $movieModel = new MovieModel();

        $movieModel->delete($id);

        header('Location: ' . BASE_URL . '?action=movie_list');
        exit;
    }
    public function create()
    {
        $view = 'admin/movie/create';
        require PATH_VIEW . 'admin/layout/layout.php';
    }
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=movie_create');
            exit;
        }

        $data = [
            'title'        => trim($_POST['title']),
            'genres'       => trim($_POST['genres']),
            'duration'     => (int) $_POST['duration'],
            'description'  => trim($_POST['description']),
            'trailer'      => trim($_POST['trailer']),
            'release_date' => $_POST['release_date'],
            'language'     => trim($_POST['language']),
            'director'     => trim($_POST['director']),
            'actors'       => trim($_POST['actors']),
            'age_rating'   => $_POST['age_rating'],
            'status'       => $_POST['status'],
        ];

        $errors = $this->validate($data);

        $errors = array_merge(
            $errors,
            $this->validatePoster($_FILES['poster'])
        );

        if (!empty($errors)) {

            $view = 'admin/movie/create';

            require PATH_VIEW . 'admin/layout/layout.php';

            return;
        }

        $data['poster'] = upload_file('movie', $_FILES['poster']);

        $movieModel = new MovieModel();

        $movieModel->insert($data);

        header('Location: ' . BASE_URL . '?action=movie_list');

        exit;
    }
    public function edit()
    {
        if (empty($_GET['id'])) {
            header('Location: ' . BASE_URL . '?action=movie_list');
            exit;
        }

        $id = (int) $_GET['id'];

        $movieModel = new MovieModel();

        $data = $movieModel->getById($id);

        if (!$data) {
            header('Location: ' . BASE_URL . '?action=movie_list');
            exit;
        }

        $view = 'admin/movie/edit';

        require PATH_VIEW . 'admin/layout/layout.php';
    }
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=movie_list');
            exit;
        }

        $id = (int)$_GET['id'];

        $movieModel = new MovieModel();

        // Kiểm tra phim có tồn tại không
        $movie = $movieModel->getById($id);

        if (!$movie) {
            header('Location: ' . BASE_URL . '?action=movie_list');
            exit;
        }

        // Lấy dữ liệu từ form
        $data = [
            'id'           => $id,
            'title'        => trim($_POST['title']),
            'genres'       => trim($_POST['genres']),
            'duration'     => (int)$_POST['duration'],
            'description'  => trim($_POST['description']),
            'trailer'      => trim($_POST['trailer']),
            'release_date' => $_POST['release_date'],
            'language'     => trim($_POST['language']),
            'director'     => trim($_POST['director']),
            'actors'       => trim($_POST['actors']),
            'age_rating'   => $_POST['age_rating'],
            'status'       => $_POST['status'],
            'poster'       => $movie['poster'] // Giữ ảnh cũ mặc định
        ];

        // Validate
        $errors = $this->validate($data);

        // Nếu chọn poster mới
        if (!empty($_FILES['poster']['name'])) {

            $errors = array_merge(
                $errors,
                $this->validatePoster($_FILES['poster'])
            );

            if (empty($errors)) {

                // Xóa poster cũ
                if (!empty($movie['poster'])) {

                    $oldFile = PATH_ASSETS_UPLOADS . $movie['poster'];

                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                // Upload poster mới
                $data['poster'] = upload_file('movie', $_FILES['poster']);
            }
        }

        // Có lỗi -> quay lại edit
        if (!empty($errors)) {

            $view = PATH_VIEW . 'admin/movie/edit.php';

            require PATH_VIEW . 'admin/layout/layout.php';

            return;
        }

        // Cập nhật database
        $movieModel->update($data);

        header('Location: ' . BASE_URL . '?action=movie_list');

        exit;
    }
    public function show()
    {
        if (empty($_GET['id'])) {
            header('Location: ' . BASE_URL . '?action=movie_list');
            exit;
        }

        $id = (int) $_GET['id'];

        $movieModel = new MovieModel();

        $movie = $movieModel->getById($id);

        if (!$movie) {
            header('Location: ' . BASE_URL . '?action=movie_list');
            exit;
        }

        $view = 'admin/movie/show';

        require PATH_VIEW . 'admin/layout/layout.php';
    }
    private function validate($data)
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors['title'] = 'Tên phim không được để trống.';
        }

        if (empty($data['genres'])) {
            $errors['genres'] = 'Vui lòng nhập thể loại.';
        }

        if (empty($data['duration'])) {
            $errors['duration'] = 'Vui lòng nhập thời lượng.';
        } elseif ($data['duration'] <= 0) {
            $errors['duration'] = 'Thời lượng phải lớn hơn 0.';
        }

        if (empty($data['description'])) {
            $errors['description'] = 'Vui lòng nhập mô tả.';
        }

        if (empty($data['release_date'])) {
            $errors['release_date'] = 'Vui lòng chọn ngày khởi chiếu.';
        }

        if (empty($data['language'])) {
            $errors['language'] = 'Vui lòng nhập ngôn ngữ.';
        }

        if (empty($data['director'])) {
            $errors['director'] = 'Vui lòng nhập đạo diễn.';
        }

        if (empty($data['actors'])) {
            $errors['actors'] = 'Vui lòng nhập diễn viên.';
        }

        $validAgeRatings = ['P', 'K', 'T13', 'T16', 'T18', 'C'];

        if (empty($data['age_rating'])) {
            $errors['age_rating'] = 'Vui lòng chọn giới hạn độ tuổi.';
        } elseif (!in_array($data['age_rating'], $validAgeRatings, true)) {
            $errors['age_rating'] = 'Giới hạn độ tuổi không hợp lệ.';
        }

        $validStatus = ['coming_soon', 'now_showing', 'ended'];

        if (!in_array($data['status'], $validStatus, true)) {
            $errors['status'] = 'Trạng thái không hợp lệ.';
        }

        return $errors;
    }
    private function validatePoster($file)
    {
        $errors = [];

        if (empty($file['name'])) {

            $errors['poster'] = 'Vui lòng chọn poster.';

            return $errors;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $allow = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $allow)) {

            $errors['poster'] = 'Poster phải là jpg, jpeg, png hoặc webp.';
        }

        if ($file['size'] > 2 * 1024 * 1024) {

            $errors['poster'] = 'Poster không được vượt quá 2MB.';
        }

        return $errors;
    }

}
