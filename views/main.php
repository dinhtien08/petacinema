<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= h($title ?? 'Petacinema - Rạp Chiếu Phim Đẳng Cấp') ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Montserrat, Poppins & Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@400;600;700;800&family=Poppins:wght@400;500;600;700;800&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --peta-bg-light: #f8f9fa;
            --peta-card-bg: #ffffff;
            --peta-card-border: #e2e8f0;
            --peta-accent: #e50914;
            --peta-accent-hover: #b00710;
            --peta-text-main: #0f172a;
            --peta-text-muted: #64748b;
        }

        body {
            background-color: var(--peta-bg-light);
            color: var(--peta-text-main);
            font-family: 'Poppins', 'Roboto', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        h1, h2, h3, h4, h5, h6, .navbar-brand {
            font-family: 'Poppins', 'Montserrat', sans-serif;
            font-weight: 700;
        }

        /* Header & Navbar Light Styling (Cinema Level) */
        .site-header-wrapper {
            background-color: #ffffff;
            border-bottom: 3px solid var(--peta-accent);
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.12);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .header-top-bar {
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #e9ecef !important;
            font-size: 0.83rem;
            font-family: 'Poppins', 'Roboto', sans-serif;
        }

        .navbar-cinema {
            background-color: #ffffff !important;
            border-bottom: none !important;
            box-shadow: none !important;
        }

        .brand-logo-img {
            max-height: 48px;
            width: auto;
            object-fit: contain;
        }

        .nav-link-cinema {
            color: #222222 !important;
            font-family: 'Poppins', 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 0.92rem;
            letter-spacing: 0.3px;
            padding: 8px 14px !important;
            position: relative;
            transition: color 0.25s ease;
        }

        .nav-link-cinema::after {
            content: '';
            position: absolute;
            bottom: 2px;
            left: 14px;
            right: 14px;
            height: 3px;
            background-color: var(--peta-accent);
            border-radius: 2px;
            transform: scaleX(0);
            transition: transform 0.25s ease;
        }

        .nav-link-cinema:hover {
            color: var(--peta-accent) !important;
        }

        .nav-link-cinema:hover::after,
        .nav-link-cinema.active::after {
            transform: scaleX(1);
        }

        .nav-link-cinema.active {
            color: var(--peta-accent) !important;
        }

        .cinema-dropdown {
            min-width: 230px;
            margin-top: 10px !important;
            padding: 8px;
            border: 1px solid var(--peta-card-border);
            border-radius: 12px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.14);
        }

        .cinema-dropdown .dropdown-item {
            border-radius: 8px;
            color: #222222;
            font-family: 'Poppins', 'Montserrat', sans-serif;
            font-size: 0.82rem;
            font-weight: 700;
            padding: 10px 12px;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .cinema-dropdown .dropdown-item:hover,
        .cinema-dropdown .dropdown-item:focus {
            background-color: #fff1f2;
            color: var(--peta-accent);
        }

        /* Light Movie Cards */
        .card-cinema {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            overflow: hidden;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease, border-color 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .card-cinema:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(229, 9, 20, 0.18);
            border-color: var(--peta-accent);
        }

        .card-cinema .card-title {
            color: #0f172a !important;
            font-weight: 700;
        }

        .card-cinema .card-title a {
            color: #0f172a !important;
            transition: color 0.2s ease;
        }

        .card-cinema .card-title a:hover {
            color: var(--peta-accent) !important;
        }

        .card-cinema .card-text {
            color: #64748b !important;
        }

        .movie-poster-wrapper {
            position: relative;
            aspect-ratio: 2/3;
            overflow: hidden;
            background-color: #0f172a;
        }

        .movie-poster-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .card-cinema:hover .movie-poster-img {
            transform: scale(1.05);
        }

        .age-rating-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 6px;
            text-transform: uppercase;
        }

        .badge-rating-P { background-color: #10b981; color: #ffffff; }
        .badge-rating-K { background-color: #3b82f6; color: #ffffff; }
        .badge-rating-T13 { background-color: #f97316; color: #ffffff; }
        .badge-rating-T16 { background-color: #e50914; color: #ffffff; }
        .badge-rating-T18 { background-color: #dc3545; color: #ffffff; }
        .badge-rating-C { background-color: #8b5cf6; color: #ffffff; }

        /* Buttons */
        .btn-peta {
            background-color: var(--peta-accent);
            color: #ffffff;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.2s ease;
        }

        .btn-peta:hover {
            background-color: var(--peta-accent-hover);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(229, 9, 20, 0.35);
        }

        .btn-outline-peta {
            border: 2px solid var(--peta-accent);
            color: var(--peta-accent);
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 16px;
            background: transparent;
            transition: all 0.2s ease;
        }

        .btn-outline-peta:hover, .btn-outline-peta.active {
            background-color: var(--peta-accent);
            color: #ffffff;
        }

        /* Hero Banner Carousel Styling */
        .hero-carousel-item {
            min-height: 480px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .hero-logo-badge {
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.6);
        }

        .hero-logo-img {
            height: 42px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.8));
        }

        .hero-movie-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            color: #ffffff !important;
            text-shadow: 0 4px 16px rgba(0, 0, 0, 0.95), 0 0 25px rgba(229, 9, 20, 0.35);
            letter-spacing: 0.5px;
            line-height: 1.1;
        }

        .hero-slogan {
            color: #f4f4f5 !important;
            font-family: 'Poppins', 'Roboto', sans-serif;
            font-weight: 500;
            border-left: 4px solid var(--peta-accent);
            padding: 8px 16px;
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            border-radius: 0 8px 8px 0;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.9);
            max-width: 90%;
        }

        .hero-btn-booking {
            background-color: var(--peta-accent) !important;
            color: #ffffff !important;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            letter-spacing: 0.5px;
            padding: 12px 28px;
            box-shadow: 0 6px 20px rgba(229, 9, 20, 0.45) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hero-btn-booking:hover {
            background-color: var(--peta-accent-hover) !important;
            color: #ffffff !important;
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 10px 28px rgba(229, 9, 20, 0.65) !important;
        }

        .carousel-indicators [data-bs-target] {
            background-color: #ffffff;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin: 0 4px;
            opacity: 0.5;
            transition: all 0.3s ease;
        }

        .carousel-indicators .active {
            background-color: var(--peta-accent);
            width: 26px;
            border-radius: 6px;
            opacity: 1;
        }

        .hero-poster-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid var(--peta-accent);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.8);
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: rgba(229, 9, 20, 0.85);
            padding: 20px;
            border-radius: 50%;
            background-size: 50%;
        }

        .carousel-control-prev-icon:hover,
        .carousel-control-next-icon:hover {
            background-color: var(--peta-accent-hover);
        }

        /* Đồng bộ kiểu chữ với giao diện quản trị. */
        body, button, input, select, textarea, .btn, .breadcrumb, .nav-link {
            font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        h1, h2, h3, h4, h5, h6, .navbar-brand {
            font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        .hero-slogan, .hero-btn-booking, .card-cinema, .carousel-indicators,
        footer, .btn-peta, .btn-outline-peta {
            font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        /* Footer */
        footer {
            margin-top: auto;
            background: linear-gradient(135deg, #080d19 0%, #111827 52%, #17111a 100%);
            border-top: 3px solid var(--peta-accent);
            color: #b7c0ce;
            font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            font-size: .88rem;
            font-weight: 400;
            line-height: 1.65;
        }

        .footer-main { padding: 2.75rem 0 2.1rem; }
        .footer-brand-logo { display: block; height: 42px; margin-bottom: .9rem; max-width: 175px; object-fit: contain; object-position: left center; }
        .footer-description { color: #aab5c5; margin: 0; max-width: 300px; }
        .footer-heading { color: #fff; font-size: .88rem; font-weight: 800; letter-spacing: .06em; line-height: 1.35; margin: .35rem 0 1rem; text-transform: uppercase; }
        .footer-links { display: grid; gap: .55rem; list-style: none; margin: 0; padding: 0; }
        .footer-links a, .footer-contact-item { align-items: flex-start; color: #b7c0ce; display: inline-flex; gap: .55rem; text-decoration: none; transition: color .2s ease, transform .2s ease; }
        .footer-links a:hover, .footer-links a:focus { color: #fff; transform: translateX(3px); }
        .footer-links a i, .footer-contact-item i { color: var(--peta-accent); font-size: .8rem; line-height: 1.65; }
        .footer-contact-list { display: grid; gap: .55rem; }
        .footer-socials { display: flex; flex-wrap: wrap; gap: .55rem; margin-top: 1.05rem; }
        .footer-socials a { align-items: center; background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.1); border-radius: 50%; color: #fff; display: inline-flex; font-size: .9rem; height: 2.2rem; justify-content: center; text-decoration: none; transition: background-color .2s ease, border-color .2s ease, transform .2s ease; width: 2.2rem; }
        .footer-socials a:hover, .footer-socials a:focus { background: var(--peta-accent); border-color: var(--peta-accent); color: #fff; transform: translateY(-2px); }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,.11); color: #8793a5; font-size: .78rem; padding: .9rem 0; }
        .footer-bottom strong { color: #fff; font-weight: 700; }

        @media (max-width: 991.98px) { .footer-main { padding: 2.3rem 0 1.75rem; } .footer-brand-logo { max-width: 160px; } }
        @media (max-width: 575.98px) { .footer-main { padding: 2rem 0 1.5rem; } .footer-heading { margin-top: .2rem; } .footer-bottom { text-align: center; } }
    </style>
</head>

<body>

    <!-- Unified Site Header Wrapper (Light Cinema Aesthetic) -->
    <header class="site-header-wrapper">

        <!-- Main Header Navigation -->
        <nav class="navbar navbar-expand-lg navbar-cinema py-2">
            <div class="container">
                <!-- Brand Logo -->
                <a class="navbar-brand me-4 d-flex align-items-center" href="<?= BASE_URL ?>">
                    <img src="<?= BASE_ASSETS_UPLOADS ?>logo/logo.png" alt="Petacinema Logo" class="brand-logo-img">
                </a>

                <!-- Mobile Navbar Toggler -->
                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="bi bi-list fs-2 text-dark"></i>
                </button>

                <!-- Navbar Menu Links -->
                <div class="collapse navbar-collapse" id="navbarContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-2 gap-1 align-items-lg-center">
                        <li class="nav-item">
                            <a class="nav-link nav-link-cinema text-uppercase<?= (($_GET['client_page'] ?? '') === 'showtimes') ? ' active' : '' ?>" href="<?= BASE_URL ?>?client_page=showtimes">
                                LỊCH CHIẾU
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-cinema text-uppercase<?= (($_GET['movie_category'] ?? '') === 'now_showing') ? ' active' : '' ?>" href="<?= BASE_URL ?>?movie_category=now_showing">PHIM ĐANG CHIẾU</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-cinema text-uppercase<?= (($_GET['movie_category'] ?? '') === 'coming_soon') ? ' active' : '' ?>" href="<?= BASE_URL ?>?movie_category=coming_soon">PHIM SẮP CHIẾU</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-cinema text-uppercase<?= (($_GET['movie_category'] ?? '') === 'special') ? ' active' : '' ?>" href="<?= BASE_URL ?>?movie_category=special">SUẤT CHIẾU ĐẶC BIỆT</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link nav-link-cinema text-uppercase<?= (($_GET['client_page'] ?? '') === 'foods') ? ' active' : '' ?>" href="<?= BASE_URL ?>?client_page=foods">
                                TIN MỚI VÀ ƯU ĐÃI
                            </a>
                        </li>

                        <?php
                        $clientRole = $_SESSION['role'] ?? ($_SESSION['user']['role'] ?? null);
                        if (isset($_SESSION['user']) && in_array($clientRole, ['user', 'client'], true)):
                        ?>
                            <li class="nav-item">
                                <a class="nav-link nav-link-cinema text-uppercase<?= (($_GET['action'] ?? '') === 'my_tickets') ? ' active' : '' ?>" href="<?= BASE_URL ?>?action=my_tickets">
                                    VÉ CỦA TÔI
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <!-- User Account Actions -->
                    <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                        <?php if (isset($_SESSION['user'])): ?>
                            <span class="text-dark fw-bold small me-2">
                                <i class="bi bi-person-circle text-danger me-1 fs-6"></i> <?= h($_SESSION['user']['fullname'] ?? 'Thành viên') ?>
                            </span>
                            <a class="btn btn-sm btn-outline-dark fw-semibold" href="<?= BASE_URL ?>?action=logout">Đăng xuất</a>
                        <?php else: ?>
                            <a class="btn btn-sm btn-outline-danger fw-bold px-3 py-2" href="<?= BASE_URL ?>?action=login">Đăng nhập</a>
                            <a class="btn btn-sm btn-peta fw-bold px-3 py-2" href="<?= BASE_URL ?>?action=register">Đăng ký</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content Container -->
    <main class="py-4">
        <div class="container">
            <?php
            $clientFlash = get_flash();
            if ($clientFlash):
                $flashType = $clientFlash['type'] ?? 'info';
                $bootstrapFlashType = match ($flashType) {
                    'success' => 'success',
                    'error', 'danger' => 'danger',
                    'warning' => 'warning',
                    default => 'info',
                };
            ?>
                <div class="alert alert-<?= h($bootstrapFlashType) ?> alert-dismissible fade show shadow-sm" role="alert">
                    <?= h($clientFlash['message'] ?? '') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                </div>
            <?php endif; ?>

            <?php
            if (isset($view)) {
                require_once PATH_VIEW . $view . '.php';
            }
            ?>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container footer-main">
            <div class="row g-4 g-lg-5">
                <section class="col-12 col-md-6 col-lg-4">
                    <a href="<?= BASE_URL ?>" aria-label="Petacinema - Trang chủ">
                        <img src="<?= BASE_ASSETS_UPLOADS ?>logo/logo.png" alt="Petacinema Logo" class="footer-brand-logo">
                    </a>
                    <p class="footer-description">Hệ thống rạp chiếu phim hiện đại, mang đến những trải nghiệm điện ảnh trọn vẹn cho mọi khán giả.</p>
                </section>

                <section class="col-12 col-md-6 col-lg-2">
                    <h2 class="footer-heading">Khám phá</h2>
                    <ul class="footer-links">
                        <li><a href="<?= BASE_URL ?>?movie_category=now_showing"><i class="bi bi-chevron-right"></i>Phim đang chiếu</a></li>
                        <li><a href="<?= BASE_URL ?>?movie_category=coming_soon"><i class="bi bi-chevron-right"></i>Phim sắp chiếu</a></li>
                        <li><a href="<?= BASE_URL ?>?client_page=showtimes"><i class="bi bi-chevron-right"></i>Lịch chiếu</a></li>
                        <li><a href="<?= BASE_URL ?>?client_page=foods"><i class="bi bi-chevron-right"></i>Ưu đãi &amp; combo</a></li>
                    </ul>
                </section>

                <section class="col-12 col-md-6 col-lg-2">
                    <h2 class="footer-heading">Hỗ trợ khách hàng</h2>
                    <ul class="footer-links">
                        <li><a href="#footer-contact"><i class="bi bi-chevron-right"></i>Liên hệ</a></li>
                        <li><a href="#footer-contact"><i class="bi bi-chevron-right"></i>Câu hỏi thường gặp</a></li>
                        <li><a href="#footer-contact"><i class="bi bi-chevron-right"></i>Điều khoản sử dụng</a></li>
                        <li><a href="#footer-contact"><i class="bi bi-chevron-right"></i>Chính sách bảo mật</a></li>
                    </ul>
                </section>

                <section class="col-12 col-md-6 col-lg-4" id="footer-contact">
                    <h2 class="footer-heading">Thông tin liên hệ</h2>
                    <div class="footer-contact-list">
                        <span class="footer-contact-item"><i class="bi bi-geo-alt-fill"></i>Petacinema, Việt Nam</span>
                        <a class="footer-contact-item" href="mailto:hotro@petacinema.vn"><i class="bi bi-envelope-fill"></i>hotro@petacinema.vn</a>
                        <a class="footer-contact-item" href="tel:19000000"><i class="bi bi-telephone-fill"></i>Hotline: 1900 0000</a>
                    </div>
                    <div class="footer-socials" aria-label="Mạng xã hội Petacinema">
                        <a href="#footer-contact" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#footer-contact" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                        <a href="#footer-contact" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                    </div>
                </section>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container d-flex flex-column flex-md-row justify-content-between gap-1">
                <span>© <?= date('Y') ?> <strong>Petacinema</strong>. Bảo lưu mọi quyền.</span>
                <span>Trải nghiệm điện ảnh hiện đại.</span>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
