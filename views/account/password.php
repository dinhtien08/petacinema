<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4 p-md-5">
        <h2 class="h4 mb-4 fw-bold">Đổi mật khẩu</h2>
        
        <?php
        $errors = get_errors();
        ?>

        <form method="post" action="<?= BASE_URL ?>?action=changePasswordPost" novalidate>
            <div class="mb-3">
                <label for="current_password" class="form-label text-secondary fw-semibold small">Mật khẩu hiện tại</label>
                <input type="password" class="form-control <?= isset($errors['current_password']) ? 'is-invalid' : '' ?>" id="current_password" name="current_password" required>
                <?php if (isset($errors['current_password'])): ?>
                    <div class="invalid-feedback"><?= h($errors['current_password']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="new_password" class="form-label text-secondary fw-semibold small">Mật khẩu mới</label>
                <input type="password" class="form-control <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>" id="new_password" name="new_password" required>
                <?php if (isset($errors['new_password'])): ?>
                    <div class="invalid-feedback"><?= h($errors['new_password']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="confirm_password" class="form-label text-secondary fw-semibold small">Xác nhận mật khẩu mới</label>
                <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" id="confirm_password" name="confirm_password" required>
                <?php if (isset($errors['confirm_password'])): ?>
                    <div class="invalid-feedback"><?= h($errors['confirm_password']) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-peta px-4 py-2 fw-bold">Lưu thay đổi</button>
        </form>
    </div>
</div>
