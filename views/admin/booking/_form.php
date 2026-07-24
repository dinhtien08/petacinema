<div class="row">

    <!-- Khách hàng -->
    <div class="col-md-6 mb-3">
        <label class="form-label">Khách hàng <span class="text-danger">*</span></label>

        <select
            name="user_id"
            class="form-select <?= isset($errors['user_id']) ? 'is-invalid' : '' ?>">

            <option value="">-- Chọn khách hàng --</option>

            <?php foreach ($users as $user) : ?>
                <option value="<?= $user['id'] ?>" <?= (string) ($data['user_id'] ?? '') === (string) $user['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($user['fullname']) ?> (<?= htmlspecialchars($user['email']) ?>)
                </option>
            <?php endforeach; ?>

        </select>

        <div class="invalid-feedback">
            <?= $errors['user_id'] ?? '' ?>
        </div>
    </div>

    <!-- Suất chiếu -->
    <div class="col-md-6 mb-3">
        <label class="form-label">Suất chiếu <span class="text-danger">*</span></label>

        <select
            name="showtime_id"
            class="form-select <?= isset($errors['showtime_id']) ? 'is-invalid' : '' ?>">

            <option value="">-- Chọn suất chiếu --</option>

            <?php foreach ($showtimes as $showtime) : ?>
                <option value="<?= $showtime['id'] ?>" <?= (string) ($data['showtime_id'] ?? '') === (string) $showtime['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($showtime['movie_title']) ?> - <?= htmlspecialchars($showtime['room_name']) ?>
                    - <?= date('d/m/Y H:i', strtotime($showtime['start_time'])) ?>
                    (<?= number_format((float) $showtime['base_price']) ?>đ)
                </option>
            <?php endforeach; ?>

        </select>

        <div class="invalid-feedback">
            <?= $errors['showtime_id'] ?? '' ?>
        </div>
    </div>

    <!-- Tổng tiền -->
    <div class="col-md-6 mb-3">
        <label class="form-label">Tổng tiền (đ) <span class="text-danger">*</span></label>

        <input
            type="number"
            step="0.01"
            min="0"
            name="total_amount"
            class="form-control <?= isset($errors['total_amount']) ? 'is-invalid' : '' ?>"
            value="<?= htmlspecialchars($data['total_amount'] ?? '') ?>">

        <div class="invalid-feedback">
            <?= $errors['total_amount'] ?? '' ?>
        </div>
    </div>

    <!-- Trạng thái -->
    <div class="col-md-6 mb-3">
        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>

        <select
            name="status"
            class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>">

            <option value="pending" <?= ($data['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
            <option value="paid" <?= ($data['status'] ?? '') === 'paid' ? 'selected' : '' ?>>Đã thanh toán</option>
            <option value="cancelled" <?= ($data['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>

        </select>

        <div class="invalid-feedback">
            <?= $errors['status'] ?? '' ?>
        </div>
    </div>

</div>
