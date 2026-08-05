<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $title ?? 'PETACINEMA - Rạp Chiếu Phim Đẳng Cấp' ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --peta-bg-dark: #0f172a;
            --peta-bg-darker: #0b0f19;
            --peta-bg-card: #1e293b;
            --peta-border: rgba(255, 255, 255, 0.08);
            --peta-primary: #ef4444; /* Đỏ */
            --peta-secondary: #f97316; /* Cam */
            --peta-text-main: #ffffff; /* Trắng */
            --peta-text-muted: #d1d5db; /* Xám nhạt */
            --peta-gold: #f59e0b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--peta-bg-dark);
            color: var(--peta-text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Sticky Header */
        .peta-navbar {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--peta-border);
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .peta-logo {
            height: 42px;
            width: auto;
        }

        .nav-link-peta {
            color: var(--peta-text-muted);
            font-weight: 600;
            font-size: 0.95rem;
            padding: 8px 16px !important;
            transition: all 0.2s ease;
        }

        .nav-link-peta:hover,
        .nav-link-peta.active {
            color: var(--peta-primary);
        }

        /* Standard Buttons */
        .btn-peta-primary {
            background-color: var(--peta-primary);
            color: #ffffff;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 9px 20px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }

        .btn-peta-primary:hover {
            background-color: #dc2626;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.45);
        }

        .btn-peta-secondary {
            background-color: var(--peta-secondary);
            color: #ffffff;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 9px 20px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
        }

        .btn-peta-secondary:hover {
            background-color: #ea580c;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.45);
        }

        .btn-peta-outline {
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 18px;
            transition: all 0.2s ease;
        }

        .btn-peta-outline:hover {
            border-color: var(--peta-primary);
            background: rgba(239, 68, 68, 0.1);
            color: #ffffff;
        }

        /* Account Dropdown */
        .user-dropdown-btn {
            background: var(--peta-bg-card);
            border: 1px solid var(--peta-border);
            color: #ffffff;
            border-radius: 30px;
            padding: 6px 16px;
        }

        .user-dropdown-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }

        .dropdown-menu-dark-custom {
            background: #1e293b;
            border: 1px solid var(--peta-border);
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.6);
        }

        .dropdown-menu-dark-custom .dropdown-item {
            color: var(--peta-text-muted);
            font-size: 0.9rem;
            padding: 9px 16px;
        }

        .dropdown-menu-dark-custom .dropdown-item:hover {
            background: rgba(239, 68, 68, 0.15);
            color: #ffffff;
        }

        /* Unified Cards */
        .peta-card {
            background: var(--peta-bg-card);
            border: 1px solid var(--peta-border);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .peta-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.5);
            border-color: rgba(239, 68, 68, 0.4);
        }

        /* Footer */
        .peta-footer {
            background: var(--peta-bg-darker);
            border-top: 1px solid var(--peta-border);
            margin-top: auto;
            padding: 50px 0 25px;
            color: var(--peta-text-muted);
            font-size: 0.9rem;
        }

        .footer-title {
            color: #ffffff;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <!-- Sticky Header -->
    <nav class="navbar navbar-expand-lg peta-navbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>">
                <img src="<?= BASE_ASSETS_UPLOADS ?>logo/logo.png" alt="PETACINEMA" class="peta-logo me-2">
                <span class="fw-bold fs-4 text-white">PETA<span class="text-danger">CINEMA</span></span>
            </a>

            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPeta">
                <i class="bi bi-list fs-2"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarPeta">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link nav-link-peta <?= ($_GET['action'] ?? '/') === '/' ? 'active' : '' ?>" href="<?= BASE_URL ?>">
                            <i class="bi bi-house-door me-1"></i> Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-peta <?= ($_GET['action'] ?? '') === 'now_showing' ? 'active' : '' ?>" href="?action=now_showing">
                            <i class="bi bi-film me-1"></i> Phim đang chiếu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-peta <?= ($_GET['action'] ?? '') === 'upcoming' ? 'active' : '' ?>" href="?action=upcoming">
                            <i class="bi bi-calendar-event me-1"></i> Phim sắp chiếu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-peta <?= ($_GET['action'] ?? '') === 'my_tickets' ? 'active' : '' ?>" href="?action=my_tickets">
                            <i class="bi bi-ticket-detailed me-1"></i> Xem vé
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-peta <?= ($_GET['action'] ?? '') === 'news' ? 'active' : '' ?>" href="?action=news">
                            <i class="bi bi-newspaper me-1"></i> Tin tức
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-peta <?= ($_GET['action'] ?? '') === 'promotions' ? 'active' : '' ?>" href="?action=promotions">
                            <i class="bi bi-gift me-1"></i> Ưu đãi
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <?php if (isset($_SESSION['user'])): ?>
                        <div class="dropdown">
                            <button class="btn user-dropdown-btn d-flex align-items-center gap-2 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle fs-5 text-danger"></i>
                                <span class="fw-semibold"><?= h($_SESSION['username'] ?? $_SESSION['fullname'] ?? 'User') ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark-custom">
                                <li>
                                    <div class="dropdown-header text-muted small">
                                        Quyền: <span class="badge bg-danger ms-1"><?= strtoupper(h($_SESSION['role'] ?? 'USER')) ?></span>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider border-secondary"></li>
                                <li>
                                    <a class="dropdown-item" href="?action=profile">
                                        <i class="bi bi-person me-2 text-info"></i> Thông tin cá nhân
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="?action=change_password">
                                        <i class="bi bi-key me-2 text-warning"></i> Đổi mật khẩu
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="?action=my_tickets">
                                        <i class="bi bi-ticket-perforated me-2 text-success"></i> Vé của tôi
                                    </a>
                                </li>
                                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                                    <li>
                                        <a class="dropdown-item text-danger" href="?action=dashboard">
                                            <i class="bi bi-speedometer2 me-2"></i> Admin Dashboard
                                        </a>
                                    </li>
                                <?php elseif (($_SESSION['role'] ?? '') === 'staff'): ?>
                                    <li>
                                        <a class="dropdown-item text-warning" href="?action=staff_dashboard">
                                            <i class="bi bi-person-badge me-2"></i> Staff Dashboard
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider border-secondary"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="?action=logout">
                                        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="?action=login" class="btn btn-peta-outline btn-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
                        </a>
                        <a href="?action=register" class="btn btn-peta-primary btn-sm">
                            <i class="bi bi-person-plus me-1"></i> Đăng ký
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="pb-5">
        <?php
        if (isset($view)) {
            require_once PATH_VIEW . $view . '.php';
        }
        ?>
    </main>

    <!-- Modern Dark Footer -->
    <footer class="peta-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?= BASE_ASSETS_UPLOADS ?>logo/logo.png" alt="PETACINEMA" class="peta-logo me-2">
                        <span class="fw-bold fs-4 text-white">PETA<span class="text-danger">CINEMA</span></span>
                    </div>
                    <p class="small text-muted mb-3">
                        Trải nghiệm điện ảnh đỉnh cao với âm thanh sống động và màn hình sắc nét tiêu chuẩn quốc tế tại PETACINEMA.
                    </p>
                    <div class="d-flex gap-3 fs-5">
                        <a href="#" class="text-muted text-hover-white"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-muted text-hover-white"><i class="bi bi-youtube"></i></a>
                        <a href="#" class="text-muted text-hover-white"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-title">Quy Định & Điều Khoản</h5>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                        <li><a href="#" class="text-decoration-none text-muted">Giới thiệu rạp</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Điều khoản chung</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Chính sách bảo mật</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Hệ thống rạp chiếu</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-title">Chăm Sóc Khách Hàng</h5>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                        <li><i class="bi bi-telephone-fill me-2 text-danger"></i> Hotline: 1900 1234</li>
                        <li><i class="bi bi-envelope-fill me-2 text-danger"></i> Email: cskh@petacinema.com</li>
                        <li><i class="bi bi-clock-fill me-2 text-danger"></i> Giờ làm việc: 8:00 - 22:00</li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h5 class="footer-title">Công Nghệ</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-secondary">IMAX</span>
                        <span class="badge bg-secondary">3D</span>
                        <span class="badge bg-secondary">2D</span>
                        <span class="badge bg-secondary">Gold Class</span>
                    </div>
                </div>
            </div>

            <hr class="border-secondary my-4">

            <div class="text-center small text-muted">
                © <?= date('Y') ?> PETACINEMA. Tất cả các quyền được bảo lưu.
            </div>
        </div>
    </footer>

</body>

</html>