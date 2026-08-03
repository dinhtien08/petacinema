<?php

class StaffDashboardController
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

        $view = "staff/dashboard";

        require_once PATH_VIEW . "staff/layout/layout.php";
    }
}