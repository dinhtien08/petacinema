<!-- <div class="alert alert-info py-2 mb-3">
    <i class="bi bi-info-circle me-1"></i>
    Chức năng này chỉ dùng để kiểm thử dữ liệu. Booking thực tế được tạo từ quy trình đặt vé online.
</div> -->

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Booking</h4>
        <p class="text-muted mb-0">Quản lý đơn đặt vé (Read-only)</p>
    </div>

    <!-- <a href="<?= BASE_URL ?>?action=booking_add" class="btn btn-danger">
        <i class="bi bi-plus-lg me-1"></i>
        Thêm dữ liệu test
    </a> -->
</div>

<?php if (!empty($flash)) : ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>

<?php

/*
|--------------------------------------------------------------------------
| Khai báo múi giờ
|--------------------------------------------------------------------------
|
| Tốt nhất nên chuyển dòng này vào file config/index.php dùng chung.
|
*/

date_default_timezone_set('Asia/Ho_Chi_Minh');

/*
|--------------------------------------------------------------------------
| Lấy thông báo từ URL
|--------------------------------------------------------------------------
*/

$success = $_GET['success'] ?? '';
$error   = $_GET['error'] ?? '';

/*
|--------------------------------------------------------------------------
| Lấy dữ liệu bộ lọc
|--------------------------------------------------------------------------
|
| Nếu người dùng chưa chọn ngày thì mặc định lấy ngày hôm nay.
| Danh sách sẽ hiển thị từ ngày được chọn trở đi.
|
*/

$keyword = trim($_GET['keyword'] ?? '');
$movieId = $_GET['movie_id'] ?? '';
$roomId  = $_GET['room_id'] ?? '';
$status  = $_GET['status'] ?? '';

$today = date('Y-m-d');

$date = !empty($_GET['date'])
    ? $_GET['date']
    : $today;

$currentAction = $_GET['action'] ?? 'showtimes';

$hasFilter = $keyword !== ''
    || $movieId !== ''
    || $roomId !== ''
    || $status !== ''
    || (!empty($_GET['date']) && $_GET['date'] !== $today);

/*
|--------------------------------------------------------------------------
| Hàm escape HTML
|--------------------------------------------------------------------------
*/

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars(
            (string)$value,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

/*
|--------------------------------------------------------------------------
| Hàm xác định trạng thái suất chiếu
|--------------------------------------------------------------------------
*/

if (!function_exists('getShowtimeStatus')) {
    function getShowtimeStatus(
        string $startTime,
        string $endTime
    ): array {
        $now = time();

        $startTimestamp = strtotime($startTime);
        $endTimestamp   = strtotime($endTime);

        if ($now < $startTimestamp) {
            return [
                'value' => 'upcoming',
                'label' => 'Chưa chiếu',
                'class' => 'text-bg-primary',
                'icon'  => 'bi-clock-fill',
            ];
        }

        if ($now >= $startTimestamp && $now <= $endTimestamp) {
            return [
                'value' => 'showing',
                'label' => 'Đang chiếu',
                'class' => 'text-bg-success',
                'icon'  => 'bi-play-circle-fill',
            ];
        }

        return [
            'value' => 'ended',
            'label' => 'Đã kết thúc',
            'class' => 'text-bg-secondary',
            'icon'  => 'bi-check-circle-fill',
        ];
    }
}

/*
|--------------------------------------------------------------------------
| Xử lý danh sách suất chiếu
|--------------------------------------------------------------------------
|
| Sao chép dữ liệu từ Controller để xử lý tại View.
|
*/

$displayShowtimes = $showtimes ?? [];

/*
|--------------------------------------------------------------------------
| Chỉ lấy suất chiếu từ ngày được chọn trở đi
|--------------------------------------------------------------------------
|
| Mặc định $date là ngày hôm nay.
|
*/

$displayShowtimes = array_values(array_filter(
    $displayShowtimes,
    function ($showtime) use ($date) {
        if (empty($showtime['start_time'])) {
            return false;
        }

        $showtimeDate = date(
            'Y-m-d',
            strtotime($showtime['start_time'])
        );

        return $showtimeDate >= $date;
    }
));

/*
|--------------------------------------------------------------------------
| Lọc theo trạng thái
|--------------------------------------------------------------------------
|
| Phần này dùng trong trường hợp Controller chưa xử lý lọc trạng thái.
|
*/

if ($status !== '') {
    $displayShowtimes = array_values(array_filter(
        $displayShowtimes,
        function ($showtime) use ($status) {
            $showtimeStatus = getShowtimeStatus(
                $showtime['start_time'],
                $showtime['end_time']
            );

            return $showtimeStatus['value'] === $status;
        }
    ));
}

/*
|--------------------------------------------------------------------------
| Sắp xếp suất chiếu gần nhất lên đầu
|--------------------------------------------------------------------------
*/

usort(
    $displayShowtimes,
    function ($first, $second) {
        return strtotime($first['start_time'])
            <=> strtotime($second['start_time']);
    }
);

/*
|--------------------------------------------------------------------------
| Ngày chọn nhanh
|--------------------------------------------------------------------------
*/

$tomorrow = date(
    'Y-m-d',
    strtotime('+1 day')
);

$nextTwoDays = date(
    'Y-m-d',
    strtotime('+2 days')
);

$nextThreeDays = date(
    'Y-m-d',
    strtotime('+3 days')
);

/*
|--------------------------------------------------------------------------
| Thống kê trạng thái
|--------------------------------------------------------------------------
*/

$totalShowtimes    = count($displayShowtimes);
$upcomingShowtimes = 0;
$showingShowtimes  = 0;
$endedShowtimes    = 0;

foreach ($displayShowtimes as $showtime) {
    $showtimeStatus = getShowtimeStatus(
        $showtime['start_time'],
        $showtime['end_time']
    );

    switch ($showtimeStatus['value']) {
        case 'upcoming':
            $upcomingShowtimes++;
            break;

        case 'showing':
            $showingShowtimes++;
            break;

        case 'ended':
            $endedShowtimes++;
            break;
    }
}

?>

<!-- Thông báo xóa thành công -->
<?php if ($success === 'deleted'): ?>

    <div
        class="alert alert-success alert-dismissible fade show"
        role="alert">

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

<!-- Thông báo lỗi -->
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

    <div
        class="alert alert-danger alert-dismissible fade show"
        role="alert">

        <i class="bi bi-exclamation-triangle-fill me-2"></i>

        <?= e($errorMessage) ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Đóng">
        </button>

    </div>

<?php endif; ?>

<!-- Tiêu đề trang -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

    <div>
        <h3 class="mb-1">Quản lý suất chiếu</h3>

        <p class="text-muted mb-0">
            Hiển thị các suất chiếu từ
            <strong><?= date('d/m/Y', strtotime($date)) ?></strong>
            trở đi.
        </p>
    </div>

    <a
        href="<?= BASE_URL ?>?action=showtime_create"
        class="btn btn-primary">

        <i class="bi bi-plus-circle me-1"></i>
        Thêm suất chiếu

    </a>

</div>

<!-- Thống kê nhanh -->
<div class="row g-3 mb-4">

    <div class="col-xl-3 col-md-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body d-flex align-items-center gap-3">

                <div class="bg-dark-subtle rounded-3 p-3">
                    <i class="bi bi-camera-reels fs-4"></i>
                </div>

                <div>
                    <div class="text-muted small">
                        Tổng suất chiếu
                    </div>

                    <div class="fs-4 fw-bold">
                        <?= $totalShowtimes ?>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <div class="col-xl-3 col-md-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body d-flex align-items-center gap-3">

                <div class="bg-primary-subtle text-primary rounded-3 p-3">
                    <i class="bi bi-clock-fill fs-4"></i>
                </div>

                <div>
                    <div class="text-muted small">
                        Chưa chiếu
                    </div>

                    <div class="fs-4 fw-bold text-primary">
                        <?= $upcomingShowtimes ?>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <div class="col-xl-3 col-md-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body d-flex align-items-center gap-3">

                <div class="bg-success-subtle text-success rounded-3 p-3">
                    <i class="bi bi-play-circle-fill fs-4"></i>
                </div>

                <div>
                    <div class="text-muted small">
                        Đang chiếu
                    </div>

                    <div class="fs-4 fw-bold text-success">
                        <?= $showingShowtimes ?>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <div class="col-xl-3 col-md-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body d-flex align-items-center gap-3">

                <div class="bg-secondary-subtle text-secondary rounded-3 p-3">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                </div>

                <div>
                    <div class="text-muted small">
                        Đã kết thúc
                    </div>

                    <div class="fs-4 fw-bold text-secondary">
                        <?= $endedShowtimes ?>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

<!-- Chọn nhanh ngày bắt đầu -->
<div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <div class="d-flex flex-wrap align-items-center gap-2">

            <span class="fw-semibold me-2">
                <i class="bi bi-calendar3 me-1"></i>
                Hiển thị từ:
            </span>

            <a
                href="<?= BASE_URL ?>?action=<?= e($currentAction) ?>&date=<?= $today ?>"
                class="btn <?= $date === $today ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">

                Hôm nay

            </a>

            <a
                href="<?= BASE_URL ?>?action=<?= e($currentAction) ?>&date=<?= $tomorrow ?>"
                class="btn <?= $date === $tomorrow ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">

                Ngày mai

            </a>

            <a
                href="<?= BASE_URL ?>?action=<?= e($currentAction) ?>&date=<?= $nextTwoDays ?>"
                class="btn <?= $date === $nextTwoDays ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">

                <?= date('d/m', strtotime($nextTwoDays)) ?>

            </a>

            <a
                href="<?= BASE_URL ?>?action=<?= e($currentAction) ?>&date=<?= $nextThreeDays ?>"
                class="btn <?= $date === $nextThreeDays ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">

                <?= date('d/m', strtotime($nextThreeDays)) ?>

            </a>

        </div>

    </div>

</div>

<!-- Bộ lọc -->
<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">

        <h5 class="mb-0">
            <i class="bi bi-funnel me-2"></i>
            Bộ lọc suất chiếu
        </h5>

    </div>

    <div class="card-body">

        <form
            method="GET"
            action=""
            class="row g-3 align-items-end">

            <input
                type="hidden"
                name="action"
                value="<?= e($currentAction) ?>">

            <?php if ($keyword !== ''): ?>

                <input
                    type="hidden"
                    name="keyword"
                    value="<?= e($keyword) ?>">

            <?php endif; ?>

            <div class="col-xl-3 col-md-6">

                <label class="form-label fw-semibold">
                    Phim
                </label>

                <select
                    name="movie_id"
                    class="form-select">

                    <option value="">
                        Tất cả phim
                    </option>

                    <?php foreach (($movies ?? []) as $movie): ?>

                        <option
                            value="<?= (int)$movie['id'] ?>"
                            <?= (string)$movieId === (string)$movie['id']
                                ? 'selected'
                                : '' ?>>

                            <?= e($movie['title']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="col-xl-2 col-md-6">

                <label class="form-label fw-semibold">
                    Phòng chiếu
                </label>

                <select
                    name="room_id"
                    class="form-select">

                    <option value="">
                        Tất cả phòng
                    </option>

                    <?php foreach (($rooms ?? []) as $room): ?>

                        <option
                            value="<?= (int)$room['id'] ?>"
                            <?= (string)$roomId === (string)$room['id']
                                ? 'selected'
                                : '' ?>>

                            <?= e($room['name']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="col-xl-2 col-md-6">

                <label class="form-label fw-semibold">
                    Trạng thái
                </label>

                <select
                    name="status"
                    class="form-select">

                    <option value="">
                        Tất cả trạng thái
                    </option>

                    <option
                        value="upcoming"
                        <?= $status === 'upcoming'
                            ? 'selected'
                            : '' ?>>

                        Chưa chiếu

                    </option>

                    <option
                        value="showing"
                        <?= $status === 'showing'
                            ? 'selected'
                            : '' ?>>

                        Đang chiếu

                    </option>

                    <option
                        value="ended"
                        <?= $status === 'ended'
                            ? 'selected'
                            : '' ?>>

                        Đã kết thúc

                    </option>

                </select>

            </div>

            <div class="col-xl-2 col-md-6">

                <label class="form-label fw-semibold">
                    Hiển thị từ ngày
                </label>

                <input
                    type="date"
                    name="date"
                    class="form-control"
                    min="<?= $today ?>"
                    value="<?= e($date) ?>">

            </div>

            <div class="col-xl-3 col-md-12">

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary flex-grow-1">

                        <i class="bi bi-funnel me-1"></i>
                        Lọc

                    </button>

                    <a
                        href="<?= BASE_URL ?>?action=<?= e($currentAction) ?>"
                        class="btn btn-outline-secondary"
                        title="Đặt lại bộ lọc">

                        <i class="bi bi-arrow-counterclockwise"></i>

                    </a>

                </div>

            </div>

        </form>

    </div>

</div>

<!-- Thông báo kết quả lọc -->
<?php if ($hasFilter): ?>

    <div class="alert alert-info py-2 mb-3">

        <i class="bi bi-info-circle me-1"></i>

        Tìm thấy

        <strong>
            <?= count($displayShowtimes) ?>
        </strong>

        suất chiếu phù hợp.

        <?php if ($keyword !== ''): ?>

            Từ khóa:

            <strong>
                “<?= e($keyword) ?>”
            </strong>

        <?php endif; ?>

    </div>

<?php endif; ?>

<!-- Bảng danh sách -->
<div class="card border-0 shadow-sm">

    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">

        <div>

            <h4 class="mb-1">
                Danh sách suất chiếu
            </h4>

            <small class="text-muted">
                Từ <?= date('d/m/Y', strtotime($date)) ?> trở đi
            </small>

        </div>

        <span class="badge text-bg-light border px-3 py-2">
            <?= count($displayShowtimes) ?> suất chiếu
        </span>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr class="text-center">

                        <th width="65">STT</th>

                        <th class="text-start">
                            Phim
                        </th>

                        <th>
                            Phòng
                        </th>

                        <th>
                            Ngày chiếu
                        </th>

                        <th>
                            Bắt đầu
                        </th>

                        <th>
                            Kết thúc
                        </th>

                        <th>
                            Trạng thái
                        </th>

                        <th>
                            Giá cơ sở
                        </th>

                        <th>
                            Đã đặt / Tổng ghế
                        </th>

                        <th width="250">
                            Thao tác
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php if (!empty($displayShowtimes)): ?>

                        <?php foreach ($displayShowtimes as $index => $showtime): ?>

                            <?php
                            $showtimeStatus = getShowtimeStatus(
                                $showtime['start_time'],
                                $showtime['end_time']
                            );

                            $bookedSeats = (int)(
                                $showtime['booked_seats'] ?? 0
                            );

                            $totalSeats = (int)(
                                $showtime['total_seats'] ?? 0
                            );

                            $rowClass = $showtimeStatus['value'] === 'ended'
                                ? 'table-light'
                                : '';

                            $isToday = date(
                                'Y-m-d',
                                strtotime($showtime['start_time'])
                            ) === $today;
                            ?>

                            <tr class="<?= $rowClass ?>">

                                <td class="text-center">
                                    <?= $index + 1 ?>
                                </td>

                                <td>

                                    <div class="fw-semibold">
                                        <?= e($showtime['movie_title']) ?>
                                    </div>

                                    <small class="text-muted">
                                        Mã suất #<?= (int)$showtime['id'] ?>
                                    </small>

                                </td>

                                <td class="text-center">

                                    <span class="badge text-bg-light border">

                                        <i class="bi bi-door-open me-1"></i>

                                        <?= e($showtime['room_name']) ?>

                                    </span>

                                </td>

                                <td class="text-center">

                                    <div class="fw-semibold">

                                        <?= date(
                                            'd/m/Y',
                                            strtotime($showtime['start_time'])
                                        ) ?>

                                    </div>

                                    <?php if ($isToday): ?>

                                        <span class="badge text-bg-warning mt-1">
                                            Hôm nay
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td class="text-center fw-semibold">

                                    <?= date(
                                        'H:i',
                                        strtotime($showtime['start_time'])
                                    ) ?>

                                </td>

                                <td class="text-center fw-semibold">

                                    <?= date(
                                        'H:i',
                                        strtotime($showtime['end_time'])
                                    ) ?>

                                </td>

                                <td class="text-center">

                                    <span
                                        class="badge <?= $showtimeStatus['class'] ?> px-3 py-2">

                                        <i class="bi <?= $showtimeStatus['icon'] ?> me-1"></i>

                                        <?= $showtimeStatus['label'] ?>

                                    </span>

                                </td>

                                <td class="text-end">

                                    <?= number_format(
                                        (float)$showtime['base_price'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>

                                    đ

                                </td>

                                <td class="text-center fw-semibold">

                                    <span class="badge text-bg-light border px-2 py-1">

                                        <span class="text-danger fw-bold">
                                            <?= $bookedSeats ?>
                                        </span>

                                        /

                                        <?= $totalSeats ?>

                                        ghế

                                    </span>

                                </td>

                                <td class="text-center">

                                    <div class="d-flex justify-content-center gap-1">

                                        <a
                                            href="<?= BASE_URL ?>?action=showtimeSeats&id=<?= (int)$showtime['id'] ?>"
                                            class="btn btn-outline-secondary btn-sm"
                                            title="Xem sơ đồ ghế">

                                            <i class="bi bi-grid-3x3-gap"></i>

                                        </a>

                                        <a
                                            href="<?= BASE_URL ?>?action=showtime_show&id=<?= (int)$showtime['id'] ?>"
                                            class="btn btn-info btn-sm"
                                            title="Xem chi tiết">

                                            <i class="bi bi-eye"></i>

                                        </a>

                                        <?php if ($showtimeStatus['value'] === 'upcoming'): ?>

                                            <a
                                                href="<?= BASE_URL ?>?action=showtime_edit&id=<?= (int)$showtime['id'] ?>"
                                                class="btn btn-warning btn-sm"
                                                title="Chỉnh sửa">

                                                <i class="bi bi-pencil-square"></i>

                                            </a>

                                            <a
                                                href="<?= BASE_URL ?>?action=showtime_delete&id=<?= (int)$showtime['id'] ?>"
                                                class="btn btn-danger btn-sm"
                                                title="Xóa suất chiếu"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa suất chiếu này?')">

                                                <i class="bi bi-trash"></i>

                                            </a>

                                        <?php endif; ?>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="10"
                                class="text-center py-5">

                                <div class="mb-3">

                                    <i class="bi bi-calendar-x display-5 text-muted"></i>

                                </div>

                                <h5>
                                    Không tìm thấy suất chiếu
                                </h5>

                                <p class="text-muted mb-3">

                                    Không có suất chiếu nào từ ngày

                                    <strong>
                                        <?= date('d/m/Y', strtotime($date)) ?>
                                    </strong>

                                    trở đi.

                                </p>

                                <a
                                    href="<?= BASE_URL ?>?action=<?= e($currentAction) ?>"
                                    class="btn btn-outline-primary">

                                    <i class="bi bi-arrow-counterclockwise me-1"></i>

                                    Quay về hôm nay

                                </a>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>