<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Booking</h4>
        <p class="text-muted mb-0">Quản lý đơn đặt vé</p>
    </div>

    <a href="<?= BASE_URL ?>?action=booking_add" class="btn btn-danger">
        <i class="bi bi-plus-lg me-1"></i>
        Thêm booking
    </a>
</div>

<?php if (!empty($flash)) : ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible">
        <?= htmlspecialchars($flash['message']) ?>
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
                                <td class="fw-semibold"><?= htmlspecialchars($booking['booking_code']) ?></td>
                                <td>
                                    <?= htmlspecialchars($booking['customer_name']) ?>
                                    <br>
                                    <small class="text-muted"><?= htmlspecialchars($booking['customer_email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($booking['movie_title']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($booking['start_time'])) ?></td>
                                <td><?= number_format((float) $booking['total_amount']) ?>đ</td>
                                <td>
                                    <span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                                </td>
                                <td class="text-end pe-4">

                                    <a
                                        href="<?= BASE_URL ?>?action=booking_edit&id=<?= $booking['id'] ?>"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="<?= BASE_URL ?>?action=booking_delete" method="post" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $booking['id'] ?>">
                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa booking này?')"
                                            title="Xóa">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>

                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">Chưa có booking nào.</td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>
</div>
