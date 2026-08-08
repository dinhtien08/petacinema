<?php
$staffName = $_SESSION['user']['fullname'] ?? 'Nhân viên';

$statusMetaMap = [
    'paid' => ['label' => 'Đã thanh toán', 'class' => 'badge-paid', 'color' => '#22c55e'],
    'pending' => ['label' => 'Đang chờ', 'class' => 'badge-pending', 'color' => '#f59e0b'],
    'cancelled' => ['label' => 'Đã hủy', 'class' => 'badge-cancelled', 'color' => '#ef4444'],
];

$statusCounts = [];
foreach ($bookingStatusToday as $row) {
    $statusCounts[$row['status']] = (int) $row['total'];
}

$statusLabels = [];
$statusValues = [];
$statusColors = [];
$statusLegend = [];
$statusTotal = array_sum($statusCounts);

foreach ($statusMetaMap as $key => $meta) {
    $count = $statusCounts[$key] ?? 0;
    $statusLabels[] = $meta['label'];
    $statusValues[] = $count;
    $statusColors[] = $meta['color'];
    $statusLegend[] = [
        'label' => $meta['label'],
        'count' => $count,
        'percent' => $statusTotal > 0 ? round(($count / $statusTotal) * 100) : 0,
        'color' => $meta['color'],
    ];
}

$statCards = [
    ['label' => 'Doanh thu hôm nay', 'value' => number_format($todayRevenue) . 'đ', 'icon' => 'bi-cash-stack', 'theme' => 'red', 'link' => '?action=staff_payment_list'],
    ['label' => 'Vé bán hôm nay', 'value' => number_format($todayTickets), 'icon' => 'bi-ticket-perforated-fill', 'theme' => 'orange', 'link' => '?action=staff_booking_list'],
    ['label' => 'Booking hôm nay', 'value' => number_format($todayBookings), 'icon' => 'bi-receipt', 'theme' => 'blue', 'link' => '?action=staff_booking_list'],
    ['label' => 'Suất chiếu hôm nay', 'value' => number_format($todayShowtimes), 'icon' => 'bi-camera-reels', 'theme' => 'purple', 'link' => '?action=staff_showtimes'],
];
?>

<style>
.dashboard-page { --dash-red:#ef4444; --dash-orange:#f97316; --dash-blue:#3b82f6; --dash-purple:#8b5cf6; }
.dashboard-header { display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1.25rem; }
.dashboard-header h2 { margin:0; font-weight:700; }
.dashboard-date { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:.5rem .85rem; color:#374151; font-weight:600; }
.welcome-banner { display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1.5rem; padding:1.35rem 1.5rem; border:1px solid #f1f5f9; border-radius:16px; background:linear-gradient(135deg,#fff 0%,#fff7f7 100%); }
.welcome-banner h4 { font-weight:700; margin-bottom:.35rem; }
.welcome-banner p { margin:0; color:#6b7280; }
.welcome-illustration { font-size:2.35rem; white-space:nowrap; }
.stat-card-link { display:block; height:100%; color:inherit; text-decoration:none; }
.stat-card { height:100%; padding:1.2rem; background:#fff; border:1px solid #eef2f7; border-radius:16px; transition:.2s ease; }
.stat-card-link:hover .stat-card { transform:translateY(-3px); box-shadow:0 10px 24px rgba(15,23,42,.08); }
.stat-card-top { display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; }
.stat-value { font-size:1.55rem; font-weight:700; line-height:1.25; overflow-wrap:anywhere; }
.stat-label { margin-top:.3rem; color:#6b7280; font-size:.9rem; }
.stat-icon { width:44px; height:44px; flex:0 0 44px; display:grid; place-items:center; border-radius:12px; font-size:1.2rem; }
.theme-red .stat-icon { background:rgba(239,68,68,.12); color:var(--dash-red); }
.theme-orange .stat-icon { background:rgba(249,115,22,.12); color:var(--dash-orange); }
.theme-blue .stat-icon { background:rgba(59,130,246,.12); color:var(--dash-blue); }
.theme-purple .stat-icon { background:rgba(139,92,246,.12); color:var(--dash-purple); }
.dashboard-card { height:100%; background:#fff; border:1px solid #eef2f7; border-radius:16px; overflow:hidden; }
.dashboard-card-header { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:1.15rem 1.2rem .8rem; }
.dashboard-card-header h5 { margin:0; font-weight:700; }
.dashboard-card-body { padding:1.2rem; }
.status-layout { display:flex; align-items:center; gap:1.5rem; min-height:215px; }
.status-chart-wrap { width:170px; height:170px; flex:0 0 170px; }
.status-legend { flex:1; }
.status-item { display:flex; justify-content:space-between; align-items:center; gap:.75rem; padding:.7rem 0; border-bottom:1px solid #f3f4f6; }
.status-item:last-child { border-bottom:0; }
.status-name { display:flex; align-items:center; gap:.55rem; }
.status-dot { width:9px; height:9px; border-radius:50%; }
.recent-table { margin:0; }
.recent-table thead th { background:#f8fafc; color:#64748b; font-size:.8rem; font-weight:600; white-space:nowrap; border-bottom:1px solid #e5e7eb; }
.recent-table tbody td { vertical-align:middle; font-size:.88rem; border-bottom-color:#f1f5f9; }
.status-badge, .checkin-badge { display:inline-block; padding:.32rem .65rem; border-radius:999px; font-size:.75rem; font-weight:600; white-space:nowrap; }
.badge-paid { background:#dcfce7; color:#15803d; }
.badge-pending { background:#fef3c7; color:#b45309; }
.badge-cancelled { background:#fee2e2; color:#b91c1c; }
.checkin-ok { background:#dbeafe; color:#1d4ed8; }
.checkin-wait { background:#f3f4f6; color:#6b7280; }
@media(max-width:991px){ .dashboard-header,.welcome-banner{align-items:flex-start;flex-direction:column}.status-layout{flex-direction:column;align-items:stretch}.status-chart-wrap{margin:0 auto} }
</style>

<div class="dashboard-page">
    <div class="dashboard-header">
        <div>
            <h2>Dashboard</h2>
            <div class="text-muted small mt-1">Hoạt động trong ngày của nhân viên</div>
        </div>
        <div class="dashboard-date"><i class="bi bi-calendar-event me-1"></i><?= h($today) ?></div>
    </div>

    <div class="welcome-banner">
        <div>
            <h4>Xin chào, <?= h($staffName) ?>! 👋</h4>
            <p>Theo dõi booking, vé bán, suất chiếu và trạng thái check-in trong hôm nay.</p>
        </div>
        <div class="welcome-illustration" aria-hidden="true">🍿 🎬</div>
    </div>

    <div class="row g-3 mb-4">
        <?php foreach ($statCards as $card): ?>
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="<?= h($card['link']) ?>" class="stat-card-link">
                    <div class="stat-card theme-<?= h($card['theme']) ?>">
                        <div class="stat-card-top">
                            <div>
                                <div class="stat-value"><?= h($card['value']) ?></div>
                                <div class="stat-label"><?= h($card['label']) ?></div>
                            </div>
                            <div class="stat-icon"><i class="bi <?= h($card['icon']) ?>"></i></div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-6">
            <div class="dashboard-card">
                <div class="dashboard-card-header"><h5>Trạng thái booking hôm nay</h5></div>
                <div class="dashboard-card-body">
                    <div class="status-layout">
                        <div class="status-chart-wrap"><canvas id="statusChart"></canvas></div>
                        <div class="status-legend">
                            <?php foreach ($statusLegend as $item): ?>
                                <div class="status-item">
                                    <div class="status-name"><span class="status-dot" style="background:<?= h($item['color']) ?>"></span><span><?= h($item['label']) ?></span></div>
                                    <div><strong><?= $item['count'] ?></strong> đơn · <?= $item['percent'] ?>%</div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h5>Thao tác nhanh</h5>
                </div>
                <div class="dashboard-card-body">
                    <div class="row g-3">
                        <div class="col-sm-6"><a href="?action=staff_booking_checkin" class="btn btn-danger w-100 py-3"><i class="bi bi-qr-code-scan me-2"></i>Check-in booking</a></div>
                        <div class="col-sm-6"><a href="?action=staff_showtimes" class="btn btn-outline-danger w-100 py-3"><i class="bi bi-camera-reels me-2"></i>Xem suất chiếu</a></div>
                        <div class="col-sm-6"><a href="?action=staff_booking_list" class="btn btn-outline-secondary w-100 py-3"><i class="bi bi-ticket-perforated me-2"></i>Danh sách booking</a></div>
                        <div class="col-sm-6"><a href="?action=staff_food_order_list" class="btn btn-outline-secondary w-100 py-3"><i class="bi bi-cup-straw me-2"></i>Đơn đồ ăn</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h5>Booking trong hôm nay</h5>
            <a href="?action=staff_booking_list" class="text-danger text-decoration-none small fw-semibold">Xem tất cả</a>
        </div>
        <div class="table-responsive">
            <table class="table recent-table">
                <thead>
                    <tr>
                        <th>Mã booking</th>
                        <th>Khách hàng</th>
                        <th>Phim</th>
                        <th>Suất chiếu</th>
                        <th>Ghế</th>
                        <th>Số tiền</th>
                        <th>Thanh toán</th>
                        <th>Check-in</th>
                        <th>Tạo lúc</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentBookings)): ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">Chưa có booking nào được tạo hôm nay.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentBookings as $booking):
                            $meta = $statusMetaMap[$booking['status']] ?? ['label' => $booking['status'], 'class' => 'badge-pending'];
                        ?>
                            <tr>
                                <td><strong><?= h($booking['booking_code'] ?: '—') ?></strong></td>
                                <td><?= h($booking['customer_name']) ?></td>
                                <td><?= h($booking['movie_title']) ?></td>
                                <td><?= !empty($booking['showtime_at']) ? h(date('d/m/Y H:i', strtotime($booking['showtime_at']))) : '—' ?></td>
                                <td><?= h($booking['seat_labels']) ?></td>
                                <td><?= number_format((float) $booking['total_amount']) ?>đ</td>
                                <td><span class="status-badge <?= h($meta['class']) ?>"><?= h($meta['label']) ?></span></td>
                                <td>
                                    <?php if (($booking['checkin_status'] ?? 'pending') === 'checked_in'): ?>
                                        <span class="checkin-badge checkin-ok"><i class="bi bi-check-circle me-1"></i>Đã check-in</span>
                                    <?php else: ?>
                                        <span class="checkin-badge checkin-wait">Chưa check-in</span>
                                    <?php endif; ?>
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
    if (typeof Chart === 'undefined') return;
    const canvas = document.getElementById('statusChart');
    if (!canvas) return;

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($statusLabels, JSON_UNESCAPED_UNICODE) ?>,
            datasets: [{
                data: <?= json_encode($statusValues, JSON_UNESCAPED_UNICODE) ?>,
                backgroundColor: <?= json_encode($statusColors, JSON_UNESCAPED_UNICODE) ?>,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: (ctx) => ` ${ctx.label}: ${ctx.raw} đơn` } }
            }
        }
    });
})();
</script>
