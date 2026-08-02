<?php if (!empty($errors['general'])) : ?>
    <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Khách hàng <span class="text-danger">*</span></label>
        <select name="user_id" class="form-select <?= isset($errors['user_id']) ? 'is-invalid' : '' ?>">
            <option value="">-- Chọn khách hàng --</option>
            <?php foreach ($users as $user) : ?>
                <option value="<?= (int) $user['id'] ?>" <?= (string) ($data['user_id'] ?? '') === (string) $user['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($user['fullname']) ?> (<?= htmlspecialchars($user['email']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <div class="invalid-feedback"><?= $errors['user_id'] ?? '' ?></div>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Suất chiếu <span class="text-danger">*</span></label>
        <select name="showtime_id" class="form-select <?= isset($errors['showtime_id']) ? 'is-invalid' : '' ?>">
            <option value="">-- Chọn suất chiếu --</option>
            <?php foreach ($showtimes as $showtime) : ?>
                <option value="<?= (int) $showtime['id'] ?>" <?= (string) ($data['showtime_id'] ?? '') === (string) $showtime['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($showtime['movie_title']) ?> - <?= htmlspecialchars($showtime['room_name']) ?>
                    - <?= date('d/m/Y H:i', strtotime($showtime['start_time'])) ?>
                    (<?= number_format((float) $showtime['base_price'], 0, ',', '.') ?>đ)
                </option>
            <?php endforeach; ?>
        </select>
        <div class="invalid-feedback"><?= $errors['showtime_id'] ?? '' ?></div>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Ghế test <span class="text-danger">*</span></label>
        <input type="text" name="seat_numbers"
               class="form-control <?= isset($errors['seat_numbers']) ? 'is-invalid' : '' ?>"
               value="<?= htmlspecialchars($data['seat_numbers'] ?? '') ?>"
               placeholder="Ví dụ: A1,A2">
        <div class="form-text">Nhập mã ghế cách nhau bằng dấu phẩy. Ghế phải thuộc phòng và chưa được đặt.</div>
        <div class="invalid-feedback"><?= $errors['seat_numbers'] ?? '' ?></div>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
        <select name="status" class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>">
            <option value="pending" <?= ($data['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
            <option value="paid" <?= ($data['status'] ?? '') === 'paid' ? 'selected' : '' ?>>Đã thanh toán</option>
            <option value="cancelled" <?= ($data['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
        </select>
        <div class="invalid-feedback"><?= $errors['status'] ?? '' ?></div>
    </div>
</div>

<div class="card border mb-3">
    <div class="card-header bg-light fw-semibold">
        <i class="bi bi-cup-straw me-1"></i> Đồ ăn test (không bắt buộc)
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Món</th>
                        <th>Size</th>
                        <th class="text-end">Đơn giá</th>
                        <th class="text-center" style="width: 140px">Số lượng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($foodVariants)) : ?>
                        <?php foreach ($foodVariants as $variant) : ?>
                            <?php $qty = (int) (($data['food_quantities'] ?? [])[$variant['id']] ?? 0); ?>
                            <tr>
                                <td class="ps-3 fw-semibold"><?= htmlspecialchars($variant['food_name']) ?></td>
                                <td><?= htmlspecialchars($variant['size'] ?: '-') ?></td>
                                <td class="text-end"><?= number_format((float) $variant['price'], 0, ',', '.') ?> đ</td>
                                <td>
                                    <input type="number" min="0" max="99"
                                           name="food_quantities[<?= (int) $variant['id'] ?>]"
                                           value="<?= $qty ?>" class="form-control form-control-sm text-center">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="4" class="text-center text-muted py-3">Chưa có đồ ăn đang bán.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="alert alert-light border mb-0">
    <i class="bi bi-calculator me-1"></i>
    Tổng tiền được tự động tính bằng tiền vé cộng tiền đồ ăn. Giá món được lưu tại thời điểm tạo booking.
</div>
