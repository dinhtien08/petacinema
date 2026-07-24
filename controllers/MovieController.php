<?php
class MovieController
{
    public function index()
    {
        $movieModel = new MovieModel();
        $movies = $movieModel->getAll();
        // debug($movies);
        $view = PATH_VIEW . 'admin/movie/list.php';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }
}
