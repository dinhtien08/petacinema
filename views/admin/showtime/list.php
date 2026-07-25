<?php
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>

<?php if ($success === 'deleted'): ?>

    <div class="alert alert-success alert-dismissible fade show" role="alert">

        <i class="bi bi-check-circle-fill me-2"></i>

        Xóa suất chiếu thành công.

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Đóng">
        </button>

    </div>

<?php endif; ?>

<?php if ($error): ?>

    <?php
    $errorMessages = [
        'invalid_method' => 'Phương thức xóa không hợp lệ.',
        'invalid_id'     => 'Mã suất chiếu không hợp lệ.',
        'not_found'      => 'Không tìm thấy suất chiếu.',
        'has_booking'    => 'Không thể xóa vì suất chiếu đã có người đặt vé.',
        'delete_failed'  => 'Xóa suất chiếu thất bại. Vui lòng thử lại.',
    ];

    $errorMessage = $errorMessages[$error]
        ?? 'Đã xảy ra lỗi trong quá trình xóa.';
    ?>

    <div class="alert alert-danger alert-dismissible fade show" role="alert">

        <i class="bi bi-exclamation-triangle-fill me-2"></i>

        <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Đóng">
        </button>

    </div>

<?php endif; ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Danh sách suất chiếu</h4>

        <a href="?action=showtime_create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm suất chiếu
        </a>
    </div>

    <div class="card-body">
        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-light">
                    <tr class="text-center">
                        <th width="60">#</th>
                        <th>Phim</th>
                        <th>Phòng</th>
                        <th>Bắt đầu</th>
                        <th>Kết thúc</th>
                        <th>Giá cơ sở</th>
                        <th width="180">Thao tác</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (!empty($showtimes)): ?>

                    <?php foreach ($showtimes as $index => $showtime): ?>

                        <tr>
                            <td class="text-center">
                                <?= $index + 1 ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($showtime['movie_title']) ?>
                            </td>

                            <td class="text-center">
                                <?= htmlspecialchars($showtime['room_name']) ?>
                            </td>

                            <td class="text-center">
                                <?= date('Y-m-d H:i', strtotime($showtime['start_time'])) ?>
                            </td>

                            <td class="text-center">
                                <?= date('Y-m-d H:i', strtotime($showtime['end_time'])) ?>
                            </td>

                            <td class="text-end">
                                <?= number_format($showtime['base_price'], 0, ',', '.') ?> đ
                            </td>

                            <td class="text-center">

                                <a href="?action=showtime_show&id=<?= $showtime['id'] ?>"
                                   class="btn btn-info btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="?action=showtime_edit&id=<?= $showtime['id'] ?>"
                                   class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <a href="?action=showtime_delete&id=<?= $showtime['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                    <i class="bi bi-trash"></i>
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            Chưa có suất chiếu nào.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>
    </div>
</div>