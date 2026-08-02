<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Đăng ký - PETACINEMA</title>

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
                radial-gradient(circle at top left,
                    rgba(239, 68, 68, 0.75) 0%,
                    transparent 30%),
                radial-gradient(circle at bottom right,
                    rgba(127, 29, 29, 0.8) 0%,
                    transparent 35%),
                linear-gradient(135deg,
                    #111827,
                    #030712);
        }

        .register-card {
            width: 100%;
            max-width: 370px;
            padding: 20px 22px;

            background: rgba(255,255,255,.07);

            border: 1px solid rgba(255,255,255,.1);
            border-radius: 20px;

            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);

            box-shadow:
                0 22px 55px rgba(0,0,0,.45),
                inset 0 1px 0 rgba(255,255,255,.05);
        }

        .logo-wrapper{
            text-align:center;
            margin-bottom:8px;
        }

        .logo{
            width:130px;
            display:block;
            margin:auto;
        }

        .title{
            color:#fff;
            font-size:23px;
            font-weight:700;
            text-align:center;
            margin-bottom:3px;
        }

        .subtitle{
            text-align:center;
            color:#cbd5e1;
            font-size:13px;
            margin-bottom:14px;
        }

        .form-label{
            color:#f8fafc;
            font-size:13px;
            font-weight:600;
            margin-bottom:5px;
        }

        .input-group{
            border-radius:10px;
            overflow:hidden;
        }

        .input-group:focus-within{
            box-shadow:0 0 0 3px rgba(239,68,68,.2);
        }

        .input-group-text{
            width:43px;
            justify-content:center;
            background:#1e293b;
            color:#fff;
            border-right:none;
            border-color:rgba(255,255,255,.08);
        }

        .form-control{
            height:41px;
            background:#1e293b;
            color:#fff;
            border-left:none;
            border-color:rgba(255,255,255,.08);
            font-size:13px;
        }

        .form-control::placeholder{
            color:#94a3b8;
        }

        .form-control:focus{
            background:#1e293b;
            color:#fff;
            box-shadow:none;
            border-color:rgba(255,255,255,.08);
        }

        .btn-action{
            min-height:41px;
            border-radius:10px;
            font-size:13px;
            font-weight:600;
            display:flex;
            justify-content:center;
            align-items:center;
        }

        .btn-register{
            border:none;
            color:#fff;
            background:linear-gradient(135deg,#ef4444,#b91c1c);
            box-shadow:0 9px 20px rgba(185,28,28,.3);
        }

        .btn-register:hover{
            color:#fff;
        }

        .btn-login{
            background:transparent;
            border:1px solid rgba(255,255,255,.65);
            color:#fff;
        }

        .btn-login:hover{
            background:#fff;
            color:#111827;
        }

        .divider{
            display:flex;
            align-items:center;
            gap:10px;
            margin:12px 0;
        }

        .divider::before,
        .divider::after{
            content:"";
            flex:1;
            height:1px;
            background:rgba(255,255,255,.15);
        }

        .divider span{
            color:#94a3b8;
            font-size:10px;
            font-weight:600;
        }

        .alert{
            margin-bottom:12px;
            font-size:12px;
        }

        .footer{
            margin-top:12px;
            text-align:center;
            color:#94a3b8;
            font-size:11px;
        }

        @media(max-height:700px){

            body{
                padding:8px 12px;
                align-items:flex-start;
            }

            .register-card{
                margin-top:8px;
                max-width:350px;
                padding:18px;
            }

            .logo{
                width:110px;
            }

            .title{
                font-size:21px;
            }

            .subtitle{
                font-size:12px;
                margin-bottom:10px;
            }

            .form-control{
                height:38px;
            }

            .btn-action{
                min-height:38px;
            }

            .mb-3{
                margin-bottom:10px!important;
            }
        }
    </style>

</head>

<body>

<main class="register-card">

    <div class="logo-wrapper">
        <img
            src="<?= BASE_ASSETS_UPLOADS ?>logo/logo.png"
            class="logo"
            alt="PETACINEMA">
    </div>

    <h1 class="title">
        PETACINEMA
    </h1>

    <p class="subtitle">
        Đăng ký tài khoản
    </p>

    <?php $flash = get_flash(); ?>

    <?php if($flash): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle-fill me-1"></i>
            <?= h($flash['message']) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="?action=registerStore">

        <div class="mb-3">
            <label class="form-label">Họ và tên</label>

            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-person-fill"></i>
                </span>

                <input
                    type="text"
                    name="fullname"
                    class="form-control"
                    placeholder="Nhập họ và tên"
                    required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>

            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-envelope-fill"></i>
                </span>

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="Nhập địa chỉ email"
                    required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>

            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-lock-fill"></i>
                </span>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Nhập mật khẩu"
                    required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Xác nhận mật khẩu</label>

            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-shield-lock-fill"></i>
                </span>

                <input
                    type="password"
                    name="confirm_password"
                    class="form-control"
                    placeholder="Nhập lại mật khẩu"
                    required>
            </div>
        </div>

        <button
            type="submit"
            class="btn btn-action btn-register w-100">

            <i class="bi bi-person-plus-fill me-2"></i>
            Đăng ký

        </button>

    </form>

    <div class="divider">
        <span>HOẶC</span>
    </div>

    <a
        href="?action=login"
        class="btn btn-action btn-login w-100">

        <i class="bi bi-arrow-left me-2"></i>
        Quay lại đăng nhập

    </a>

    <footer class="footer">
        © <?= date('Y') ?> PETACINEMA
    </footer>

</main>

</body>

</html>