<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>PETACINEMA Staff</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_ASSETS ?>css/admin.css">
</head>
<body>
    <?php require_once PATH_VIEW_STAFF . 'layout/header.php'; ?>

    <div class="admin-wrapper">
        <?php require_once PATH_VIEW_STAFF . 'layout/sidebar.php'; ?>

        <main class="content">
            <?php
            $flashSuccess = $_SESSION['success'] ?? null;
            $flashError = $_SESSION['error'] ?? null;
            $flash = get_flash();
            if ($flash) {
                if ($flash['type'] === 'success') {
                    $flashSuccess = $flash['message'];
                }
                if ($flash['type'] === 'danger' || $flash['type'] === 'error') {
                    $flashError = $flash['message'];
                }
            }
            unset($_SESSION['success'], $_SESSION['error']);
            ?>

            <?php if (!empty($flashSuccess)): ?>
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <?= htmlspecialchars($flashSuccess) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($flashError)): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <?= htmlspecialchars($flashError) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php
            $viewPath = null;
            if (isset($view) && $view !== '') {
                if (file_exists($view)) {
                    $viewPath = $view;
                } else if (defined('PATH_VIEW') && file_exists(PATH_VIEW . $view)) {
                    $viewPath = PATH_VIEW . $view;
                } else if (defined('PATH_VIEW') && file_exists(PATH_VIEW . $view . '.php')) {
                    $viewPath = PATH_VIEW . $view . '.php';
                }
            }

            if ($viewPath && file_exists($viewPath)) {
                require $viewPath;
            } else {
                echo '<div class="alert alert-danger">
                        Không tìm thấy nội dung trang.
                      </div>';
            }
            ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>