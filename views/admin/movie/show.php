
<?php
$statusClass = match ($movie['status']) {
    'coming_soon' => 'warning',
    'now_showing' => 'success',
    'ended' => 'secondary',
    default => 'dark',
};

$statusLabel = match ($movie['status']) {
    'coming_soon' => 'Sắp chiếu',
    'now_showing' => 'Đang chiếu',
    'ended' => 'Ngừng chiếu',
    default => 'Không xác định',
};

$ageClass = match ($movie['age_rating']) {
    'P' => 'success',
    'K' => 'info',
    'T13' => 'primary',
    'T16' => 'warning',
    'T18' => 'danger',
    'C' => 'dark',
    default => 'secondary',
};

// Chuyển link YouTube sang dạng embed
$embedUrl = '';

if (!empty($movie['trailer'])) {

    if (preg_match('/youtu\.be\/([^\?&]+)/', $movie['trailer'], $match)) {
        $embedUrl = "https://www.youtube.com/embed/" . $match[1];
    } elseif (preg_match('/v=([^\?&]+)/', $movie['trailer'], $match)) {
        $embedUrl = "https://www.youtube.com/embed/" . $match[1];
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><?= htmlspecialchars($movie['title']) ?></h3>
        <p class="text-muted mb-0">Chi tiết thông tin phim</p>
    </div>

    <div>
        <a href="<?= BASE_URL ?>?action=movie_edit&id=<?= $movie['id'] ?>"
           class="btn btn-primary">
            <i class="bi bi-pencil-square me-1"></i>
            Chỉnh sửa
        </a>

        <a href="<?= BASE_URL ?>?action=movie_list"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Quay lại
        </a>
    </div>
</div>

<div class="row g-4">

    <!-- Poster -->
    <div class="col-lg-4">

        <div class="card shadow-sm">

            <div class="card-body text-center">

                <img
                    src="<?= BASE_ASSETS_UPLOADS . $movie['poster'] ?>"
                    alt="<?= htmlspecialchars($movie['title']) ?>"
                    class="img-fluid rounded shadow"
                    style="max-height:560px;object-fit:cover;">

                <div class="mt-4">

                    <span class="badge bg-<?= $statusClass ?> fs-6">
                        <?= $statusLabel ?>
                    </span>

                    <span class="badge bg-<?= $ageClass ?> fs-6 ms-2">
                        <?= $movie['age_rating'] ?>
                    </span>

                </div>

            </div>

        </div>

    </div>

    <!-- Thông tin -->
    <div class="col-lg-8">

        <div class="card shadow-sm">

            <div class="card-header">
                <h5 class="mb-0">
                    Thông tin phim
                </h5>
            </div>

            <div class="card-body">

                <div class="row gy-4">

                    <div class="col-md-6">
                        <small class="text-muted">Tên phim</small>
                        <div class="fw-semibold fs-5">
                            <?= htmlspecialchars($movie['title']) ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted">Thể loại</small>
                        <div>
                            <?= htmlspecialchars($movie['genres']) ?>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <small class="text-muted">Thời lượng</small>
                        <div><?= $movie['duration'] ?> phút</div>
                    </div>

                    <div class="col-md-4">
                        <small class="text-muted">Ngày khởi chiếu</small>
                        <div><?= date('d/m/Y', strtotime($movie['release_date'])) ?></div>
                    </div>

                    <div class="col-md-4">
                        <small class="text-muted">Ngôn ngữ</small>
                        <div><?= htmlspecialchars($movie['language']) ?></div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted">Đạo diễn</small>
                        <div><?= htmlspecialchars($movie['director']) ?></div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted">Diễn viên</small>
                        <div><?= htmlspecialchars($movie['actors']) ?></div>
                    </div>

                    <div class="col-12">
                        <small class="text-muted">Mô tả</small>

                        <div class="border rounded p-3 bg-light mt-2">
                            <?= nl2br(htmlspecialchars($movie['description'])) ?>
                        </div>
                    </div>

                    <?php if (!empty($embedUrl)) : ?>

                        <div class="col-12">

                            <small class="text-muted">
                                Trailer
                            </small>

                            <div class="ratio ratio-16x9 mt-2">

                                <iframe
                                    src="<?= $embedUrl ?>"
                                    title="Trailer"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen>
                                </iframe>

                            </div>

                        </div>

                    <?php elseif (!empty($movie['trailer'])) : ?>

                        <div class="col-12">

                            <small class="text-muted">
                                Trailer
                            </small>

                            <div class="mt-2">

                                <a href="<?= htmlspecialchars($movie['trailer']) ?>"
                                   target="_blank"
                                   class="btn btn-outline-danger">

                                    <i class="bi bi-youtube me-1"></i>
                                    Xem trailer

                                </a>

                            </div>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>
```
