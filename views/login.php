<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Đăng nhập - PETACINEMA</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
        }

        body {
            min-height: 100vh;
            padding: 10px 14px;

            display: flex;
            align-items: center;
            justify-content: center;

            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;

            background:
                radial-gradient(
                    circle at top left,
                    rgba(239, 68, 68, 0.75) 0%,
                    transparent 30%
                ),
                radial-gradient(
                    circle at bottom right,
                    rgba(127, 29, 29, 0.8) 0%,
                    transparent 35%
                ),
                linear-gradient(
                    135deg,
                    #111827,
                    #030712
                );
        }

        .login-card {
            width: 100%;
            max-width: 350px;
            padding: 20px 22px;

            background: rgba(255, 255, 255, 0.07);

            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;

            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);

            box-shadow:
                0 22px 55px rgba(0, 0, 0, 0.45),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }

        .logo-wrapper {
            text-align: center;
            margin-bottom: 8px;
        }

        .logo {
            display: block;
            width: 130px;
            max-width: 100%;
            height: auto;
            margin: 0 auto;
        }

        .login-title {
            margin-bottom: 3px;

            color: #ffffff;

            font-size: 23px;
            font-weight: 750;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .login-subtitle {
            margin-bottom: 14px;

            color: #cbd5e1;

            font-size: 13px;
            text-align: center;
        }

        .form-label {
            margin-bottom: 5px;

            color: #f8fafc;

            font-size: 13px;
            font-weight: 600;
        }

        .input-group {
            border-radius: 10px;
            overflow: hidden;

            transition:
                box-shadow 0.2s ease,
                transform 0.2s ease;
        }

        .input-group:focus-within {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
        }

        .input-group-text {
            width: 43px;

            display: flex;
            justify-content: center;

            background: rgba(30, 41, 59, 0.95);
            color: #f8fafc;

            border: 1px solid rgba(255, 255, 255, 0.08);
            border-right: none;

            font-size: 14px;
        }

        .form-control {
            height: 41px;

            background: rgba(30, 41, 59, 0.95);
            color: #ffffff;

            border: 1px solid rgba(255, 255, 255, 0.08);
            border-left: none;

            font-size: 13px;
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        .form-control:focus {
            background: rgba(30, 41, 59, 0.95);
            color: #ffffff;

            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: none;
        }

        .btn-action {
            min-height: 41px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 10px;

            font-size: 13px;
            font-weight: 650;

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                background-color 0.2s ease;
        }

        .btn-login {
            border: none;

            background: linear-gradient(
                135deg,
                #ef4444,
                #b91c1c
            );

            color: #ffffff;

            box-shadow: 0 9px 20px rgba(185, 28, 28, 0.3);
        }

        .btn-login:hover {
            color: #ffffff;

            transform: translateY(-1px);

            box-shadow: 0 12px 24px rgba(185, 28, 28, 0.4);
        }

        .btn-register {
            border: 1px solid rgba(255, 255, 255, 0.65);

            background: transparent;
            color: #ffffff;
        }

        .btn-register:hover {
            border-color: #ffffff;

            background: #ffffff;
            color: #111827;

            transform: translateY(-1px);
        }

        .btn-home {
            border: 1px solid rgba(148, 163, 184, 0.45);

            background: rgba(15, 23, 42, 0.3);
            color: #cbd5e1;
        }

        .btn-home:hover {
            border-color: rgba(203, 213, 225, 0.8);

            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;

            transform: translateY(-1px);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 10px;

            margin: 12px 0;
        }

        .divider::before,
        .divider::after {
            content: "";

            flex: 1;
            height: 1px;

            background: rgba(255, 255, 255, 0.15);
        }

        .divider span {
            color: #94a3b8;

            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .alert {
            margin-bottom: 12px;
            padding: 8px 10px;

            border: none;
            border-radius: 9px;

            font-size: 12px;
        }

        .login-footer {
            margin-top: 12px;

            color: #94a3b8;

            font-size: 11px;
            text-align: center;
        }

        @media (max-height: 700px) {
            body {
                padding: 8px 12px;
                align-items: flex-start;
            }

            .login-card {
                max-width: 340px;
                margin-top: 8px;
                padding: 16px 20px;
            }

            .logo-wrapper {
                margin-bottom: 5px;
            }

            .logo {
                width: 110px;
            }

            .login-title {
                font-size: 21px;
            }

            .login-subtitle {
                margin-bottom: 10px;
                font-size: 12px;
            }

            .form-label {
                margin-bottom: 4px;
                font-size: 12px;
            }

            .form-control {
                height: 38px;
                font-size: 12px;
            }

            .input-group-text {
                width: 40px;
            }

            .btn-action {
                min-height: 38px;
                font-size: 12px;
            }

            .divider {
                margin: 9px 0;
            }

            .login-footer {
                margin-top: 9px;
            }

            .mb-3 {
                margin-bottom: 10px !important;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 12px;
                align-items: center;
            }

            .login-card {
                max-width: 100%;
                padding: 20px 18px;
            }

            .logo {
                width: 120px;
            }

            .login-title {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>

    <main class="login-card">

        <div class="logo-wrapper">
            <img
                src="<?= BASE_ASSETS_UPLOADS ?>logo/logo.png"
                alt="Logo PETACINEMA"
                class="logo">
        </div>

        <h1 class="login-title">PETACINEMA</h1>

        <p class="login-subtitle">
            Đăng nhập vào hệ thống
        </p>

        <?php 
        $flash  = get_flash(); 
        $errors = get_errors();
        $old    = get_old();
        ?>

        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
                <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill' ?> me-1"></i>
                <?= h($flash['message']) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="?action=loginPost" novalidate>

            <div class="mb-3">
                <label for="email" class="form-label">
                    Email / Tên đăng nhập
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-envelope-fill"></i>
                    </span>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        placeholder="Nhập địa chỉ email"
                        autocomplete="email"
                        value="<?= old_value($old, 'email') ?>">
                </div>
                <?= field_error($errors, 'email') ?>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">
                    Mật khẩu
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock-fill"></i>
                    </span>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Nhập mật khẩu"
                        autocomplete="current-password">
                </div>
                <?= field_error($errors, 'password') ?>
            </div>

            <button
                type="submit"
                class="btn btn-action btn-login w-100">

                <i class="bi bi-box-arrow-in-right me-2"></i>
                Đăng nhập

            </button>

        </form>

        <div class="divider">
            <span>HOẶC</span>
        </div>

        <a
            href="?action=register"
            class="btn btn-action btn-register w-100 mb-2">

            <i class="bi bi-person-plus-fill me-2"></i>
            Đăng ký tài khoản

        </a>

        <a
            href="<?= BASE_URL ?>"
            class="btn btn-action btn-home w-100">

            <i class="bi bi-house-door-fill me-2"></i>
            Quay lại trang chủ

        </a>

        <footer class="login-footer">
            © <?= date('Y') ?> PETACINEMA
        </footer>

    </main>

</body>

</html>