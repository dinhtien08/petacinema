<div class="card p-4">

    <div class="d-flex align-items-center justify-content-between mb-3">

        <h4 class="mb-0">Thêm size cho món: <?= h($food['name']) ?></h4>

        <a href="?action=food_variant_list&food_id=<?= (int) $food['id'] ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>

    </div>

    <form action="?action=food_variant_addPost" method="post" class="row g-3" style="max-width: 500px;">

        <input type="hidden" name="food_id" value="<?= (int) $food['id'] ?>">

        <div class="col-12">
            <label class="form-label">Kích cỡ <span class="text-danger">*</span></label>
            <input type="text" name="size" class="form-control <?= !empty($errors['size']) ? 'is-invalid' : '' ?>" value="<?= old_value($old, 'size') ?>" maxlength="10" placeholder="S, M, L...">
            <?= field_error($errors, 'size') ?>
        </div>

        <div class="col-12">
            <label class="form-label">Giá (đ) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0" name="price" class="form-control <?= !empty($errors['price']) ? 'is-invalid' : '' ?>" value="<?= old_value($old, 'price') ?>">
            <?= field_error($errors, 'price') ?>
        </div>

        <div class="col-12">
            <label class="form-label">Tồn kho <span class="text-danger">*</span></label>
            <input type="number" step="1" min="0" name="stock" class="form-control <?= !empty($errors['stock']) ? 'is-invalid' : '' ?>" value="<?= old_value($old, 'stock', 0) ?>">
            <?= field_error($errors, 'stock') ?>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-check-lg"></i> Lưu
            </button>
            <a href="?action=food_variant_list&food_id=<?= (int) $food['id'] ?>" class="btn btn-outline-secondary">Hủy</a>
        </div>

    </form>

</div>
