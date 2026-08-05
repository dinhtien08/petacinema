<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="peta-card p-4">
                <h3 class="fw-bold text-white mb-2 text-center">
                    <i class="bi bi-shield-lock me-2 text-warning"></i>Đổi Mật Khẩu
                </h3>
                <p class="text-muted small text-center mb-4">Cập nhật mật khẩu bảo mật cho tài khoản của bạn</p>

                <?php 
                $errors = get_errors();
                $flash  = get_flash();
                ?>

                <?php if ($flash): ?>
                    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> mb-3">
                        <?= h($flash['message']) ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="?action=changePasswordPost" novalidate>

                    <!-- Mật khẩu hiện tại -->
                    <div class="mb-3">
                        <label class="form-label text-white small font-weight-bold">Mật khẩu hiện tại</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark text-white border-secondary">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <input 
                                type="password" 
                                name="old_password" 
                                class="form-control bg-dark text-white border-secondary" 
                                placeholder="Nhập mật khẩu cũ">
                        </div>
                        <?= field_error($errors, 'old_password') ?>
                    </div>

                    <!-- Mật khẩu mới -->
                    <div class="mb-3">
                        <label class="form-label text-white small font-weight-bold">Mật khẩu mới</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark text-white border-secondary">
                                <i class="bi bi-key-fill"></i>
                            </span>
                            <input 
                                type="password" 
                                name="new_password" 
                                class="form-control bg-dark text-white border-secondary" 
                                placeholder="Nhập mật khẩu mới (tối thiểu 8 ký tự)">
                        </div>
                        <?= field_error($errors, 'new_password') ?>
                    </div>

                    <!-- Xác nhận mật khẩu mới -->
                    <div class="mb-4">
                        <label class="form-label text-white small font-weight-bold">Xác nhận mật khẩu mới</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark text-white border-secondary">
                                <i class="bi bi-shield-check"></i>
                            </span>
                            <input 
                                type="password" 
                                name="confirm_new_password" 
                                class="form-control bg-dark text-white border-secondary" 
                                placeholder="Nhập lại mật khẩu mới">
                        </div>
                        <?= field_error($errors, 'confirm_new_password') ?>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="?action=profile" class="btn btn-peta-outline flex-fill text-center">Hủy</a>
                        <button type="submit" class="btn btn-peta-primary flex-fill">
                            <i class="bi bi-check-circle me-1"></i> Lưu mật khẩu
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
