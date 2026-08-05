<?php

class HomeController
{
    protected $movieModel;
    protected $roomModel;

    public function __construct()
    {
        $this->movieModel = new MovieModel();
        $this->roomModel  = new RoomModel();
    }

    public function index() 
    {
        $nowShowing   = $this->movieModel->getNowShowingMovies();
        $comingSoon   = $this->movieModel->getComingSoonMovies();
        $featured     = $this->movieModel->getFeaturedMovies();
        $rooms        = $this->roomModel->getAll();

        $title = "PETACINEMA - Trải nghiệm điện ảnh đỉnh cao";
        $view  = "client/home";

        require_once PATH_VIEW . 'main.php';
    }

    public function news()
    {
        $title = "Tin Tức Điện Ảnh - PETACINEMA";
        $view  = "client/news";

        require_once PATH_VIEW . 'main.php';
    }

    public function promotions()
    {
        $title = "Ưu Đãi & Khuyến Mãi - PETACINEMA";
        $view  = "client/promotions";

        require_once PATH_VIEW . 'main.php';
    }
}