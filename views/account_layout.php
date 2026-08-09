<style>
    .account-layout { max-width: 1000px; margin: 0 auto 3rem; }
    .account-sidebar { border-right: 1px solid #e2e8f0; padding-right: 1.5rem; }
    .account-menu-list { list-style: none; padding: 0; margin: 0; }
    .account-menu-item { margin-bottom: 0.5rem; }
    .account-menu-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: 12px; color: #64748b; font-weight: 600; text-decoration: none; transition: all 0.2s ease; }
    .account-menu-link:hover { background-color: #f8fafc; color: #0f172a; }
    .account-menu-link.active { background-color: #fff1f2; color: #e50914; }
    .account-content { padding-left: 1.5rem; }
    
    @media (max-width: 767.98px) {
        .account-sidebar { border-right: none; border-bottom: 1px solid #e2e8f0; padding-right: 0; padding-bottom: 1.5rem; margin-bottom: 1.5rem; }
        .account-content { padding-left: 0; }
        .account-menu-list { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .account-menu-item { margin-bottom: 0; flex-grow: 1; }
        .account-menu-link { justify-content: center; }
    }
</style>

<div class="account-layout">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-4 col-lg-3 account-sidebar">
            <h4 class="mb-4 fw-bold">Quản lý tài khoản</h4>
            
            <ul class="account-menu-list">
                <?php $currentAction = $_GET['action'] ?? 'account'; ?>
                <li class="account-menu-item">
                    <a href="<?= BASE_URL ?>?action=account" class="account-menu-link <?= $currentAction === 'account' ? 'active' : '' ?>">
                        <i class="bi bi-person-circle fs-5"></i>
                        Thông tin tài khoản
                    </a>
                </li>
                <li class="account-menu-item">
                    <a href="<?= BASE_URL ?>?action=change_password" class="account-menu-link <?= $currentAction === 'change_password' ? 'active' : '' ?>">
                        <i class="bi bi-shield-lock fs-5"></i>
                        Đổi mật khẩu
                    </a>
                </li>
                <li class="account-menu-item">
                    <a href="<?= BASE_URL ?>?action=my_tickets" class="account-menu-link <?= $currentAction === 'my_tickets' ? 'active' : '' ?>">
                        <i class="bi bi-ticket-perforated fs-5"></i>
                        Vé của tôi
                    </a>
                </li>
                <li class="account-menu-item mt-2">
                    <a href="<?= BASE_URL ?>?action=logout" class="account-menu-link text-danger">
                        <i class="bi bi-box-arrow-right fs-5"></i>
                        Đăng xuất
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Content -->
        <div class="col-md-8 col-lg-9 account-content">
            <?php
            if (isset($accountView) && file_exists(PATH_VIEW . $accountView . '.php')) {
                require_once PATH_VIEW . $accountView . '.php';
            } else {
                echo '<div class="alert alert-danger">Không tìm thấy nội dung.</div>';
            }
            ?>
        </div>
    </div>
</div>
