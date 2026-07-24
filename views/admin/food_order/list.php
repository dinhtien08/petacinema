<div class="card p-4">

    <div class="d-flex align-items-center justify-content-between mb-3">

        <h4 class="mb-0">Quản lý đơn món ăn (Food Orders)</h4>

        <div class="d-flex gap-2">
            <a href="?action=dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            <a href="?action=food_order_add" class="btn btn-danger">
                <i class="bi bi-plus-lg"></i> Thêm mới
            </a>
        </div>

    </div>

    <?php require PATH_VIEW . 'admin/layout/flash.php'; ?>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Booking</th>
                    <th>Món ăn</th>
                    <th>Kích cỡ</th>
                    <th>Số lượng</th>
                    <th>Giá tại thời điểm đặt</th>
                    <th class="text-center" style="width: 200px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">Chưa có đơn món ăn nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= h($order['id']) ?></td>
                            <td><?= h($order['booking_code'] ?? ('#' . $order['booking_id'])) ?></td>
                            <td><?= h($order['food_name']) ?></td>
                            <td><?= h($order['variant_size']) ?></td>
                            <td><?= (int) $order['quantity'] ?></td>
                            <td><?= number_format((float) $order['price_at_booking']) ?>đ</td>
                            <td class="text-center">
                                <a href="?action=food_order_edit&id=<?= (int) $order['id'] ?>" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i> Sửa
                                </a>
                                <form action="?action=food_order_delete" method="post" class="d-inline">
                                    <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa dữ liệu này?')">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
