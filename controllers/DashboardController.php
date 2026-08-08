<?php

class DashboardController
{
    public $dashboardModel;

    public function __construct()
    {
        $this->dashboardModel = new DashboardModel();
    }

    private function parseDateRange(): array
    {
        $fromDate = trim($_GET['from_date'] ?? '');
        $toDate = trim($_GET['to_date'] ?? '');

        if ($fromDate === '' || $toDate === '') {
            return [false, null, null];
        }

        $from = DateTime::createFromFormat('!Y-m-d', $fromDate);
        $to = DateTime::createFromFormat('!Y-m-d', $toDate);

        if (!$from || !$to || $from->format('Y-m-d') !== $fromDate || $to->format('Y-m-d') !== $toDate) {
            return [false, null, null];
        }

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        return [true, $from->format('Y-m-d'), $to->format('Y-m-d')];
    }

    private function monthRange(int $offset = 0): array
    {
        $start = new DateTime('first day of this month 00:00:00');
        if ($offset > 0) {
            $start->modify("-{$offset} months");
        }

        $end = clone $start;
        $end->modify('+1 month -1 second');

        return [
            $start->format('Y-m-d H:i:s'),
            $end->format('Y-m-d H:i:s'),
        ];
    }

    private function growth(int|float $current, int|float $previous): array
    {
        if ((float) $previous === 0.0) {
            if ((float) $current === 0.0) {
                return ['text' => '— Không đổi', 'class' => 'text-muted', 'icon' => 'bi-dash'];
            }

            return ['text' => 'Mới trong tháng này', 'class' => 'text-success', 'icon' => 'bi-arrow-up-right'];
        }

        $percent = (int) round((($current - $previous) / $previous) * 100);

        if ($percent > 0) {
            return ['text' => '+' . $percent . '% so với tháng trước', 'class' => 'text-success', 'icon' => 'bi-arrow-up-right'];
        }

        if ($percent < 0) {
            return ['text' => $percent . '% so với tháng trước', 'class' => 'text-danger', 'icon' => 'bi-arrow-down-right'];
        }

        return ['text' => '— Không đổi', 'class' => 'text-muted', 'icon' => 'bi-dash'];
    }

    private function neutralGrowth(string $text = '— Không áp dụng'): array
    {
        return ['text' => $text, 'class' => 'text-muted', 'icon' => 'bi-dash'];
    }

    private function statusMeta(string $status): array
    {
        return match ($status) {
            'paid'      => ['label' => 'Đã thanh toán', 'badge' => 'badge-paid',      'color' => '#22c55e'],
            'pending'   => ['label' => 'Đang chờ',      'badge' => 'badge-pending',   'color' => '#f97316'],
            'cancelled' => ['label' => 'Đã hủy',        'badge' => 'badge-cancelled', 'color' => '#3b82f6'],
            'refunded'  => ['label' => 'Hoàn tiền',     'badge' => 'badge-refunded',  'color' => '#ef4444'],
            default     => ['label' => ucfirst($status), 'badge' => 'badge-cancelled', 'color' => '#6b7280'],
        };
    }

    public function dashboard()
    {
        [$hasDateFilter, $fromDate, $toDate] = $this->parseDateRange();

        $filterStart = $hasDateFilter ? $fromDate . ' 00:00:00' : null;
        $filterEnd = $hasDateFilter ? $toDate . ' 23:59:59' : null;

        // Các bảng users/movies/rooms/seats/foods hiển thị tổng toàn hệ thống.
        // Các chỉ số giao dịch (booking/payment/revenue) sẽ theo khoảng ngày khi người dùng bật bộ lọc.
        $totalUsers = $this->dashboardModel->countUsers();
        $totalMovies = $this->dashboardModel->countMovies();
        $totalBookings = $this->dashboardModel->countBookings($filterStart, $filterEnd);
        $totalRooms = $this->dashboardModel->countRooms();
        $totalSeats = $this->dashboardModel->countSeats();
        $totalFoods = $this->dashboardModel->countFoods();
        $totalPayments = $this->dashboardModel->countPayments($filterStart, $filterEnd);
        $totalRevenue = $this->dashboardModel->totalRevenue($filterStart, $filterEnd);

        $bookingStatus = $this->dashboardModel->bookingStatus($filterStart, $filterEnd);
        $recentBookings = $this->dashboardModel->recentBookings(5, $filterStart, $filterEnd);
        $topMovies = $this->dashboardModel->topMovies(5);

        if (!$hasDateFilter) {
            $fromDate = $this->dashboardModel->earliestBookingDate() ?: date('Y-m-d');
            $toDate = date('Y-m-d');
        }

        // Biểu đồ doanh thu: theo ngày khi có filter, ngược lại là 7 tháng gần nhất.
        $monthLabels = [];
        $monthValues = [];

        if ($hasDateFilter) {
            $revenueRows = $this->dashboardModel->revenueByDay($filterStart, $filterEnd);
            $revenueMap = [];

            foreach ($revenueRows as $row) {
                $revenueMap[$row['period']] = (float) $row['revenue'];
            }

            $cursor = new DateTime($fromDate);
            $end = new DateTime($toDate);

            while ($cursor <= $end) {
                $key = $cursor->format('Y-m-d');
                $monthLabels[] = $cursor->format('d/m');
                $monthValues[] = $revenueMap[$key] ?? 0;
                $cursor->modify('+1 day');
            }
        } else {
            $revenueRows = $this->dashboardModel->revenueByMonth(7);
            $revenueMap = [];

            foreach ($revenueRows as $row) {
                $revenueMap[$row['period']] = (float) $row['revenue'];
            }

            for ($i = 6; $i >= 0; $i--) {
                $month = new DateTime('first day of this month');
                $month->modify("-{$i} months");
                $key = $month->format('Y-m');
                $monthLabels[] = $month->format('m/Y');
                $monthValues[] = $revenueMap[$key] ?? 0;
            }
        }

        // Chuẩn bị dữ liệu biểu đồ trạng thái booking ngay ở controller.
        $statusChart = [
            'labels' => [],
            'values' => [],
            'colors' => [],
            'legend' => [],
        ];
        $statusTotal = array_sum(array_map(static fn($row) => (int) ($row['total'] ?? 0), $bookingStatus));

        foreach (['paid', 'pending', 'cancelled', 'refunded'] as $statusKey) {
            $count = 0;
            foreach ($bookingStatus as $row) {
                if (($row['status'] ?? '') === $statusKey) {
                    $count = (int) $row['total'];
                    break;
                }
            }

            $meta = $this->statusMeta($statusKey);
            $percent = $statusTotal > 0 ? (int) round(($count / $statusTotal) * 100) : 0;

            $statusChart['labels'][] = $meta['label'];
            $statusChart['values'][] = $count;
            $statusChart['colors'][] = $meta['color'];
            $statusChart['legend'][] = [
                'label' => $meta['label'],
                'percent' => $percent,
                'count' => $count,
                'color' => $meta['color'],
            ];
        }

        // Metadata trạng thái dùng cho bảng booking gần đây ở View.
        $statusMetaMap = [];
        foreach (['paid', 'pending', 'cancelled', 'refunded'] as $statusKey) {
            $statusMetaMap[$statusKey] = $this->statusMeta($statusKey);
        }

        // Growth chỉ áp dụng cho bảng có cột thời gian phù hợp với schema hiện tại.
        [$currentStart, $currentEnd] = $this->monthRange(0);
        [$previousStart, $previousEnd] = $this->monthRange(1);

        $userGrowth = $this->growth(
            $this->dashboardModel->countUsers($currentStart, $currentEnd),
            $this->dashboardModel->countUsers($previousStart, $previousEnd)
        );
        $bookingGrowth = $this->growth(
            $this->dashboardModel->countBookings($currentStart, $currentEnd),
            $this->dashboardModel->countBookings($previousStart, $previousEnd)
        );
        $revenueGrowth = $this->growth(
            $this->dashboardModel->totalRevenue($currentStart, $currentEnd),
            $this->dashboardModel->totalRevenue($previousStart, $previousEnd)
        );
        $paymentGrowth = $this->growth(
            $this->dashboardModel->countPayments($currentStart, $currentEnd),
            $this->dashboardModel->countPayments($previousStart, $previousEnd)
        );

        $movieGrowth = $this->neutralGrowth();
        $foodGrowth = $this->neutralGrowth();
        $roomGrowth = $this->neutralGrowth('— Tổng hiện tại');
        $seatGrowth = $this->neutralGrowth('— Tổng hiện tại');

        $statCards = [
            ['label' => 'Users',    'value' => number_format($totalUsers),         'icon' => 'bi-people-fill',            'theme' => 'green',  'link' => '?action=users_list',    'growth' => $userGrowth],
            ['label' => 'Movies',   'value' => number_format($totalMovies),        'icon' => 'bi-film',                   'theme' => 'blue',   'link' => '?action=movie_list',    'growth' => $movieGrowth],
            ['label' => 'Bookings', 'value' => number_format($totalBookings),      'icon' => 'bi-ticket-perforated-fill', 'theme' => 'orange', 'link' => '?action=booking_list',  'growth' => $bookingGrowth],
            ['label' => 'Revenue',  'value' => number_format($totalRevenue) . 'đ', 'icon' => 'bi-cash-stack',             'theme' => 'red',    'link' => '?action=payment_list',  'growth' => $revenueGrowth],
            ['label' => 'Rooms',    'value' => number_format($totalRooms),         'icon' => 'bi-building',               'theme' => 'purple', 'link' => '?action=rooms',         'growth' => $roomGrowth],
            ['label' => 'Seats',    'value' => number_format($totalSeats),         'icon' => 'bi-grid-3x3-gap-fill',      'theme' => 'teal',   'link' => '?action=seat-types',    'growth' => $seatGrowth],
            ['label' => 'Foods',    'value' => number_format($totalFoods),         'icon' => 'bi-cup-hot-fill',           'theme' => 'pink',   'link' => '?action=food_list',     'growth' => $foodGrowth],
            ['label' => 'Payments', 'value' => number_format($totalPayments),      'icon' => 'bi-credit-card-fill',       'theme' => 'gold',   'link' => '?action=payment_list',  'growth' => $paymentGrowth],
        ];

        $view = 'admin/dashboard';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }
}
