<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sửa phòng - PETACINEMA Admin</title>
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
        <h4 class="mb-1 fw-bold">Sửa phòng chiếu</h4>
        <p class="text-muted mb-0">Cập nhật thông tin phòng chiếu.</p>
    </div>

    <?php if (($_GET['error'] ?? '') === 'invalid_room'): ?>
        <div class="alert alert-danger">Vui lòng nhập đầy đủ và hợp lệ thông tin phòng.</div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-4">
            <form method="POST" action="?action=roomEditProcess&id=<?= $room['id'] ?>">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên phòng</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($room['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Loại phòng</label>
                    <select name="room_type_id" class="form-select" required>
                        <option value="">-- Chọn loại phòng --</option>
                        <?php foreach ($listRoomType as $roomType): ?>
                            <option value="<?= $roomType['id'] ?>" <?= $roomType['id'] == $room['room_type_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($roomType['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Tổng số ghế</label>
                    <input type="number" name="total_seats" class="form-control" min="1" value="<?= (int)$room['total_seats'] ?>" required>
                </div>
                <a href="?action=rooms" class="btn btn-light">Hủy</a>
                <button type="submit" class="btn btn-danger">Cập nhật</button>
            </form>
        </div>
    </div>
</main>
</div>
</body>
</html>
