<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4 p-md-5">
        <h2 class="h4 mb-4 fw-bold">Thông tin tài khoản</h2>
        
        <?php
        $errors = get_errors();
        $user = $_SESSION['user'] ?? [];
        ?>

        <form method="post" action="<?= BASE_URL ?>?action=accountPost" novalidate>
            <div class="mb-3">
                <label for="fullname" class="form-label text-secondary fw-semibold small">Họ và tên</label>
                <input type="text" class="form-control <?= isset($errors['fullname']) ? 'is-invalid' : '' ?>" id="fullname" name="fullname" value="<?= h($user['fullname'] ?? '') ?>" required>
                <?php if (isset($errors['fullname'])): ?>
                    <div class="invalid-feedback"><?= h($errors['fullname']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="email" class="form-label text-secondary fw-semibold small">Email</label>
                <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= h($user['email'] ?? '') ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?= h($errors['email']) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-peta px-4 py-2 fw-bold">Cập nhật thông tin</button>
        </form>
    </div>
</div>
