<div class="card p-4">

    <div class="d-flex align-items-center justify-content-between mb-3">

        <h4 class="mb-0">Quản lý món ăn (Foods)</h4>

        <div class="d-flex gap-2">
            <a href="?action=food_add" class="btn btn-danger">
                <i class="bi bi-plus-lg"></i> Thêm mới
            </a>
        </div>

    </div>

    <?php require PATH_VIEW . 'admin/layout/flash.php'; ?>

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
                    <th class="text-center" style="width: 260px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($foods)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">Chưa có món ăn nào.</td>
                    </tr>
                <?php else: ?>
                        <?php $stt = 1; ?>
                        <?php foreach ($foods as $food): ?>
                        <tr>
                            <td><?= $stt++ ?></td>
                            <td>
                                <?php if (!empty($food['image'])): ?>
                                    <img src="<?= h(BASE_ASSETS_UPLOADS . $food['image']) ?>" alt="" width="60" height="60" style="object-fit: cover; border-radius: 8px;">
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
                            <td class="text-center">
                                <a href="?action=food_variant_list&food_id=<?= (int) $food['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-list-ul"></i> Size
                                </a>
                                <a href="?action=food_edit&id=<?= (int) $food['id'] ?>" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i> Sửa
                                </a>
                                <form action="?action=food_delete" method="post" class="d-inline">
                                    <input type="hidden" name="id" value="<?= (int) $food['id'] ?>">
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
