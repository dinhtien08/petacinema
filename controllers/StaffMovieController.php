<?php
class StaffMovieController
{
    public function list()
    {
        $keyword   = trim($_GET['keyword'] ?? '');
        $status    = trim($_GET['status'] ?? '');
        $genre     = trim($_GET['genre'] ?? '');
        $ageRating = trim($_GET['age_rating'] ?? '');
        $sort      = trim($_GET['sort'] ?? 'status');

        $movieModel = new MovieModel();

        $movies = $movieModel->searchAndFilter(
            $keyword,
            $status,
            $genre,
            $ageRating,
            $sort
        );

        $view = 'staff/list_movie';

        require_once PATH_VIEW . 'staff/layout/layout.php';
    }
    public function show()
    {
        if (empty($_GET['id'])) {
            header('Location: ' . BASE_URL . '?action=staff_movie_list');
            exit;
        }

        $id = (int) $_GET['id'];

        $movieModel = new MovieModel();

        $movie = $movieModel->getById($id);

        if (!$movie) {
            header('Location: ' . BASE_URL . '?action=staff_movie_list');
            exit;
        }

        $view = 'staff/movie_show';

        require PATH_VIEW . 'staff/layout/layout.php';
    }
}
