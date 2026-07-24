<?php
class MovieController
{
    public function index()
    {
        $movieModel = new MovieModel();
        $movies = $movieModel->getAll();
        // debug($movies);
        $view = 'admin/movie/list';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }
}
