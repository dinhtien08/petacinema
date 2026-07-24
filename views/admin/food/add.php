<div class="card p-4">

    <div class="d-flex align-items-center justify-content-between mb-3">

        <h4 class="mb-0">Thêm món ăn</h4>

        <a href="?action=food_list" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>

    </div>

    <form action="?action=food_addPost" method="post" enctype="multipart/form-data" class="row g-3" style="max-width: 700px;">

        <div class="col-12">
            <label class="form-label">Tên món ăn <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control <?= !empty($errors['name']) ? 'is-invalid' : '' ?>" value="<?= old_value($old, 'name') ?>" maxlength="255">
            <?= field_error($errors, 'name') ?>
        </div>

        <div class="col-12">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" rows="4"><?= old_value($old, 'description') ?></textarea>
        </div>

        <div class="col-12">
            <label class="form-label">Hình ảnh</label>
            <input type="file" name="image" class="form-control <?= !empty($errors['image']) ? 'is-invalid' : '' ?>" accept="image/*">
            <?= field_error($errors, 'image') ?>
        </div>

        <div class="col-12">
            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
            <select name="status" class="form-select <?= !empty($errors['status']) ? 'is-invalid' : '' ?>">
                <option value="active" <?= ($old['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Đang bán</option>
                <option value="inactive" <?= ($old['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Ngừng bán</option>
            </select>
            <?= field_error($errors, 'status') ?>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-check-lg"></i> Lưu
            </button>
            <a href="?action=food_list" class="btn btn-outline-secondary">Hủy</a>
        </div>

    </form>

</div>
