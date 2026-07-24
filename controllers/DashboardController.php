<?php

class DashboardController
{
    public $dashboardModel;

    public function __construct()
    {
        $this->dashboardModel = new DashboardModel();
    }

    public function dashboard()
    {
        $totalUsers = $this->dashboardModel->countUsers();

        $totalMovies = $this->dashboardModel->countMovies();

        $totalBookings = $this->dashboardModel->countBookings();

        $totalRooms = $this->dashboardModel->countRooms();

        $totalFoods = $this->dashboardModel->countFoods();

        $totalRevenue = $this->dashboardModel->totalRevenue();

        $recentBookings = $this->dashboardModel->recentBookings();

        $topMovies = $this->dashboardModel->topMovies();

        $bookingStatus = $this->dashboardModel->bookingStatus();

        $view = "admin/dashboard";

        require_once PATH_VIEW . "admin/layout/layout.php";
    }
}