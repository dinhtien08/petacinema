<?php
$currentAction = 'room-types';
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Room Types - PETACINEMA Admin</title>
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
            <h4 class="mb-1 fw-bold">Quản lý loại phòng</h4>
            <p class="text-muted mb-0">Quản lý các loại phòng chiếu của rạp.</p>
        </div>
        <a href="?action=roomTypeAdd" class="btn btn-danger rounded-3 px-4">
            <i class="bi bi-plus-lg me-1"></i> Thêm loại phòng
        </a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success mt-3">
            <?= match ($success) {
                'room_type_added' => 'Thêm loại phòng thành công.',
                'room_type_updated' => 'Cập nhật loại phòng thành công.',
                'room_type_deleted' => 'Xóa loại phòng thành công.',
                default => 'Thao tác thành công.'
            } ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger mt-3">
            <?= match ($error) {
                'room_type_using' => 'Không thể xóa loại phòng vì đang được sử dụng bởi phòng chiếu.',
                'invalid_room_type' => 'Vui lòng nhập đầy đủ và hợp lệ thông tin loại phòng.',
                'room_type_not_found' => 'Không tìm thấy loại phòng.',
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
                        <th>Tên loại phòng</th>
                        <th>Phụ thu</th>
                        <th>Mô tả</th>
                        <th>Số phòng</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($listRoomType as $roomType): ?>
                        <tr>
                            <td><?= $roomType['id'] ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($roomType['name']) ?></td>
                            <td><?= number_format((float)$roomType['price_modifier'], 0, ',', '.') ?> đ</td>
                            <td><?= htmlspecialchars($roomType['description'] ?? '') ?></td>
                            <td><?= (int)$roomType['total_rooms'] ?></td>
                            <td class="text-end">
                                <a href="?action=roomTypeEdit&id=<?= $roomType['id'] ?>" class="btn btn-outline-primary btn-sm me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="?action=roomTypeDelete&id=<?= $roomType['id'] ?>" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$listRoomType): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Chưa có loại phòng.</td></tr>
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
