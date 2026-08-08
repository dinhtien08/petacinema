<?php if (empty($booking)): ?>
    <!-- Search / Scanner State -->
    <div class="container-fluid px-4 py-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bi bi-box-seam me-2 text-primary"></i> Giao đồ ăn
                </h4>
                <p class="text-muted mb-0">
                    Nhập hoặc quét mã Booking để bắt đầu giao đồ ăn & thức uống.
                </p>
            </div>
            <a href="?action=staff_booking_list" class="btn btn-outline-secondary">
                <i class="bi bi-list-ul me-1"></i> Danh sách Booking
            </a>
        </div>

        <!-- Hiển thị thông báo nếu có -->
        <?php if (!empty($flash)): ?>
            <div class="row justify-content-center mb-3">
                <div class="col-lg-5 col-md-7">
                    <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= htmlspecialchars($flash['message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5 text-center">
                        <div class="scanner-icon-wrapper mb-3 text-warning" style="font-size: 3rem;">
                            <i class="bi bi-upc-scan animate-pulse"></i>
                        </div>

                        <h5 class="card-title fw-bold mb-2">Nhập mã Booking</h5>
                        <p class="text-muted small mb-4">
                            Đặt con trỏ vào ô nhập liệu bên dưới và quét mã bằng máy quét Barcode/QR (USB),
                            hoặc gõ trực tiếp mã Booking rồi nhấn Enter.
                        </p>

                        <!-- Scanner / Manual entry form -->
                        <form action="<?= BASE_URL ?>" method="GET" id="usb-scanner-form">
                            <input type="hidden" name="action" value="staff_food_delivery">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-keyboard text-muted"></i>
                                </span>
                                <input
                                    type="text"
                                    name="code"
                                    id="scanner-input"
                                    class="form-control border-start-0 border-end-0 fs-5"
                                    placeholder="Mã Booking (ví dụ: PET2026...)"
                                    autofocus
                                    autocomplete="off"
                                    required
                                >
                                <button class="btn btn-warning px-4 text-dark" type="submit">
                                    <i class="bi bi-arrow-right-short fs-4"></i>
                                </button>
                            </div>
                            <div class="form-text text-start text-muted mt-2">
                                <i class="bi bi-info-circle me-1"></i> Thiết bị quét tự động nhấn Enter sau khi nhận diện mã.
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Styles for Pulse Animation -->
    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .6; transform: scale(1.05); }
        }
        .animate-pulse {
            animation: pulse 2s infinite ease-in-out;
            display: inline-block;
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const scannerInput = document.getElementById('scanner-input');

        if (scannerInput) {
            scannerInput.focus();
            document.addEventListener('click', function(e) {
                const isClickInsideInput = scannerInput.contains(e.target);
                const isClickInsideButton = e.target.closest('button') || e.target.closest('a');
                if (!isClickInsideInput && !isClickInsideButton) {
                    scannerInput.focus();
                }
            });
        }
    });
    </script>

<?php else: ?>
    <!-- Details & Confirm State -->
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    Chi tiết Giao đồ ăn: <?= htmlspecialchars($booking['booking_code'] ?? '') ?>
                </h4>
                <p class="text-muted mb-0">
                    Xác nhận giao đồ ăn kèm theo của khách hàng.
                </p>
            </div>
            <a href="?action=staff_food_delivery" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        <!-- Hiển thị thông báo thành công/lỗi -->
        <?php if (!empty($flash)): ?>
            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show mb-4" role="alert">
                <i class="bi <?= $flash['type'] === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> me-2"></i>
                <?= htmlspecialchars($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
            </div>
        <?php endif; ?>

        <!-- Info Card -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="bi bi-receipt me-2"></i> Thông tin đặt vé
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
                                <td class="text-muted">Phim:</td>
                                <td class="fw-semibold"><?= htmlspecialchars($booking['movie_title'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Suất chiếu:</td>
                                <td><?= !empty($booking['start_time']) ? date('d/m/Y H:i', strtotime($booking['start_time'])) : '-' ?> (<?= htmlspecialchars($booking['room_name'] ?? '') ?>)</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Delivery Action Card -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100 bg-light">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="bi bi-box-seam me-2"></i> Trạng thái giao hàng
                        </h5>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center align-items-center py-4">
                        <?php if ($hasFoodOrders): ?>
                            <?php if (!$allFoodDelivered): ?>
                                <div class="text-center mb-3">
                                    <i class="bi bi-hourglass-split text-warning" style="font-size: 2.5rem;"></i>
                                    <p class="text-muted mt-2 mb-0">Có đồ ăn cần được giao cho khách hàng.</p>
                                </div>
                                <a href="?action=staff_food_delivery_confirm&booking_id=<?= (int)$booking['id'] ?>&redirect=food_delivery" class="btn btn-warning btn-lg px-5 text-dark fw-bold shadow-sm">
                                    <i class="bi bi-check2-all me-2"></i> Xác nhận Giao đồ ăn
                                </a>
                            <?php else: ?>
                                <div class="text-center mb-3 text-success">
                                    <i class="bi bi-check-circle-fill" style="font-size: 3rem;"></i>
                                    <h5 class="fw-bold mt-2">✓ Food Delivered</h5>
                                </div>
                                <div class="text-center small text-muted border-top pt-2 w-100" style="max-width: 300px;">
                                    <div><strong>Thời gian giao:</strong> <?= $deliveryTime ?></div>
                                    <div><strong>Nhân viên:</strong> <?= htmlspecialchars($deliveredBy ?? '-') ?></div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center text-muted">
                                <i class="bi bi-info-circle fs-2"></i>
                                <p class="mt-2 mb-0">Đơn hàng này không đi kèm đồ ăn & thức uống.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Food Orders Table -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-cup-straw me-2"></i> Chi tiết đồ ăn & combo đã gọi
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="60" class="ps-4">STT</th>
                                <th>Tên món / Combo</th>
                                <th>Size</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Đơn giá</th>
                                <th class="text-end">Thành tiền</th>
                                <th class="text-end pe-4">Trạng thái</th>
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
                                        <td class="text-end"><?= number_format($subtotal, 0, ',', '.') ?> đ</td>
                                        <td class="text-end pe-4">
                                            <?php if (($fo['delivery_status'] ?? 'pending') === 'delivered'): ?>
                                                <span class="badge bg-success" title="Nhân viên: <?= htmlspecialchars($fo['delivered_by_name'] ?? '-') ?>">
                                                    ✓ Đã giao
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Chờ giao</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        Không có đồ ăn đi kèm.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
