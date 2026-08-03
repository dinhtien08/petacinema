<div class="card p-4">

    <div class="d-flex align-items-center justify-content-between mb-3">

        <div>
            <h4 class="mb-1">Quản lý đơn món ăn</h4>
            <p class="text-muted mb-0">
                Theo dõi các món ăn và đồ uống được đặt kèm theo vé.
            </p>
        </div>

        <a href="?action=staff_dashboard" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            Quay lại Dashboard
        </a>

    </div>

    <?php require PATH_VIEW . 'staff/layout/flash.php'; ?>

    <div class="table-responsive">

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-light">

                <tr>

                    <th width="70">STT</th>

                    <th>Mã Booking</th>

                    <th>Tên món</th>

                    <th>Kích cỡ</th>

                    <th class="text-center">Số lượng</th>

                    <th class="text-end">Đơn giá</th>

                    <th class="text-end">Thành tiền</th>

                </tr>

            </thead>

            <tbody>

            <?php if (empty($orders)): ?>

                <tr>

                    <td colspan="7" class="text-center text-muted py-4">

                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                        Chưa có đơn món ăn nào.

                    </td>

                </tr>

            <?php else: ?>

                <?php $stt = 1; ?>

                <?php foreach ($orders as $order): ?>

                    <?php
                        $quantity = (int)$order['quantity'];
                        $price = (float)$order['price_at_booking'];
                        $subtotal = $quantity * $price;
                    ?>

                    <tr>

                        <td><?= $stt++ ?></td>

                        <td class="fw-semibold text-primary">
                            <?= h($order['booking_code'] ?? ('#'.$order['booking_id'])) ?>
                        </td>

                        <td>

                            <?= h($order['food_name']) ?>

                        </td>

                        <td>

                            <span class="badge bg-light text-dark border">

                                <?= h($order['variant_size']) ?>

                            </span>

                        </td>

                        <td class="text-center fw-bold">

                            <?= $quantity ?>

                        </td>

                        <td class="text-end">

                            <?= number_format($price) ?>đ

                        </td>

                        <td class="text-end fw-bold text-danger">

                            <?= number_format($subtotal) ?>đ

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>