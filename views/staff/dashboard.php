<?php
$staffName = h($_SESSION['user']['fullname'] ?? 'Nhân viên');

$dashPdo = new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8', DB_HOST, DB_PORT, DB_NAME),
    DB_USERNAME,
    DB_PASSWORD,
    DB_OPTIONS
);

function dash_scalar(PDO $pdo, string $sql, array $params = [], $default = 0)
{
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $value = $stmt->fetchColumn();
        return $value !== false && $value !== null ? $value : $default;
    } catch (Throwable $e) {
        return $default;
    }
}

function dash_rows(PDO $pdo, string $sql, array $params = []): array
{
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        return [];
    }
}

function dash_status_meta(string $status): array
{
    return match ($status) {
        'paid'      => ['label' => 'Đã thanh toán', 'badge' => 'badge-paid',      'color' => '#22c55e'],
        'pending'   => ['label' => 'Đang chờ',      'badge' => 'badge-pending',   'color' => '#f97316'],
        'cancelled' => ['label' => 'Đã hủy',        'badge' => 'badge-cancelled', 'color' => '#3b82f6'],
        'refunded'  => ['label' => 'Hoàn tiền',     'badge' => 'badge-refunded',  'color' => '#ef4444'],
        default     => ['label' => ucfirst($status), 'badge' => 'badge-cancelled', 'color' => '#6b7280'],
    };
}

// --- Chỉ tính dữ liệu trong ngày hôm nay ---
$todayStart = date('Y-m-d 00:00:00');
$todayEnd   = date('Y-m-d 23:59:59');
$today      = date('d/m/Y');

$todayRevenue = (float) dash_scalar($dashPdo, "
    SELECT COALESCE(SUM(total_amount), 0)
    FROM bookings
    WHERE status = 'paid'
      AND created_at BETWEEN ? AND ?
", [$todayStart, $todayEnd]);

$todayBookings = (int) dash_scalar($dashPdo, "
    SELECT COUNT(*)
    FROM bookings
    WHERE created_at BETWEEN ? AND ?
", [$todayStart, $todayEnd]);

$todayTickets = (int) dash_scalar($dashPdo, "
    SELECT COUNT(*)
    FROM booking_seats bs
    INNER JOIN bookings b ON b.id = bs.booking_id
    WHERE b.created_at BETWEEN ? AND ?
", [$todayStart, $todayEnd]);

$todayShowtimes = (int) dash_scalar($dashPdo, "
    SELECT COUNT(*)
    FROM showtimes
    WHERE start_time BETWEEN ? AND ?
", [$todayStart, $todayEnd]);

$statCards = [
    ['label' => 'Doanh thu hôm nay', 'value' => number_format($todayRevenue) . 'đ', 'icon' => 'bi-cash-stack',            'theme' => 'red'],
    ['label' => 'Vé đã bán hôm nay', 'value' => number_format($todayTickets),       'icon' => 'bi-ticket-perforated-fill','theme' => 'orange'],
    ['label' => 'Đơn đặt vé hôm nay','value' => number_format($todayBookings),      'icon' => 'bi-receipt',               'theme' => 'blue'],
    ['label' => 'Suất chiếu hôm nay','value' => number_format($todayShowtimes),     'icon' => 'bi-camera-reels',          'theme' => 'purple'],
];

$bookingStatusToday = dash_rows($dashPdo, "
    SELECT status, COUNT(*) AS total
    FROM bookings
    WHERE created_at BETWEEN ? AND ?
    GROUP BY status
", [$todayStart, $todayEnd]);

$statusChart = ['labels' => [], 'values' => [], 'colors' => [], 'legend' => []];
$statusTotal = array_sum(array_column($bookingStatusToday, 'total'));

foreach (['paid', 'pending', 'cancelled', 'refunded'] as $statusKey) {
    $count = 0;
    foreach ($bookingStatusToday as $row) {
        if (($row['status'] ?? '') === $statusKey) {
            $count = (int) $row['total'];
            break;
        }
    }

    $meta = dash_status_meta($statusKey);
    $percent = $statusTotal > 0 ? round(($count / $statusTotal) * 100) : 0;

    $statusChart['labels'][] = $meta['label'];
    $statusChart['values'][] = $count;
    $statusChart['colors'][] = $meta['color'];
    $statusChart['legend'][] = [
        'label'   => $meta['label'],
        'percent' => $percent,
        'count'   => $count,
        'color'   => $meta['color'],
    ];
}

$recentBookings = dash_rows($dashPdo, "
    SELECT
        b.booking_code,
        b.total_amount,
        b.status,
        b.created_at,
        COALESCE(u.fullname, b.customer_name, 'Khách lẻ') AS customer_name,
        COALESCE(m.title, '—') AS movie_title,
        COALESCE(st.start_time, st.show_datetime) AS showtime_at,
        COALESCE(
            GROUP_CONCAT(
                DISTINCT COALESCE(s.seat_label, CONCAT(s.row_label, s.seat_number))
                ORDER BY s.id SEPARATOR ', '
            ),
            '—'
        ) AS seat_labels
    FROM bookings b
    LEFT JOIN users u ON u.id = b.user_id
    LEFT JOIN showtimes st ON st.id = b.showtime_id
    LEFT JOIN movies m ON m.id = st.movie_id
    LEFT JOIN booking_seats bs ON bs.booking_id = b.id
    LEFT JOIN seats s ON s.id = bs.seat_id
    WHERE b.created_at BETWEEN ? AND ?
    GROUP BY b.id
    ORDER BY b.created_at DESC
    LIMIT 10
", [$todayStart, $todayEnd]);
?>

<div class="dashboard-page">

<style>
.dashboard-page {
    --dash-red: #ef4444;
    --dash-orange: #f97316;
    --dash-blue: #3b82f6;
    --dash-purple: #8b5cf6;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.dashboard-header h2 {
    font-weight: 700;
    margin-bottom: 0;
}

.dashboard-date {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: .55rem 1rem;
    color: #374151;
    font-weight: 500;
}

.welcome-banner {
    background: linear-gradient(135deg, #ffffff 0%, #fff7f7 100%);
    border: 1px solid #f3f4f6;
    border-radius: 16px;
    padding: 1.5rem 1.75rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    overflow: hidden;
    position: relative;
}

.welcome-banner h4 {
    font-weight: 700;
    margin-bottom: .35rem;
}

.welcome-banner p {
    color: #6b7280;
    margin-bottom: 0;
}

.welcome-illustration {
    width: 120px;
    height: 90px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    font-size: 2.6rem;
    opacity: .95;
}

.stat-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #f3f4f6;
    padding: 1.25rem;
    height: 100%;
}

.stat-card-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: .5rem;
}

.stat-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.stat-value {
    font-size: 1.65rem;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: .15rem;
}

.stat-label {
    color: #6b7280;
    font-size: .92rem;
    margin-bottom: 0;
}

.theme-red .stat-icon { background: rgba(239,68,68,.12); color: var(--dash-red); }
.theme-orange .stat-icon { background: rgba(249,115,22,.12); color: var(--dash-orange); }
.theme-blue .stat-icon { background: rgba(59,130,246,.12); color: var(--dash-blue); }
.theme-purple .stat-icon { background: rgba(139,92,246,.12); color: var(--dash-purple); }

.chart-card {
    background: #fff;
    border: 1px solid #f3f4f6;
    border-radius: 16px;
    padding: 1.25rem;
    height: 100%;
}

.chart-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.chart-card-header h5 {
    margin: 0;
    font-weight: 700;
}

.donut-layout {
    display: flex;
    align-items: center;
    gap: 20px;
}

.donut-canvas-wrap {
    flex: 0 0 180px;
    width: 180px;
    height: 180px;
    display: flex;
    justify-content: center;
    align-items: center;
}

#statusChart {
    width: 180px !important;
    height: 180px !important;
}

.status-legend {
    flex: 1;
}

.status-legend-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 10px 0;
}

.status-legend-item strong {
    margin-left: auto;
    white-space: nowrap;
}

.status-legend-item span:first-child {
    display: flex;
    align-items: center;
    gap: 10px;
}

.status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
}

.recent-card {
    background: #fff;
    border: 1px solid #f3f4f6;
    border-radius: 16px;
    overflow: hidden;
}

.recent-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.25rem .75rem;
}

.recent-card-header h5 {
    margin: 0;
    font-weight: 700;
}

.recent-table {
    margin-bottom: 0;
}

.recent-table thead th {
    background: #fafafa;
    color: #6b7280;
    font-weight: 600;
    font-size: .82rem;
    border-bottom: 1px solid #f1f5f9;
    white-space: nowrap;
}

.recent-table tbody td {
    vertical-align: middle;
    font-size: .9rem;
    border-bottom: 1px solid #f8fafc;
}

.badge-paid { background: #dcfce7; color: #15803d; }
.badge-pending { background: #ffedd5; color: #c2410c; }
.badge-cancelled { background: #fee2e2; color: #b91c1c; }
.badge-refunded { background: #dbeafe; color: #1d4ed8; }

.status-badge {
    display: inline-block;
    padding: .35rem .75rem;
    border-radius: 999px;
    font-size: .78rem;
    font-weight: 600;
    white-space: nowrap;
}

@media (max-width: 991px) {
    .donut-layout {
        flex-direction: column;
    }

    .welcome-banner {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
}
</style>

<div class="dashboard-header">
    <h2>Dashboard</h2>
    <div class="dashboard-date">
        <i class="bi bi-calendar-event me-1"></i>
        <?= h($today) ?>
    </div>
</div>

<div class="welcome-banner">
    <div>
        <h4>Xin chào, <?= h($staffName) ?>! 👋</h4>
        <p>Đây là tổng quan hoạt động bán vé trong hôm nay.</p>
    </div>
    <div class="welcome-illustration" aria-hidden="true">
        <span>🍿</span>
        <span>🎬</span>
    </div>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($statCards as $card): ?>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card theme-<?= h($card['theme']) ?>">
                <div class="stat-card-top">
                    <div>
                        <div class="stat-value"><?= $card['value'] ?></div>
                        <div class="stat-label"><?= h($card['label']) ?></div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi <?= h($card['icon']) ?>"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="chart-card">
            <div class="chart-card-header">
                <h5>Tỷ lệ đặt vé theo trạng thái (hôm nay)</h5>
            </div>
            <div class="donut-layout">
                <div class="donut-canvas-wrap">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="status-legend">
                    <?php foreach ($statusChart['legend'] as $item): ?>
                        <div class="status-legend-item">
                            <span>
                                <span class="status-dot" style="background: <?= h($item['color']) ?>"></span>
                                <?= h($item['label']) ?>
                            </span>
                            <strong><?= (int) $item['percent'] ?>% (<?= (int) $item['count'] ?> vé)</strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="recent-card">
    <div class="recent-card-header">
        <h5>Đặt vé trong hôm nay</h5>
        <a href="?action=staff_booking_list" class="text-danger text-decoration-none">Xem tất cả</a>
    </div>
    <div class="table-responsive">
        <table class="table recent-table">
            <thead>
                <tr>
                    <th>Mã vé</th>
                    <th>Khách hàng</th>
                    <th>Phim</th>
                    <th>Suất chiếu</th>
                    <th>Ghế</th>
                    <th>Số tiền</th>
                    <th>Trạng thái</th>
                    <th>Thời gian</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentBookings)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Chưa có đơn đặt vé nào hôm nay.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentBookings as $booking): ?>
                        <?php $statusMeta = dash_status_meta($booking['status'] ?? ''); ?>
                        <tr>
                            <td><strong><?= h($booking['booking_code'] ?? '—') ?></strong></td>
                            <td><?= h($booking['customer_name'] ?? '—') ?></td>
                            <td><?= h($booking['movie_title'] ?? '—') ?></td>
                            <td>
                                <?php
                                $showtime = $booking['showtime_at'] ?? null;
                                echo $showtime ? h(date('d/m/Y - H:i', strtotime($showtime))) : '—';
                                ?>
                            </td>
                            <td><?= h($booking['seat_labels'] ?? '—') ?></td>
                            <td><?= number_format((float) ($booking['total_amount'] ?? 0)) ?>đ</td>
                            <td>
                                <span class="status-badge <?= h($statusMeta['badge']) ?>">
                                    <?= h($statusMeta['label']) ?>
                                </span>
                            </td>
                            <td><?= !empty($booking['created_at']) ? h(date('H:i', strtotime($booking['created_at']))) : '—' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(() => {
    const statusLabels = <?= json_encode($statusChart['labels'], JSON_UNESCAPED_UNICODE) ?>;
    const statusValues = <?= json_encode($statusChart['values'], JSON_UNESCAPED_UNICODE) ?>;
    const statusColors = <?= json_encode($statusChart['colors'], JSON_UNESCAPED_UNICODE) ?>;

    const statusCanvas = document.getElementById('statusChart');
    if (statusCanvas) {
        new Chart(statusCanvas, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusValues,
                    backgroundColor: statusColors,
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ` ${ctx.label}: ${ctx.raw} vé`
                        }
                    }
                }
            }
        });
    }
})();
</script>