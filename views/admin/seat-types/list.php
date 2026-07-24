<?php
$currentAction = 'seat-types';
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seat Types - PETACINEMA Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_ASSETS ?>css/admin.css">
</head>
<body>
<?php require PATH_VIEW . 'admin/layout/sidebar.php'; ?>
<div class="main-wrapper">
<?php require PATH_VIEW . 'admin/layout/header.php'; ?>
<main class="content">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <div>
            <h4 class="mb-1 fw-bold">Quản lý loại ghế</h4>
            <p class="text-muted mb-0">Quản lý các loại ghế trong phòng chiếu.</p>
        </div>
        <a href="?action=seatTypeAdd" class="btn btn-danger rounded-3 px-4">
            <i class="bi bi-plus-lg me-1"></i> Thêm loại ghế
        </a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success mt-3">
            <?= match ($success) {
                'seat_type_added' => 'Thêm loại ghế thành công.',
                'seat_type_updated' => 'Cập nhật loại ghế thành công.',
                'seat_type_deleted' => 'Xóa loại ghế thành công.',
                default => 'Thao tác thành công.'
            } ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger mt-3">
            <?= match ($error) {
                'seat_type_using' => 'Không thể xóa loại ghế vì đang được sử dụng bởi ghế trong phòng.',
                'invalid_seat_type' => 'Vui lòng nhập đầy đủ và hợp lệ thông tin loại ghế.',
                'seat_type_not_found' => 'Không tìm thấy loại ghế.',
                default => 'Có lỗi xảy ra. Vui lòng thử lại.'
            } ?>
        </div>
    <?php endif; ?>

    <div class="card mt-4">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên loại ghế</th>
                        <th>Phụ thu</th>
                        <th>Mô tả</th>
                        <th>Số ghế</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($listSeatType as $seatType): ?>
                        <tr>
                            <td><?= $seatType['id'] ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($seatType['name']) ?></td>
                            <td><?= number_format((float)$seatType['surcharge'], 0, ',', '.') ?> đ</td>
                            <td><?= htmlspecialchars($seatType['description'] ?? '') ?></td>
                            <td><?= (int)$seatType['total_seats'] ?></td>
                            <td class="text-end">
                                <a href="?action=seatTypeEdit&id=<?= $seatType['id'] ?>" class="btn btn-outline-primary btn-sm me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="?action=seatTypeDelete&id=<?= $seatType['id'] ?>" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$listSeatType): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Chưa có loại ghế.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
</div>
</body>
</html>
