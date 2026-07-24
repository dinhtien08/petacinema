<?php
$adminName = h($_SESSION['user']['fullname'] ?? 'Administrator');

$dashDb = new DashboardModel();

$totalUsers     = $totalUsers     ?? $dashDb->countUsers();
$totalMovies    = $totalMovies    ?? $dashDb->countMovies();
$totalBookings  = $totalBookings  ?? $dashDb->countBookings();
$totalRooms     = $totalRooms     ?? $dashDb->countRooms();
$totalFoods     = $totalFoods     ?? $dashDb->countFoods();
$totalRevenue   = $totalRevenue   ?? $dashDb->totalRevenue();
$bookingStatus  = $bookingStatus  ?? $dashDb->bookingStatus();

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

function dash_growth(int|float $current, int|float $previous): array
{
    if ($previous == 0) {
        if ($current == 0) {
            return ['text' => '— Không đổi', 'class' => 'text-muted', 'icon' => 'bi-dash'];
        }
        return ['text' => 'Mới trong tháng này', 'class' => 'text-success', 'icon' => 'bi-arrow-up-right'];
    }

    $percent = round((($current - $previous) / $previous) * 100);

    if ($percent > 0) {
        return ['text' => '+' . $percent . '% so với tháng trước', 'class' => 'text-success', 'icon' => 'bi-arrow-up-right'];
    }
    if ($percent < 0) {
        return ['text' => $percent . '% so với tháng trước', 'class' => 'text-danger', 'icon' => 'bi-arrow-down-right'];
    }

    return ['text' => '— Không đổi', 'class' => 'text-muted', 'icon' => 'bi-dash'];
}

function dash_month_count(PDO $pdo, string $table, int $offset = 0): int
{
    return (int) dash_scalar($pdo, "
        SELECT COUNT(*)
        FROM {$table}
        WHERE created_at >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL {$offset} MONTH), '%Y-%m-01')
          AND created_at <  DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL " . ($offset - 1) . " MONTH), '%Y-%m-01')
    ");
}

function dash_month_revenue(PDO $pdo, int $offset = 0): float
{
    return (float) dash_scalar($pdo, "
        SELECT COALESCE(SUM(total_amount), 0)
        FROM bookings
        WHERE status = 'paid'
          AND created_at >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL {$offset} MONTH), '%Y-%m-01')
          AND created_at <  DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL " . ($offset - 1) . " MONTH), '%Y-%m-01')
    ");
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

$totalSeats = (int) dash_scalar($dashPdo, 'SELECT COUNT(*) FROM seats');
$totalPayments = (int) dash_scalar(
    $dashPdo,
    'SELECT COUNT(*) FROM payments',
    [],
    (int) dash_scalar($dashPdo, "SELECT COUNT(*) FROM bookings WHERE status = 'paid'")
);

$userGrowth     = dash_growth(dash_month_count($dashPdo, 'users'), dash_month_count($dashPdo, 'users', 1));
$movieGrowth    = dash_growth(dash_month_count($dashPdo, 'movies'), dash_month_count($dashPdo, 'movies', 1));
$bookingGrowth  = dash_growth(dash_month_count($dashPdo, 'bookings'), dash_month_count($dashPdo, 'bookings', 1));
$revenueGrowth  = dash_growth(dash_month_revenue($dashPdo), dash_month_revenue($dashPdo, 1));
$foodGrowth     = dash_growth(dash_month_count($dashPdo, 'foods'), dash_month_count($dashPdo, 'foods', 1));
$paymentGrowth  = dash_growth(
    (int) dash_scalar($dashPdo, "SELECT COUNT(*) FROM payments WHERE created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')"),
    (int) dash_scalar($dashPdo, "
        SELECT COUNT(*)
        FROM payments
        WHERE created_at >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01')
          AND created_at < DATE_FORMAT(CURDATE(), '%Y-%m-01')
    ", [], dash_month_count($dashPdo, 'bookings'))
);

$monthlyRevenue = dash_rows($dashPdo, "
    SELECT
        YEAR(created_at) AS y,
        MONTH(created_at) AS m,
        COALESCE(SUM(total_amount), 0) AS revenue
    FROM bookings
    WHERE status = 'paid'
      AND created_at IS NOT NULL
    GROUP BY YEAR(created_at), MONTH(created_at)
    ORDER BY YEAR(created_at), MONTH(created_at)
");

$monthLabels = [];
$monthValues = [];
$monthMap = [];

foreach ($monthlyRevenue as $row) {
    $monthMap[$row['y'] . '-' . str_pad((string) $row['m'], 2, '0', STR_PAD_LEFT)] = (float) $row['revenue'];
}

for ($i = 6; $i >= 0; $i--) {
    $date = new DateTime('first day of this month');
    $date->modify("-{$i} months");
    $key = $date->format('Y-m');
    $monthLabels[] = $date->format('m/Y');
    $monthValues[] = $monthMap[$key] ?? 0;
}

if (array_sum($monthValues) == 0 && $totalRevenue > 0) {
    $monthValues[count($monthValues) - 1] = (float) $totalRevenue;
}

$statusChart = [
    'labels' => [],
    'values' => [],
    'colors' => [],
    'legend' => [],
];

$statusTotal = array_sum(array_column($bookingStatus, 'total'));

foreach (['paid', 'pending', 'cancelled', 'refunded'] as $statusKey) {
    $count = 0;
    foreach ($bookingStatus as $row) {
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
        COALESCE(
            st.start_time,
            st.show_datetime,
            CONCAT(st.show_date, ' ', st.show_time)
        ) AS showtime_at,
        COALESCE(
            GROUP_CONCAT(
                DISTINCT COALESCE(
                    s.seat_label,
                    CONCAT(s.row_label, s.seat_number),
                    CONCAT(s.seat_row, s.seat_number),
                    CONCAT(s.row_name, s.seat_number)
                )
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
    GROUP BY b.id
    ORDER BY b.created_at DESC
    LIMIT 5
");

if (empty($recentBookings)) {
    $recentBookings = $dashDb->recentBookings();
}

$statCards = [
    ['label' => 'Users',     'value' => number_format($totalUsers),     'icon' => 'bi-people-fill',           'theme' => 'green',  'link' => '?action=users_list',     'growth' => $userGrowth],
    ['label' => 'Movies',    'value' => number_format($totalMovies),    'icon' => 'bi-film',                  'theme' => 'blue',   'link' => '?action=movies',         'growth' => $movieGrowth],
    ['label' => 'Bookings',  'value' => number_format($totalBookings),  'icon' => 'bi-ticket-perforated-fill','theme' => 'orange', 'link' => '?action=bookings',       'growth' => $bookingGrowth],
    ['label' => 'Revenue',   'value' => number_format($totalRevenue) . 'đ', 'icon' => 'bi-cash-stack',      'theme' => 'red',    'link' => '?action=payments',       'growth' => $revenueGrowth],
    ['label' => 'Rooms',     'value' => number_format($totalRooms),     'icon' => 'bi-building',              'theme' => 'purple', 'link' => '?action=rooms',          'growth' => ['text' => '— Không đổi', 'class' => 'text-muted', 'icon' => 'bi-dash']],
    ['label' => 'Seats',     'value' => number_format($totalSeats),     'icon' => 'bi-grid-3x3-gap-fill',     'theme' => 'teal',   'link' => '?action=seat-types',     'growth' => ['text' => '— Không đổi', 'class' => 'text-muted', 'icon' => 'bi-dash']],
    ['label' => 'Foods',     'value' => number_format($totalFoods),     'icon' => 'bi-cup-hot-fill',          'theme' => 'pink',   'link' => '?action=food_list',      'growth' => $foodGrowth],
    ['label' => 'Payments',  'value' => number_format($totalPayments),  'icon' => 'bi-credit-card-fill',      'theme' => 'gold',   'link' => '?action=payments',       'growth' => $paymentGrowth],
];
?>

<div class="dashboard-page">

<style>
.dashboard-page {
    --dash-green: #22c55e;
    --dash-blue: #3b82f6;
    --dash-orange: #f97316;
    --dash-red: #ef4444;
    --dash-purple: #8b5cf6;
    --dash-teal: #14b8a6;
    --dash-pink: #ec4899;
    --dash-gold: #f59e0b;
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

.stat-card-link {
    text-decoration: none;
    color: inherit;
    display: block;
    height: 100%;
}

.stat-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #f3f4f6;
    padding: 1.25rem 1.25rem .75rem;
    height: 100%;
    transition: transform .25s ease, box-shadow .25s ease;
}

.stat-card-link:hover .stat-card {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(15, 23, 42, .08);
}

.stat-card-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: .75rem;
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
    margin-bottom: .5rem;
}

.stat-trend {
    font-size: .78rem;
    display: flex;
    align-items: center;
    gap: .25rem;
    margin-bottom: .75rem;
}

.stat-sparkline {
    height: 42px;
}

.theme-green .stat-icon { background: rgba(34,197,94,.12); color: var(--dash-green); }
.theme-blue .stat-icon { background: rgba(59,130,246,.12); color: var(--dash-blue); }
.theme-orange .stat-icon { background: rgba(249,115,22,.12); color: var(--dash-orange); }
.theme-red .stat-icon { background: rgba(239,68,68,.12); color: var(--dash-red); }
.theme-purple .stat-icon { background: rgba(139,92,246,.12); color: var(--dash-purple); }
.theme-teal .stat-icon { background: rgba(20,184,166,.12); color: var(--dash-teal); }
.theme-pink .stat-icon { background: rgba(236,72,153,.12); color: var(--dash-pink); }
.theme-gold .stat-icon { background: rgba(245,158,11,.12); color: var(--dash-gold); }

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

.chart-filter {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: .35rem .75rem;
    font-size: .85rem;
    color: #6b7280;
    background: #fff;
}

.chart-wrap {
    position: relative;
    height: 280px;
}

.donut-layout{
    display:flex;
    align-items:center;
    gap:20px;
}

.donut-canvas-wrap{
    flex:0 0 180px;
    width:180px;
    height:180px;
    display:flex;
    justify-content:center;
    align-items:center;
}

#statusChart{
    width:180px !important;
    height:180px !important;
}

.status-legend{
    flex:1;
}

.status-legend-item{
    display:flex;
    align-items:flex-start;
    gap:12px;
    padding:10px 0;
}

.status-legend-item strong{
    margin-left:auto;
    white-space:nowrap;
}

.status-legend-item:last-child{
    border-bottom: none;
}

.status-legend-item span:first-child{
    display: flex;
    align-items: center;
    gap: 10px;
}

.status-dot{
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

.recent-card-header a {
    color: #dc3545;
    text-decoration: none;
    font-size: .9rem;
    font-weight: 500;
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
    <div class="dashboard-date"><?= date('d/m/Y') ?></div>
</div>

<div class="welcome-banner">
    <div>
        <h4>Xin chào, <?= $adminName ?>! 👋</h4>
        <p>Chúc bạn một ngày làm việc hiệu quả.</p>
    </div>
    <div class="welcome-illustration" aria-hidden="true">
        <span>🍿</span>
        <span>🎬</span>
    </div>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($statCards as $index => $card): ?>
        <div class="col-xl-3 col-md-6">
            <a href="<?= h($card['link']) ?>" class="stat-card-link">
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
                    <div class="stat-trend <?= h($card['growth']['class']) ?>">
                        <i class="bi <?= h($card['growth']['icon']) ?>"></i>
                        <span><?= h($card['growth']['text']) ?></span>
                    </div>
                    <div class="stat-sparkline">
                        <canvas id="sparkline-<?= $index ?>"></canvas>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="chart-card">
            <div class="chart-card-header">
                <h5>Doanh thu theo tháng</h5>
                <span class="chart-filter">7 tháng gần nhất</span>
            </div>
            <div class="chart-wrap">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="chart-card">
            <div class="chart-card-header">
                <h5>Tỷ lệ đặt vé theo trạng thái</h5>
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
        <h5>Đặt vé gần đây</h5>
        <a href="?action=bookings">Xem tất cả</a>
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
                        <td colspan="8" class="text-center text-muted py-4">Chưa có dữ liệu đặt vé.</td>
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
                            <td><?= !empty($booking['created_at']) ? h(date('d/m/Y H:i', strtotime($booking['created_at']))) : '—' ?></td>
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
    const monthLabels = <?= json_encode($monthLabels, JSON_UNESCAPED_UNICODE) ?>;
    const monthValues = <?= json_encode($monthValues, JSON_UNESCAPED_UNICODE) ?>;
    const statusLabels = <?= json_encode($statusChart['labels'], JSON_UNESCAPED_UNICODE) ?>;
    const statusValues = <?= json_encode($statusChart['values'], JSON_UNESCAPED_UNICODE) ?>;
    const statusColors = <?= json_encode($statusChart['colors'], JSON_UNESCAPED_UNICODE) ?>;

    const sparkThemes = ['#22c55e', '#3b82f6', '#f97316', '#ef4444', '#8b5cf6', '#14b8a6', '#ec4899', '#f59e0b'];

    const buildSparkData = (index) => {
        const base = Number(monthValues[index % monthValues.length] || 0);
        const points = [];
        for (let i = 0; i < 7; i++) {
            points.push(Math.max(0, base * (0.55 + ((i + index) % 4) * 0.12)));
        }
        return points;
    };

    sparkThemes.forEach((color, index) => {
        const canvas = document.getElementById(`sparkline-${index}`);
        if (!canvas) return;

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: ['', '', '', '', '', '', ''],
                datasets: [{
                    data: buildSparkData(index),
                    borderColor: color,
                    backgroundColor: color + '22',
                    fill: true,
                    tension: .45,
                    pointRadius: 0,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
                scales: {
                    x: { display: false },
                    y: { display: false }
                }
            }
        });
    });

    const revenueCanvas = document.getElementById('revenueChart');
    if (revenueCanvas) {
        new Chart(revenueCanvas, {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'Doanh thu',
                    data: monthValues,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.12)',
                    fill: true,
                    tension: .35,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ' ' + Number(ctx.raw).toLocaleString('vi-VN') + 'đ'
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            color: '#6b7280',
                            callback: (value) => {
                                if (value >= 1000000) return (value / 1000000) + 'M';
                                if (value >= 1000) return (value / 1000) + 'K';
                                return value;
                            }
                        }
                    }
                }
            }
        });
    }

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
