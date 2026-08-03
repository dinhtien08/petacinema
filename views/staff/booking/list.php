<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Danh sách đặt vé</h4>
        <p class="text-muted mb-0">
            Theo dõi danh sách đặt vé của khách hàng
        </p>
    </div>
</div>

<?php if (!empty($flash)) : ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php
$keyword = $_GET['keyword'] ?? '';
?>

<!-- Search -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" action="" class="row g-2 align-items-center">

            <input type="hidden" name="action" value="staff_booking_list">

            <div class="col-md-4">
                <input
                    type="text"
                    name="keyword"
                    class="form-control form-control-sm"
                    placeholder="Tìm theo mã booking, khách hàng, email, ghế..."
                    value="<?= htmlspecialchars($keyword) ?>">
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-search me-1"></i>
                    Tìm kiếm
                </button>
            </div>

            <?php if (!empty($keyword)) : ?>

                <div class="col-md-2">
                    <a href="<?= BASE_URL ?>?action=staff_booking_list"
                       class="btn btn-outline-secondary btn-sm w-100">
                        Làm mới
                    </a>
                </div>

            <?php endif; ?>

        </form>
    </div>
</div>

<?php if (!empty($keyword)) : ?>

<div class="alert alert-info py-2 mb-3">
    <i class="bi bi-info-circle me-1"></i>

    Tìm thấy
    <strong><?= count($bookings) ?></strong>
    kết quả cho từ khóa
    "<strong><?= htmlspecialchars($keyword) ?></strong>"

</div>

<?php endif; ?>

<div class="card shadow-sm border-0">

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th class="ps-4">STT</th>

                        <th>Mã booking</th>

                        <th>Khách hàng</th>

                        <th>Phim</th>

                        <th>Suất chiếu</th>

                        <th>Ghế</th>

                        <th>Tổng tiền</th>

                        <th>Trạng thái</th>

                        <th class="text-end pe-4">Chi tiết</th>

                    </tr>

                </thead>

                <tbody>

                <?php if (!empty($bookings)) : ?>

                    <?php $stt = 1; ?>

                    <?php foreach ($bookings as $booking) : ?>

                    <?php

                    $statusClass = match ($booking['status']) {

                        'pending'   => 'bg-warning text-dark',

                        'paid'      => 'bg-success',

                        'cancelled' => 'bg-secondary',

                        default     => 'bg-dark'

                    };

                    $statusLabel = match ($booking['status']) {

                        'pending'   => 'Chờ xử lý',

                        'paid'      => 'Đã thanh toán',

                        'cancelled' => 'Đã hủy',

                        default     => 'Không xác định'

                    };

                    ?>

                    <tr>

                        <td class="ps-4">
                            <?= $stt++ ?>
                        </td>

                        <td class="fw-semibold text-primary">
                            <?= htmlspecialchars($booking['booking_code']) ?>
                        </td>

                        <td>

                            <?= htmlspecialchars($booking['customer_name']) ?>

                            <br>

                            <small class="text-muted">
                                <?= htmlspecialchars($booking['customer_email']) ?>
                            </small>

                        </td>

                        <td>
                            <?= htmlspecialchars($booking['movie_title']) ?>
                        </td>

                        <td>
                            <?= date('d/m/Y H:i', strtotime($booking['start_time'])) ?>
                        </td>

                        <td>

                            <?php if (!empty($booking['seat_numbers'])) : ?>

                                <span class="badge text-bg-secondary">

                                    <?= htmlspecialchars($booking['seat_numbers']) ?>

                                </span>

                            <?php else : ?>

                                <span class="text-muted small">
                                    Chưa có ghế
                                </span>

                            <?php endif; ?>

                        </td>

                        <td>

                            <?= number_format((float)$booking['total_amount']) ?>đ

                        </td>

                        <td>

                            <span class="badge <?= $statusClass ?>">

                                <?= $statusLabel ?>

                            </span>

                        </td>

                        <td class="text-end pe-4">

                            <a
                                href="<?= BASE_URL ?>?action=staff_booking_show&id=<?= $booking['id'] ?>"
                                class="btn btn-sm btn-outline-primary"
                                title="Xem chi tiết">

                                <i class="bi bi-eye"></i>

                            </a>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                <?php else : ?>

                    <tr>

                        <td colspan="9" class="text-center py-5">

                            <div class="text-muted">

                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>

                                Không có dữ liệu đặt vé.

                            </div>

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>