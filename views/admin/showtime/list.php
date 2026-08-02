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

<?php
$keyword = $_GET['keyword'] ?? '';
$movieId = $_GET['movie_id'] ?? '';
$roomId  = $_GET['room_id'] ?? '';
$status  = $_GET['status'] ?? '';
$date    = $_GET['date'] ?? '';
$currentAction = $_GET['action'] ?? 'showtimes';
$hasFilter = !empty($keyword) || !empty($movieId) || !empty($roomId) || !empty($status) || !empty($date);
?>

<!-- Filter Panel -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3 align-items-end">
            <input type="hidden" name="action" value="<?= htmlspecialchars($currentAction) ?>">
            <?php if (!empty($keyword)) : ?>
                <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
            <?php endif; ?>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Phim</label>
                <select name="movie_id" class="form-select">
                    <option value="">-- Tất cả phim --</option>
                    <?php if (!empty($movies)) : ?>
                        <?php foreach ($movies as $m) : ?>
                            <option value="<?= $m['id'] ?>" <?= (string)$movieId === (string)$m['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Phòng chiếu</label>
                <select name="room_id" class="form-select">
                    <option value="">-- Tất cả phòng --</option>
                    <?php if (!empty($rooms)) : ?>
                        <?php foreach ($rooms as $r) : ?>
                            <option value="<?= $r['id'] ?>" <?= (string)$roomId === (string)$r['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="upcoming" <?= $status === 'upcoming' ? 'selected' : '' ?>>Sắp chiếu</option>
                    <option value="showing" <?= $status === 'showing' ? 'selected' : '' ?>>Đang chiếu</option>
                    <option value="ended" <?= $status === 'ended' ? 'selected' : '' ?>>Đã chiếu</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Ngày chiếu</label>
                <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($date) ?>">
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Lọc
                </button>
                <a href="<?= BASE_URL ?>?action=<?= htmlspecialchars($currentAction) ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Làm mới
                </a>
            </div>
        </form>
    </div>
</div>

<?php if ($hasFilter) : ?>
    <div class="alert alert-info py-2 mb-3">
        <i class="bi bi-info-circle me-1"></i>
        Showing <?= count($showtimes) ?> result(s)
        <?php if (!empty($keyword)) : ?>
            for "<strong><?= htmlspecialchars($keyword) ?></strong>"
        <?php endif; ?>
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
                        <th width="70">STT</th>
                        <th>Phim</th>
                        <th>Phòng</th>
                        <th>Bắt đầu</th>
                        <th>Kết thúc</th>
                        <th>Giá cơ sở</th>
                        <th>Đã đặt / Tổng ghế</th>
                        <th width="280">Thao tác</th>
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

                            <td class="text-center fw-semibold">
                                <span class="badge text-bg-light border px-2 py-1">
                                    <span class="text-danger fw-bold"><?= (int)($showtime['booked_seats'] ?? 0) ?></span> / <?= (int)($showtime['total_seats'] ?? 0) ?> ghế
                                </span>
                            </td>

                            <td class="text-center">

                                <a href="?action=showtimeSeats&id=<?= (int)$showtime['id'] ?>"
                                   class="btn btn-outline-secondary btn-sm"
                                   title="Xem sơ đồ ghế theo suất chiếu">
                                    <i class="bi bi-grid-3x3-gap me-1"></i>
                                    Sơ đồ ghế
                                </a>

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
                        <td colspan="8" class="text-center py-4">
                            <div class="alert alert-warning mb-0 d-inline-block px-4" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                No matching data found.
                            </div>
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>
    </div>
</div>