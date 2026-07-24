<?php

$errors = $errors ?? [];
$old = $old ?? [];
$movies = $movies ?? [];
$rooms = $rooms ?? [];

$cleaningTime = defined('SHOWTIME_CLEANING_TIME')
    ? (int) SHOWTIME_CLEANING_TIME
    : 20;

$oldMovieId = $old['movie_id'] ?? '';
$oldRoomId = $old['room_id'] ?? '';
$oldStartTime = $old['start_time'] ?? '';
$oldBasePrice = $old['base_price'] ?? '';

/*
|--------------------------------------------------------------------------
| Chuẩn hóa dữ liệu datetime-local
|--------------------------------------------------------------------------
| Input datetime-local cần định dạng Y-m-d\TH:i.
*/
if (!empty($oldStartTime)) {
    $timestamp = strtotime($oldStartTime);

    if ($timestamp !== false) {
        $oldStartTime = date('Y-m-d\TH:i', $timestamp);
    }
}
?>

<div class="container-fluid">

    <!-- Tiêu đề trang -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h3 class="fw-bold mb-1">
                Thêm suất chiếu
            </h3>

            <p class="text-secondary mb-0">
                Tạo lịch chiếu mới cho phim và phòng chiếu.
            </p>
        </div>

        <a href="?action=showtimes" class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left me-1"></i>

            Quay lại danh sách

        </a>

    </div>

    <form
        action="?action=showtime_store"
        method="POST"
        id="showtimeForm">

        <div class="row g-4">

            <!-- Cột nhập dữ liệu -->
            <div class="col-xl-8">

                <div class="card border-0 shadow-sm">

                    <div class="card-header bg-white border-bottom py-3">

                        <div class="d-flex align-items-center">

                            <div
                                class="d-flex align-items-center justify-content-center
                                       rounded-3 bg-primary-subtle text-primary me-3"
                                style="width: 42px; height: 42px;">

                                <i class="bi bi-calendar-plus fs-5"></i>

                            </div>

                            <div>

                                <h5 class="fw-bold mb-1">
                                    Thông tin suất chiếu
                                </h5>

                                <p class="text-secondary small mb-0">
                                    Nhập phim, phòng chiếu, thời gian và giá vé cơ sở.
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="card-body p-4">

                        <div class="row g-4">

                            <!-- Phim -->
                            <div class="col-md-6">

                                <label for="movie_id" class="form-label fw-semibold">

                                    Phim

                                    <span class="text-danger">*</span>

                                </label>

                                <select
                                    name="movie_id"
                                    id="movie_id"
                                    class="form-select <?= isset($errors['movie_id'])
                                        ? 'is-invalid'
                                        : '' ?>">

                                    <option value="">
                                        -- Chọn phim --
                                    </option>

                                    <?php foreach ($movies as $movie): ?>

                                        <?php
                                        $movieId = (int) ($movie['id'] ?? 0);
                                        $movieTitle = $movie['title'] ?? '';
                                        $movieDuration = (int) ($movie['duration'] ?? 0);
                                        ?>

                                        <option
                                            value="<?= $movieId ?>"
                                            data-duration="<?= $movieDuration ?>"
                                            <?= (string) $oldMovieId === (string) $movieId
                                                ? 'selected'
                                                : '' ?>>

                                            <?= htmlspecialchars(
                                                $movieTitle,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                            <?php if ($movieDuration > 0): ?>

                                                (<?= $movieDuration ?> phút)

                                            <?php endif; ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                                <?php if (isset($errors['movie_id'])): ?>

                                    <div class="invalid-feedback">

                                        <?= htmlspecialchars(
                                            $errors['movie_id'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </div>

                                <?php endif; ?>

                            </div>

                            <!-- Phòng chiếu -->
                            <div class="col-md-6">

                                <label for="room_id" class="form-label fw-semibold">

                                    Phòng chiếu

                                    <span class="text-danger">*</span>

                                </label>

                                <select
                                    name="room_id"
                                    id="room_id"
                                    class="form-select <?= isset($errors['room_id'])
                                        ? 'is-invalid'
                                        : '' ?>">

                                    <option value="">
                                        -- Chọn phòng --
                                    </option>

                                    <?php foreach ($rooms as $room): ?>

                                        <?php
                                        $roomId = (int) ($room['id'] ?? 0);
                                        $roomName = $room['name'] ?? '';
                                        ?>

                                        <option
                                            value="<?= $roomId ?>"
                                            <?= (string) $oldRoomId === (string) $roomId
                                                ? 'selected'
                                                : '' ?>>

                                            <?= htmlspecialchars(
                                                $roomName,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                                <?php if (isset($errors['room_id'])): ?>

                                    <div class="invalid-feedback">

                                        <?= htmlspecialchars(
                                            $errors['room_id'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </div>

                                <?php endif; ?>

                            </div>

                            <!-- Giờ bắt đầu -->
                            <div class="col-md-6">

                                <label for="start_time" class="form-label fw-semibold">

                                    Giờ bắt đầu

                                    <span class="text-danger">*</span>

                                </label>

                                <div class="input-group has-validation">

                                    <span class="input-group-text bg-light">

                                        <i class="bi bi-calendar-event"></i>

                                    </span>

                                    <input
                                        type="datetime-local"
                                        name="start_time"
                                        id="start_time"
                                        min="<?= date('Y-m-d\TH:i') ?>"
                                        value="<?= htmlspecialchars(
                                            $oldStartTime,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        class="form-control <?= isset($errors['start_time'])
                                            ? 'is-invalid'
                                            : '' ?>">

                                    <?php if (isset($errors['start_time'])): ?>

                                        <div class="invalid-feedback">

                                            <?= htmlspecialchars(
                                                $errors['start_time'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </div>

                                    <?php endif; ?>

                                </div>

                            </div>

                            <!-- Giá cơ sở -->
                            <div class="col-md-6">

                                <label for="base_price" class="form-label fw-semibold">

                                    Giá cơ sở

                                    <span class="text-danger">*</span>

                                </label>

                                <div class="input-group has-validation">

                                    <span class="input-group-text bg-light">

                                        <i class="bi bi-cash-coin"></i>

                                    </span>

                                    <input
                                        type="number"
                                        name="base_price"
                                        id="base_price"
                                        min="1000"
                                        step="1000"
                                        inputmode="numeric"
                                        placeholder="Ví dụ: 80000"
                                        value="<?= htmlspecialchars(
                                            $oldBasePrice,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        class="form-control <?= isset($errors['base_price'])
                                            ? 'is-invalid'
                                            : '' ?>">

                                    <span class="input-group-text">
                                        VNĐ
                                    </span>

                                    <?php if (isset($errors['base_price'])): ?>

                                        <div class="invalid-feedback">

                                            <?= htmlspecialchars(
                                                $errors['base_price'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </div>

                                    <?php endif; ?>

                                </div>

                                <div class="form-text">
                                    Đây là mức giá cơ sở trước khi áp dụng loại ghế hoặc phụ thu.
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="card-footer bg-white border-top p-4">

                        <div class="d-flex flex-wrap justify-content-end gap-2">

                            <a
                                href="?action=showtime_create"
                                class="btn btn-outline-secondary">

                                <i class="bi bi-arrow-clockwise me-1"></i>

                                Làm mới

                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                id="submitButton">

                                <i class="bi bi-floppy me-1"></i>

                                Lưu suất chiếu

                            </button>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Cột thông tin dự kiến -->
            <div class="col-xl-4">

                <div class="card border-0 shadow-sm">

                    <div class="card-header bg-white border-bottom py-3">

                        <div class="d-flex align-items-center">

                            <div
                                class="d-flex align-items-center justify-content-center
                                       rounded-3 bg-success-subtle text-success me-3"
                                style="width: 42px; height: 42px;">

                                <i class="bi bi-clock-history fs-5"></i>

                            </div>

                            <div>

                                <h5 class="fw-bold mb-1">
                                    Thời gian dự kiến
                                </h5>

                                <p class="text-secondary small mb-0">
                                    Hệ thống tự động tính giờ kết thúc.
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="card-body p-4">

                        <!-- Thời lượng phim -->
                        <div
                            class="d-flex justify-content-between align-items-center
                                   rounded-3 border p-3 mb-3">

                            <div class="d-flex align-items-center">

                                <div
                                    class="d-flex align-items-center justify-content-center
                                           rounded-circle bg-primary-subtle text-primary me-3"
                                    style="width: 40px; height: 40px;">

                                    <i class="bi bi-film"></i>

                                </div>

                                <div>

                                    <div class="small text-secondary">
                                        Thời lượng phim
                                    </div>

                                    <div class="fw-semibold">
                                        Nội dung phim
                                    </div>

                                </div>

                            </div>

                            <span
                                id="movie_duration"
                                class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">

                                --

                            </span>

                        </div>

                        <!-- Thời gian dọn dẹp -->
                        <div
                            class="d-flex justify-content-between align-items-center
                                   rounded-3 border p-3 mb-3">

                            <div class="d-flex align-items-center">

                                <div
                                    class="d-flex align-items-center justify-content-center
                                           rounded-circle bg-warning-subtle text-warning-emphasis me-3"
                                    style="width: 40px; height: 40px;">

                                    <i class="bi bi-stars"></i>

                                </div>

                                <div>

                                    <div class="small text-secondary">
                                        Thời gian dọn dẹp
                                    </div>

                                    <div class="fw-semibold">
                                        Chuẩn bị phòng
                                    </div>

                                </div>

                            </div>

                            <span
                                class="badge rounded-pill
                                       bg-warning-subtle text-warning-emphasis px-3 py-2">

                                <?= $cleaningTime ?> phút

                            </span>

                        </div>

                        <!-- Tổng thời gian -->
                        <div
                            class="d-flex justify-content-between align-items-center
                                   rounded-3 border border-success-subtle
                                   bg-success-subtle p-3 mb-4">

                            <div class="d-flex align-items-center">

                                <div
                                    class="d-flex align-items-center justify-content-center
                                           rounded-circle bg-white text-success me-3"
                                    style="width: 40px; height: 40px;">

                                    <i class="bi bi-hourglass-split"></i>

                                </div>

                                <div>

                                    <div class="small text-success-emphasis">
                                        Tổng thời gian
                                    </div>

                                    <div class="fw-bold text-success-emphasis">
                                        Sử dụng phòng
                                    </div>

                                </div>

                            </div>

                            <span
                                id="total_duration"
                                class="badge rounded-pill bg-success text-white px-3 py-2">

                                --

                            </span>

                        </div>

                        <!-- Giờ bắt đầu dự kiến -->
                        <div class="mb-3">

                            <label class="form-label small text-secondary mb-1">
                                Giờ bắt đầu
                            </label>

                            <div class="input-group">

                                <span class="input-group-text bg-light">

                                    <i class="bi bi-play-circle"></i>

                                </span>

                                <input
                                    type="text"
                                    id="preview_start_time"
                                    class="form-control bg-light"
                                    value="Chưa chọn thời gian"
                                    readonly>

                            </div>

                        </div>

                        <!-- Giờ kết thúc dự kiến -->
                        <div>

                            <label for="end_time" class="form-label small text-secondary mb-1">
                                Giờ kết thúc dự kiến
                            </label>

                            <div class="input-group">

                                <span class="input-group-text bg-light">

                                    <i class="bi bi-stop-circle"></i>

                                </span>

                                <input
                                    type="text"
                                    id="end_time"
                                    class="form-control bg-light fw-semibold"
                                    value=""
                                    placeholder="Chọn phim và giờ bắt đầu"
                                    readonly>

                            </div>

                        </div>

                        <div class="alert alert-light border mt-4 mb-0">

                            <div class="d-flex">

                                <i class="bi bi-info-circle text-primary me-2 mt-1"></i>

                                <small class="text-secondary">

                                    Giờ kết thúc bao gồm thời lượng phim và
                                    <strong><?= $cleaningTime ?> phút</strong>
                                    dọn dẹp phòng.

                                </small>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const CLEANING_TIME = <?= $cleaningTime ?>;

    const movieSelect = document.getElementById('movie_id');
    const startInput = document.getElementById('start_time');

    const movieDurationElement =
        document.getElementById('movie_duration');

    const totalDurationElement =
        document.getElementById('total_duration');

    const previewStartInput =
        document.getElementById('preview_start_time');

    const endTimeInput =
        document.getElementById('end_time');

    const form =
        document.getElementById('showtimeForm');

    const submitButton =
        document.getElementById('submitButton');

    if (
        !movieSelect ||
        !startInput ||
        !movieDurationElement ||
        !totalDurationElement ||
        !previewStartInput ||
        !endTimeInput
    ) {
        return;
    }

    /**
     * Chuyển Date thành định dạng dd/mm/yyyy HH:mm.
     */
    function formatDateTime(date) {
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const hour = String(date.getHours()).padStart(2, '0');
        const minute = String(date.getMinutes()).padStart(2, '0');

        return `${day}/${month}/${year} ${hour}:${minute}`;
    }

    /**
     * Lấy thời lượng phim đang được chọn.
     */
    function getSelectedMovieDuration() {
        const selectedOption =
            movieSelect.options[movieSelect.selectedIndex];

        if (!selectedOption) {
            return 0;
        }

        const duration = Number.parseInt(
            selectedOption.dataset.duration || '0',
            10
        );

        return Number.isNaN(duration) ? 0 : duration;
    }

    /**
     * Xóa thông tin thời gian dự kiến.
     */
    function resetPreview() {
        movieDurationElement.textContent = '--';
        totalDurationElement.textContent = '--';
        previewStartInput.value = 'Chưa chọn thời gian';
        endTimeInput.value = '';
    }

    /**
     * Cập nhật thời lượng và giờ kết thúc dự kiến.
     */
    function updateShowtimePreview() {
        const movieDuration = getSelectedMovieDuration();

        if (movieDuration <= 0) {
            resetPreview();
            return;
        }

        const totalDuration =
            movieDuration + CLEANING_TIME;

        movieDurationElement.textContent =
            `${movieDuration} phút`;

        totalDurationElement.textContent =
            `${totalDuration} phút`;

        if (!startInput.value) {
            previewStartInput.value = 'Chưa chọn thời gian';
            endTimeInput.value = '';
            return;
        }

        const startDate =
            new Date(startInput.value);

        if (Number.isNaN(startDate.getTime())) {
            previewStartInput.value = 'Thời gian không hợp lệ';
            endTimeInput.value = '';
            return;
        }

        const endDate =
            new Date(startDate.getTime());

        endDate.setMinutes(
            endDate.getMinutes() + totalDuration
        );

        previewStartInput.value =
            formatDateTime(startDate);

        endTimeInput.value =
            formatDateTime(endDate);
    }

    movieSelect.addEventListener(
        'change',
        updateShowtimePreview
    );

    startInput.addEventListener(
        'change',
        updateShowtimePreview
    );

    startInput.addEventListener(
        'input',
        updateShowtimePreview
    );

    /**
     * Ngăn người dùng nhấn nút lưu nhiều lần.
     */
    if (form && submitButton) {
        form.addEventListener('submit', function () {
            if (!form.checkValidity()) {
                return;
            }

            submitButton.disabled = true;

            submitButton.innerHTML = `
                <span
                    class="spinner-border spinner-border-sm me-2"
                    aria-hidden="true">
                </span>
                Đang lưu...
            `;
        });
    }

    updateShowtimePreview();
});
</script>