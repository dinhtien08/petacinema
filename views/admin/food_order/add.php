<div class="card p-4">

    <div class="d-flex align-items-center justify-content-between mb-3">

        <h4 class="mb-0">Thêm đơn món ăn</h4>

        <a href="?action=food_order_list" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>

    </div>

    <p class="text-muted">Bước 1: Chọn món ăn &rarr; Bước 2: Chọn biến thể &rarr; Bước 3: Nhập thông tin đơn.</p>

    <?php if ($stage === 1): ?>

        <form action="?action=food_order_add" method="get" class="row g-3" style="max-width: 500px;">
            <input type="hidden" name="action" value="food_order_add">

            <div class="col-12">
                <label class="form-label">Chọn món ăn <span class="text-danger">*</span></label>
                <select name="food_id" class="form-select">
                    <option value="">-- Chọn món ăn --</option>
                    <?php foreach ($foods as $f): ?>
                        <option value="<?= (int) $f['id'] ?>"><?= h($f['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-danger">Tiếp tục</button>
            </div>
        </form>

    <?php elseif ($stage === 2): ?>

        <p><strong>Món ăn đã chọn:</strong> <?= h($food['name']) ?>
            &nbsp;<a href="?action=food_order_add">(Đổi món ăn)</a>
        </p>

        <?php if (empty($variants)): ?>

            <div class="alert alert-warning">Món này chưa có biến thể nào. Vui lòng chọn món khác hoặc thêm biến thể trước.</div>
            <a href="?action=food_variant_add&food_id=<?= (int) $food['id'] ?>" class="btn btn-outline-primary">Thêm biến thể cho món này</a>

        <?php else: ?>

            <form action="?action=food_order_add" method="get" class="row g-3" style="max-width: 500px;">
                <input type="hidden" name="action" value="food_order_add">
                <input type="hidden" name="food_id" value="<?= (int) $food['id'] ?>">

                <div class="col-12">
                    <label class="form-label">Chọn biến thể <span class="text-danger">*</span></label>
                    <select name="variant_id" class="form-select">
                        <option value="">-- Chọn biến thể --</option>
                        <?php foreach ($variants as $v): ?>
                            <option value="<?= (int) $v['id'] ?>"><?= h($v['size']) ?> - <?= number_format((float) $v['price']) ?>đ (Tồn: <?= (int) $v['stock'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-danger">Tiếp tục</button>
                </div>
            </form>

        <?php endif; ?>

    <?php else: ?>

        <p>
            <strong>Món ăn:</strong> <?= h($food['name']) ?> &mdash;
            <strong>Biến thể:</strong> <?= h($variant['size']) ?> (<?= number_format((float) $variant['price']) ?>đ)
            &nbsp;<a href="?action=food_order_add&food_id=<?= (int) $food['id'] ?>">(Đổi biến thể)</a>
        </p>

        <form action="?action=food_order_addPost" method="post" class="row g-3" style="max-width: 500px;">

            <input type="hidden" name="food_id" value="<?= (int) $food['id'] ?>">
            <input type="hidden" name="food_variant_id" value="<?= (int) $variant['id'] ?>">

            <div class="col-12">
                <label class="form-label">Booking ID <span class="text-danger">*</span></label>
                <input type="number" min="1" name="booking_id" class="form-control <?= !empty($errors['booking_id']) ? 'is-invalid' : '' ?>" value="<?= old_value($old, 'booking_id') ?>">
                <?= field_error($errors, 'booking_id') ?>
            </div>

            <div class="col-12">
                <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                <input type="number" min="1" step="1" name="quantity" class="form-control <?= !empty($errors['quantity']) ? 'is-invalid' : '' ?>" value="<?= old_value($old, 'quantity', 1) ?>">
                <?= field_error($errors, 'quantity') ?>
            </div>

            <div class="col-12">
                <label class="form-label">Giá tại thời điểm đặt (đ) <span class="text-danger">*</span></label>
                <input type="number" min="0" step="0.01" name="price_at_booking" class="form-control <?= !empty($errors['price_at_booking']) ? 'is-invalid' : '' ?>" value="<?= old_value($old, 'price_at_booking', $variant['price']) ?>">
                <?= field_error($errors, 'price_at_booking') ?>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-check-lg"></i> Lưu
                </button>
                <a href="?action=food_order_list" class="btn btn-outline-secondary">Hủy</a>
            </div>

        </form>

    <?php endif; ?>

</div>
