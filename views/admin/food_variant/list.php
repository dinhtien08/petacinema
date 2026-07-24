<div class="card p-4">

    <div class="d-flex align-items-center justify-content-between mb-3">

        <h4 class="mb-0">Biến thể món: <?= h($food['name']) ?></h4>

        <div class="d-flex gap-2">
            <a href="?action=food_list" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            <a href="?action=food_variant_add&food_id=<?= (int) $food['id'] ?>" class="btn btn-danger">
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
                    <th>Kích cỡ</th>
                    <th>Giá</th>
                    <th>Tồn kho</th>
                    <th class="text-center" style="width: 200px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($variants)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">Món này chưa có biến thể nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($variants as $variant): ?>
                        <tr>
                            <td><?= h($variant['id']) ?></td>
                            <td><?= h($variant['size']) ?></td>
                            <td><?= number_format((float) $variant['price']) ?>đ</td>
                            <td><?= (int) $variant['stock'] ?></td>
                            <td class="text-center">
                                <a href="?action=food_variant_edit&id=<?= (int) $variant['id'] ?>" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i> Sửa
                                </a>
                                <form action="?action=food_variant_delete" method="post" class="d-inline">
                                    <input type="hidden" name="id" value="<?= (int) $variant['id'] ?>">
                                    <input type="hidden" name="food_id" value="<?= (int) $food['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
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
