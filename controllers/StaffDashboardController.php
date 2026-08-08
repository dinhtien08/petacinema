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
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        $today = date('d/m/Y');

        $todayRevenue = $this->dashboardModel->totalRevenue($todayStart, $todayEnd);
        $todayBookings = $this->dashboardModel->countBookings($todayStart, $todayEnd);
        $todayTickets = $this->dashboardModel->countTickets($todayStart, $todayEnd);
        $todayShowtimes = $this->dashboardModel->countShowtimes($todayStart, $todayEnd);
        $bookingStatusToday = $this->dashboardModel->bookingStatus($todayStart, $todayEnd);
        $recentBookings = $this->dashboardModel->recentBookings(10, $todayStart, $todayEnd);

        $view = 'staff/dashboard';
        require_once PATH_VIEW . 'staff/layout/layout.php';
    }
}
