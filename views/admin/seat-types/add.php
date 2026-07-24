<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thêm loại ghế - PETACINEMA Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_ASSETS ?>css/admin.css">
</head>
<body>
<?php require PATH_VIEW . 'admin/layout/sidebar.php'; ?>
<div class="main-wrapper">
<?php require PATH_VIEW . 'admin/layout/header.php'; ?>
<main class="content">
    <div class="mb-4">
        <h4 class="mb-1 fw-bold">Thêm loại ghế</h4>
        <p class="text-muted mb-0">Tạo mới một loại ghế.</p>
    </div>

    <?php if (($_GET['error'] ?? '') === 'invalid_seat_type'): ?>
        <div class="alert alert-danger">Vui lòng nhập đầy đủ và hợp lệ thông tin loại ghế.</div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-4">
            <form method="POST" action="?action=seatTypeAddProcess">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên loại ghế</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Phụ thu</label>
                    <input type="number" name="surcharge" class="form-control" min="0" step="0.01" value="0" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Mô tả</label>
                    <textarea name="description" class="form-control" rows="4"></textarea>
                </div>
                <a href="?action=seat-types" class="btn btn-light">Hủy</a>
                <button type="submit" class="btn btn-danger">Lưu loại ghế</button>
            </form>
        </div>
    </div>
</main>
</div>
</body>
</html>
