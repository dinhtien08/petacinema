<div class="alert alert-info py-2 mb-3">
    <i class="bi bi-info-circle me-1"></i>
    Chức năng này chỉ dùng để kiểm thử dữ liệu. Booking thực tế được tạo từ quy trình đặt vé online.
</div>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Booking</h4>
        <p class="text-muted mb-0">Quản lý đơn đặt vé (Read-only)</p>
    </div>

    <a href="<?= BASE_URL ?>?action=booking_add" class="btn btn-danger">
        <i class="bi bi-plus-lg me-1"></i>
        Thêm dữ liệu test
    </a>
</div>

<?php if (!empty($flash)) : ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>

<?php
$keyword = $_GET['keyword'] ?? '';
?>

<!-- Search Box -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" action="" class="row g-2 align-items-center">
            <input type="hidden" name="action" value="booking_list">
            <div class="col-md-4">
                <input type="text" name="keyword" class="form-control form-control-sm" placeholder="Tìm theo mã booking, tên khách, email, ghế..." value="<?= htmlspecialchars($keyword) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Tìm kiếm
                </button>
            </div>
            <?php if (!empty($keyword)) : ?>
                <div class="col-md-2">
                    <a href="<?= BASE_URL ?>?action=booking_list" class="btn btn-sm btn-outline-secondary w-100">Làm mới</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if (!empty($keyword)) : ?>
    <div class="alert alert-info py-2 mb-3">
        <i class="bi bi-info-circle me-1"></i>
        Showing <?= count($bookings) ?> result(s) for "<strong><?= htmlspecialchars($keyword) ?></strong>"
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Mã booking</th>
                        <th>Khách hàng</th>
                        <th>Phim</th>
                        <th>Suất chiếu</th>
                        <th>Ghế đã đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($bookings)) : ?>
                        <?php foreach ($bookings as $booking) : ?>
                            <?php
                            $statusClass = match ($booking['status']) {
                                'pending'   => 'bg-warning text-dark',
                                'paid'      => 'bg-success',
                                'cancelled' => 'bg-secondary',
                                default     => 'bg-dark',
                            };
                            $statusLabel = match ($booking['status']) {
                                'pending'   => 'Chờ xử lý',
                                'paid'      => 'Đã thanh toán',
                                'cancelled' => 'Đã hủy',
                                default     => 'Không xác định',
                            };
                            ?>
                            <tr>
                                <td class="ps-4"><?= $booking['id'] ?></td>
                                <td class="fw-semibold text-primary"><?= htmlspecialchars($booking['booking_code']) ?></td>
                                <td>
                                    <?= htmlspecialchars($booking['customer_name']) ?>
                                    <br>
                                    <small class="text-muted"><?= htmlspecialchars($booking['customer_email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($booking['movie_title']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($booking['start_time'])) ?></td>
                                <td>
                                    <?php if (!empty($booking['seat_numbers'])): ?>
                                        <span class="badge text-bg-secondary"><?= htmlspecialchars($booking['seat_numbers']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">Chưa xếp ghế</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= number_format((float) $booking['total_amount']) ?>đ</td>
                                <td>
                                    <span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                                </td>
                                <td class="text-end pe-4">

                                    <a
                                        href="<?= BASE_URL ?>?action=booking_show&id=<?= $booking['id'] ?>"
                                        class="btn btn-sm btn-outline-info"
                                        title="Xem chi tiết">
                                        <i class="bi bi-eye me-1"></i> Chi tiết
                                    </a>

                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="alert alert-warning mb-0 d-inline-block px-4" role="alert">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    No matching data found.
                                </div>
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>
</div>
