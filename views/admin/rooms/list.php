<?php
$currentAction = 'rooms';
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rooms - PETACINEMA Admin</title>
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
            <h4 class="mb-1 fw-bold">Quản lý phòng chiếu</h4>
            <p class="text-muted mb-0">Quản lý danh sách phòng chiếu của rạp.</p>
        </div>
        <a href="?action=roomAdd" class="btn btn-danger rounded-3 px-4">
            <i class="bi bi-plus-lg me-1"></i> Thêm phòng
        </a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success mt-3">
            <?= match ($success) {
                'room_added' => 'Thêm phòng thành công.',
                'room_updated' => 'Cập nhật phòng thành công.',
                'room_deleted' => 'Xóa phòng thành công.',
                default => 'Thao tác thành công.'
            } ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger mt-3">
            <?= match ($error) {
                'room_using' => 'Không thể xóa phòng vì phòng đang có ghế hoặc suất chiếu liên quan.',
                'invalid_room' => 'Vui lòng nhập đầy đủ và hợp lệ thông tin phòng.',
                'room_not_found' => 'Không tìm thấy phòng chiếu.',
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
                        <th>Tên phòng</th>
                        <th>Loại phòng</th>
                        <th>Tổng số ghế</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($listRoom as $room): ?>
                        <tr>
                            <td><?= $room['id'] ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($room['name']) ?></td>
                            <td><span class="badge text-bg-light"><?= htmlspecialchars($room['room_type_name']) ?></span></td>
                            <td><?= (int)$room['total_seats'] ?> ghế</td>
                            <td class="text-end">
                                <a href="?action=roomEdit&id=<?= $room['id'] ?>" class="btn btn-outline-primary btn-sm me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="?action=roomDelete&id=<?= $room['id'] ?>" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$listRoom): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Chưa có phòng chiếu.</td></tr>
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
