<div class="card shadow-sm border-0">

    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">
            <i class="bi bi-camera-reels"></i>
            Chi tiết suất chiếu
        </h4>

        <a href="?action=showtimes" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Quay lại
        </a>
    </div>

    <div class="card-body">

        <div class="row">

            <!-- Poster -->
            <div class="col-md-3 text-center">

                <img
                    src="<?= BASE_ASSETS_UPLOADS . $showtime['poster'] ?>"
                    class="img-fluid rounded shadow"
                    style="max-height:420px; object-fit:cover;">

            </div>

            <!-- Information -->
            <div class="col-md-9">

                <h3 class="fw-bold mb-4">
                    <?= htmlspecialchars($showtime['movie_title']) ?>
                </h3>

                <div class="row g-4">

                    <div class="col-md-6">
                        <label class="text-secondary small">
                            Phòng chiếu
                        </label>

                        <div class="fw-semibold fs-5">
                            <?= $showtime['room_name'] ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-secondary small">
                            Loại phòng
                        </label>

                        <div class="fw-semibold fs-5">
                            <?= $showtime['room_type'] ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-secondary small">
                            Bắt đầu
                        </label>

                        <div class="fw-semibold">
                            <?= date('Y-m-d H:i', strtotime($showtime['start_time'])) ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-secondary small">
                            Kết thúc
                        </label>

                        <div class="fw-semibold">
                            <?= date('Y-m-d H:i', strtotime($showtime['end_time'])) ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-secondary small">
                            Thời lượng
                        </label>

                        <div class="fw-semibold">
                            <?= $showtime['duration'] ?> phút
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-secondary small">
                            Giá cơ sở
                        </label>

                        <div class="fw-bold text-danger fs-4">
                            <?= number_format($showtime['base_price'],0,',','.') ?> đ
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="card-footer bg-white d-flex justify-content-end gap-2">

        <a href="?action=showtime_edit&id=<?= $showtime['id'] ?>"
           class="btn btn-warning">
            <i class="bi bi-pencil-square"></i>
            Chỉnh sửa

        </a>

    </div>

</div>