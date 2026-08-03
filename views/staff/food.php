<div class="card p-4">

    <div class="d-flex align-items-center justify-content-between mb-3">

        <h4 class="mb-0">Quản lý món ăn (Foods)</h4>

    </div>

    <?php require PATH_VIEW . 'staff/layout/flash.php'; ?>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>STT</th>
                    <th>Hình ảnh</th>
                    <th>Tên món</th>
                    <th>Mô tả</th>
                    <th>Size</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($foods)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">Chưa có món ăn nào.</td>
                    </tr>
                <?php else: ?>
                        <?php $stt = 1; ?>
                        <?php foreach ($foods as $food): ?>
                        <tr>
                            <td><?= $stt++ ?></td>
                            <td>
                                <?php if (!empty($food['image'])): ?>
                                    <img src="<?= h(BASE_ASSETS_UPLOADS . $food['image']) ?>" alt="" width="130" height="130" style="object-fit: cover; border-radius: 8px;">
                                <?php else: ?>
                                    <span class="text-muted">Không có ảnh</span>
                                <?php endif; ?>
                            </td>
                            <td><?= h($food['name']) ?></td>
                            <td><?= h(mb_strimwidth($food['description'] ?? '', 0, 60, '...')) ?></td>
                            <td>
                                <?= (int) $food['variant_count'] ?> size
                                <?php if ($food['variant_count'] > 0): ?>
                                    <br>
                                    <small class="text-muted"><?= number_format((float) $food['min_price']) ?>đ - <?= number_format((float) $food['max_price']) ?>đ</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($food['status'] === 'active'): ?>
                                    <span class="badge bg-success">Đang bán</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Ngừng bán</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>