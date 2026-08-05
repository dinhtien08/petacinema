<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="peta-card p-4">
                <div class="text-center mb-4">
                    <div class="display-4 text-danger mb-2">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <h3 class="fw-bold text-white mb-1"><?= h($user['fullname']) ?></h3>
                    <span class="badge bg-danger text-uppercase px-3 py-1"><?= h($user['role']) ?></span>
                </div>

                <?php $flash = get_flash(); ?>
                <?php if ($flash): ?>
                    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> mb-4">
                        <?= h($flash['message']) ?>
                    </div>
                <?php endif; ?>

                <div class="list-group list-group-flush rounded-3 overflow-hidden mb-4 border border-secondary">
                    <div class="list-group-item bg-dark text-white d-flex justify-content-between py-3 border-secondary">
                        <span class="text-muted"><i class="bi bi-envelope me-2 text-danger"></i>Email:</span>
                        <strong class="text-white"><?= h($user['email']) ?></strong>
                    </div>
                    <div class="list-group-item bg-dark text-white d-flex justify-content-between py-3 border-secondary">
                        <span class="text-muted"><i class="bi bi-person me-2 text-info"></i>Họ tên:</span>
                        <strong class="text-white"><?= h($user['fullname']) ?></strong>
                    </div>
                    <div class="list-group-item bg-dark text-white d-flex justify-content-between py-3 border-secondary">
                        <span class="text-muted"><i class="bi bi-calendar-check me-2 text-warning"></i>Ngày tham gia:</span>
                        <strong class="text-white"><?= date('d/m/Y', strtotime($user['created_at'] ?? 'now')) ?></strong>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="?action=change_password" class="btn btn-peta-secondary">
                        <i class="bi bi-key me-2"></i> Đổi mật khẩu
                    </a>
                    <a href="?action=my_tickets" class="btn btn-peta-outline">
                        <i class="bi bi-ticket-perforated me-2"></i> Xem vé đã đặt
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
