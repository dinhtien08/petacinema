<?php

/*
|--------------------------------------------------------------------------
| Hàm escape HTML
|--------------------------------------------------------------------------
*/

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars(
            (string)$value,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

/*
|--------------------------------------------------------------------------
| Lấy dữ liệu bộ lọc
|--------------------------------------------------------------------------
*/

$keyword = trim($_GET['keyword'] ?? '');

$hasFilter = $keyword !== '';

/*
|--------------------------------------------------------------------------
| Xử lý danh sách đặt vé
|--------------------------------------------------------------------------
*/

$displayBookings = $bookings ?? [];

/*
|--------------------------------------------------------------------------
| Thống kê nhanh
|--------------------------------------------------------------------------
*/

$totalBookings     = count($displayBookings);
$pendingBookings   = 0;
$paidBookings      = 0;
$cancelledBookings = 0;

foreach ($displayBookings as $booking) {
    switch ($booking['status']) {
        case 'pending':
            $pendingBookings++;
            break;

        case 'paid':
            $paidBookings++;
            break;

        case 'cancelled':
            $cancelledBookings++;
            break;
    }
}

?>

<!-- Tiêu đề trang -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

    <div>
        <h4 class="fw-bold mb-1">Danh sách đặt vé</h4>

        <p class="text-muted mb-0">
            Theo dõi danh sách đặt vé của khách hàng
        </p>
    </div>

</div>

<!-- Thông báo -->
<?php if (!empty($flash)): ?>

    <div
        class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show"
        role="alert">

        <i class="bi <?= $flash['type'] === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> me-2"></i>

        <?= e($flash['message']) ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Đóng">
        </button>

    </div>

<?php endif; ?>

<!-- Thống kê nhanh -->
<div class="row g-3 mb-4">

    <div class="col-xl-3 col-md-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body d-flex align-items-center gap-3">

                <div class="bg-dark-subtle rounded-3 p-3">
                    <i class="bi bi-ticket-perforated fs-4"></i>
                </div>

                <div>
                    <div class="text-muted small">
                        Tổng đặt vé
                    </div>

                    <div class="fs-4 fw-bold">
                        <?= $totalBookings ?>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <div class="col-xl-3 col-md-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body d-flex align-items-center gap-3">

                <div class="bg-warning-subtle text-warning rounded-3 p-3">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>

                <div>
                    <div class="text-muted small">
                        Chờ xử lý
                    </div>

                    <div class="fs-4 fw-bold text-warning">
                        <?= $pendingBookings ?>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <div class="col-xl-3 col-md-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body d-flex align-items-center gap-3">

                <div class="bg-success-subtle text-success rounded-3 p-3">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                </div>

                <div>
                    <div class="text-muted small">
                        Đã thanh toán
                    </div>

                    <div class="fs-4 fw-bold text-success">
                        <?= $paidBookings ?>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <div class="col-xl-3 col-md-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body d-flex align-items-center gap-3">

                <div class="bg-secondary-subtle text-secondary rounded-3 p-3">
                    <i class="bi bi-x-circle-fill fs-4"></i>
                </div>

                <div>
                    <div class="text-muted small">
                        Đã hủy
                    </div>

                    <div class="fs-4 fw-bold text-secondary">
                        <?= $cancelledBookings ?>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

<!-- Bộ lọc -->
<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">

        <h5 class="mb-0">
            <i class="bi bi-funnel me-2"></i>
            Bộ lọc đặt vé
        </h5>

    </div>

    <div class="card-body">

        <form
            method="GET"
            action=""
            class="row g-3 align-items-end">

            <input
                type="hidden"
                name="action"
                value="staff_booking_list">

            <div class="col-md-8">

                <label class="form-label fw-semibold">
                    Từ khóa
                </label>

                <input
                    type="text"
                    name="keyword"
                    class="form-control"
                    placeholder="Tìm theo mã booking, khách hàng, email, ghế..."
                    value="<?= e($keyword) ?>">

            </div>

            <div class="col-md-4">

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary flex-grow-1">

                        <i class="bi bi-search me-1"></i>
                        Tìm kiếm

                    </button>

                    
                        <a href="<?= BASE_URL ?>?action=staff_booking_list"
                        class="btn btn-outline-secondary"
                        title="Đặt lại bộ lọc">

                        <i class="bi bi-arrow-counterclockwise"></i>

                    </a>

                </div>

            </div>

        </form>

    </div>

</div>

<!-- Thông báo kết quả lọc -->
<?php if ($hasFilter): ?>

    <div class="alert alert-info py-2 mb-3">

        <i class="bi bi-info-circle me-1"></i>

        Tìm thấy

        <strong>
            <?= count($displayBookings) ?>
        </strong>

        kết quả cho từ khóa

        <strong>
            “<?= e($keyword) ?>”
        </strong>

    </div>

<?php endif; ?>

<!-- Bảng danh sách -->
<div class="card border-0 shadow-sm">

    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">

        <div>

            <h4 class="mb-1">
                Danh sách đặt vé
            </h4>

            <small class="text-muted">
                Toàn bộ đơn đặt vé của khách hàng
            </small>

        </div>

        <span class="badge text-bg-light border px-3 py-2">
            <?= count($displayBookings) ?> đặt vé
        </span>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr class="text-center">

                        <th width="65">STT</th>

                        <th class="text-start">
                            Mã booking
                        </th>

                        <th class="text-start">
                            Khách hàng
                        </th>

                        <th class="text-start">
                            Phim
                        </th>

                        <th>
                            Suất chiếu
                        </th>

                        <th>
                            Ghế
                        </th>

                        <th>
                            Tổng tiền
                        </th>

                        <th>
                            Trạng thái
                        </th>

                        <th>
                            Check-in
                        </th>

                        <th width="100">
                            Chi tiết
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php if (!empty($displayBookings)): ?>

                        <?php foreach ($displayBookings as $index => $booking): ?>

                            <?php
                            $statusClass = match ($booking['status']) {
                                'pending'   => 'text-bg-warning',
                                'paid'      => 'text-bg-success',
                                'cancelled' => 'text-bg-secondary',
                                default     => 'text-bg-dark',
                            };

                            $statusLabel = match ($booking['status']) {
                                'pending'   => 'Chờ xử lý',
                                'paid'      => 'Đã thanh toán',
                                'cancelled' => 'Đã hủy',
                                default     => 'Không xác định',
                            };

                            $statusIcon = match ($booking['status']) {
                                'pending'   => 'bi-hourglass-split',
                                'paid'      => 'bi-check-circle-fill',
                                'cancelled' => 'bi-x-circle-fill',
                                default     => 'bi-question-circle-fill',
                            };

                            $rowClass = $booking['status'] === 'cancelled'
                                ? 'table-light'
                                : '';
                            ?>

                            <tr class="<?= $rowClass ?>">

                                <td class="text-center">
                                    <?= $index + 1 ?>
                                </td>

                                <td class="fw-semibold text-primary">
                                    <?= e($booking['booking_code']) ?>
                                </td>

                                <td>

                                    <div class="fw-semibold">
                                        <?= e($booking['customer_name']) ?>
                                    </div>

                                    <small class="text-muted">
                                        <?= e($booking['customer_email']) ?>
                                    </small>

                                </td>

                                <td>
                                    <?= e($booking['movie_title']) ?>
                                </td>

                                <td class="text-center">

                                    <?= date(
                                        'd/m/Y H:i',
                                        strtotime($booking['start_time'])
                                    ) ?>

                                </td>

                                <td class="text-center">

                                    <?php if (!empty($booking['seat_numbers'])): ?>

                                        <span class="badge text-bg-light border">
                                            <?= e($booking['seat_numbers']) ?>
                                        </span>

                                    <?php else: ?>

                                        <span class="text-muted small">
                                            Chưa có ghế
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td class="text-end">

                                    <?= number_format(
                                        (float)$booking['total_amount'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>

                                    đ

                                </td>

                                <td class="text-center">

                                    <span class="badge <?= $statusClass ?> px-3 py-2">

                                        <i class="bi <?= $statusIcon ?> me-1"></i>

                                        <?= $statusLabel ?>

                                    </span>

                                </td>

                                <td class="text-center">
                                    <?php if (($booking['checkin_status'] ?? 'pending') === 'checked_in'): ?>
                                        <span class="badge text-bg-success px-3 py-2">
                                            <i class="bi bi-person-check-fill me-1"></i> Đã check-in
                                        </span>
                                    <?php else: ?>
                                        <span class="badge text-bg-light border text-dark px-3 py-2">
                                            <i class="bi bi-person-dash me-1"></i> Chưa check-in
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">

                                    
                                        <a href="<?= BASE_URL ?>?action=staff_booking_show&id=<?= (int)$booking['id'] ?>"
                                        class="btn btn-outline-primary btn-sm"
                                        title="Xem chi tiết">

                                        <i class="bi bi-eye"></i>

                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="10"
                                class="text-center py-5">

                                <div class="mb-3">

                                    <i class="bi bi-inbox display-5 text-muted"></i>

                                </div>

                                <h5>
                                    Không có dữ liệu đặt vé
                                </h5>

                                <p class="text-muted mb-3">

                                    <?php if ($hasFilter): ?>

                                        Không tìm thấy đặt vé nào phù hợp với từ khóa

                                        <strong>
                                            “<?= e($keyword) ?>”
                                        </strong>

                                    <?php else: ?>

                                        Chưa có đơn đặt vé nào được ghi nhận.

                                    <?php endif; ?>

                                </p>

                                <?php if ($hasFilter): ?>

                                    
                                        href="<?= BASE_URL ?>?action=staff_booking_list"
                                        class="btn btn-outline-primary">

                                        <i class="bi bi-arrow-counterclockwise me-1"></i>

                                        Xóa bộ lọc

                                    </a>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>