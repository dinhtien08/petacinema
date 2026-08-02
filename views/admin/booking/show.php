<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Chi tiết Booking: <?= htmlspecialchars($booking['booking_code'] ?? '') ?></h4>
        <p class="text-muted mb-0">Xem thông tin chi tiết đơn đặt vé (Read-only)</p>
    </div>

    <a href="<?= BASE_URL ?>?action=booking_list" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>
        Quay lại danh sách
    </a>
</div>

<div class="row g-4 mb-4">
    <!-- Booking & Customer Info -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-receipt me-2"></i> Thông tin đơn hàng
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless align-middle mb-0">
                    <tr>
                        <td class="text-muted" width="140">Mã booking:</td>
                        <td class="fw-bold text-primary"><?= htmlspecialchars($booking['booking_code'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Khách hàng:</td>
                        <td>
                            <div class="fw-semibold"><?= htmlspecialchars($booking['customer_name'] ?? '') ?></div>
                            <small class="text-muted"><?= htmlspecialchars($booking['customer_email'] ?? '') ?></small>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Trạng thái:</td>
                        <td>
                            <?php
                            $statusClass = match ($booking['status'] ?? '') {
                                'pending'   => 'bg-warning text-dark',
                                'paid'      => 'bg-success',
                                'cancelled' => 'bg-secondary',
                                default     => 'bg-dark',
                            };
                            $statusLabel = match ($booking['status'] ?? '') {
                                'pending'   => 'Chờ xử lý',
                                'paid'      => 'Đã thanh toán',
                                'cancelled' => 'Đã hủy',
                                default     => 'Không xác định',
                            };
                            ?>
                            <span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tổng thanh toán:</td>
                        <td class="fw-bold text-danger fs-5"><?= number_format((float)($booking['total_amount'] ?? 0), 0, ',', '.') ?> đ</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ngày tạo:</td>
                        <td><?= !empty($booking['created_at']) ? date('d/m/Y H:i:s', strtotime($booking['created_at'])) : '-' ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Showtime Info -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-film me-2"></i> Thông tin suất chiếu
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless align-middle mb-0">
                    <tr>
                        <td class="text-muted" width="140">Phim:</td>
                        <td class="fw-bold"><?= htmlspecialchars($booking['movie_title'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Phòng chiếu:</td>
                        <td>
                            <span class="fw-semibold"><?= htmlspecialchars($booking['room_name'] ?? '') ?></span>
                            <?php if (!empty($booking['room_type_name'])): ?>
                                <span class="badge text-bg-info ms-1"><?= htmlspecialchars($booking['room_type_name']) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Thời gian chiếu:</td>
                        <td>
                            <?= !empty($booking['start_time']) ? date('d/m/Y H:i', strtotime($booking['start_time'])) : '-' ?>
                            -
                            <?= !empty($booking['end_time']) ? date('H:i', strtotime($booking['end_time'])) : '-' ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Giá suất chiếu:</td>
                        <td><?= number_format((float)($booking['base_price'] ?? 0), 0, ',', '.') ?> đ</td>
                    </tr>
                    <?php if (!empty($booking['payment_method'])): ?>
                        <tr>
                            <td class="text-muted">Thanh toán:</td>
                            <td>
                                <span class="text-uppercase fw-semibold"><?= htmlspecialchars($booking['payment_method']) ?></span>
                                <?php if (!empty($booking['transaction_code'])): ?>
                                    <small class="text-muted">(<?= htmlspecialchars($booking['transaction_code']) ?>)</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Booked Seats Table -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 fw-bold">
            <i class="bi bi-grid-3x3-gap me-2"></i> Danh sách ghế đã đặt
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60" class="ps-4">#</th>
                        <th>Mã vé</th>
                        <th>Số ghế</th>
                        <th>Loại ghế</th>
                        <th class="text-end pe-4">Giá vé</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tickets)): ?>
                        <?php foreach ($tickets as $idx => $ticket): ?>
                            <tr>
                                <td class="ps-4"><?= $idx + 1 ?></td>
                                <td>
                                    <span class="font-monospace text-muted"><?= htmlspecialchars($ticket['ticket_code'] ?? '-') ?></span>
                                </td>
                                <td>
                                    <span class="badge text-bg-primary fs-6"><?= htmlspecialchars($ticket['seat_number']) ?></span>
                                </td>
                                <td>
                                    <?php
                                    $seatTypeBadge = match ($ticket['seat_type_name'] ?? '') {
                                        'VIP' => 'bg-warning text-dark',
                                        'Couple' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                    ?>
                                    <span class="badge <?= $seatTypeBadge ?>"><?= htmlspecialchars($ticket['seat_type_name'] ?? 'Standard') ?></span>
                                </td>
                                <td class="text-end pe-4 fw-semibold">
                                    <?= number_format((float)$ticket['ticket_price'], 0, ',', '.') ?> đ
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                Chưa có thông tin ghế đặt cho đơn hàng này.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Food Orders Table -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 fw-bold">
            <i class="bi bi-cup-straw me-2"></i> Đồ ăn & thức uống đi kèm
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60" class="ps-4">#</th>
                        <th>Tên món / Combo</th>
                        <th>Size</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-end">Đơn giá</th>
                        <th class="text-end pe-4">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($foodOrders)): ?>
                        <?php foreach ($foodOrders as $idx => $fo): ?>
                            <?php $subtotal = (float) $fo['price_at_booking'] * (int) $fo['quantity']; ?>
                            <tr>
                                <td class="ps-4"><?= $idx + 1 ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($fo['food_name']) ?></td>
                                <td><span class="badge text-bg-light border"><?= htmlspecialchars($fo['variant_size'] ?: '-') ?></span></td>
                                <td class="text-center fw-bold"><?= (int) $fo['quantity'] ?></td>
                                <td class="text-end"><?= number_format((float) $fo['price_at_booking'], 0, ',', '.') ?> đ</td>
                                <td class="text-end pe-4 fw-semibold"><?= number_format($subtotal, 0, ',', '.') ?> đ</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Booking này không có đồ ăn hoặc thức uống đi kèm.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row justify-content-end mb-4">
    <div class="col-lg-5 col-xl-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-calculator me-2"></i> Tổng kết thanh toán
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tiền vé:</span>
                    <span class="fw-semibold"><?= number_format((float) $ticketTotal, 0, ',', '.') ?> đ</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Tiền đồ ăn:</span>
                    <span class="fw-semibold"><?= number_format((float) $foodTotal, 0, ',', '.') ?> đ</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Tổng thanh toán:</span>
                    <span class="fw-bold text-danger fs-5">
                        <?= number_format((float) ($booking['total_amount'] ?? 0), 0, ',', '.') ?> đ
                    </span>
                </div>
                <?php if (abs(((float) $ticketTotal + (float) $foodTotal) - (float) ($booking['total_amount'] ?? 0)) > 0.01): ?>
                    <div class="alert alert-warning py-2 px-3 mt-3 mb-0 small">
                        Tổng chi tiết không khớp dữ liệu booking cũ. Có thể booking được tạo trước khi lưu ticket hoặc food order.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
