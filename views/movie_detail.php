<?php
    $posterUrl = '';
    if (!empty($movie['poster'])) {
        $posterUrl = str_starts_with($movie['poster'], 'http') ? $movie['poster'] : BASE_ASSETS_UPLOADS . $movie['poster'];
    } else {
        $posterUrl = 'https://via.placeholder.com/300x450/1e293b/f59e0b?text=' . urlencode($movie['title']);
    }

    // Convert YouTube URL to embed format if available
    $embedTrailer = '';
    if (!empty($movie['trailer'])) {
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $movie['trailer'], $matches)) {
            $embedTrailer = 'https://www.youtube.com/embed/' . $matches[1];
        }
    }
    $ratingClass = 'badge-rating-' . ($movie['age_rating'] ?? 'P');
?>

<style>
    .movie-detail-page {
        color: var(--peta-text-main);
        font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: .94rem;
        font-weight: 400;
        letter-spacing: 0;
        line-height: 1.65;
    }
    .movie-detail-page .breadcrumb { font-size: .84rem; font-weight: 500; margin-bottom: 0; }
    .movie-detail-page .breadcrumb a { color: var(--peta-accent) !important; }
    .movie-detail-page .breadcrumb-item.active { color: var(--peta-text-muted) !important; }
    .movie-detail-card { border-radius: 1rem; box-shadow: 0 8px 24px rgba(15, 23, 42, .06); }
    .movie-detail-title { color: var(--peta-text-main); font-size: clamp(1.75rem, 3.2vw, 2.5rem); font-weight: 800; letter-spacing: -.035em; line-height: 1.15; margin: 0 0 1.3rem; }
    .movie-detail-page .badge { font-family: inherit; font-size: .72rem; font-weight: 700; letter-spacing: .045em; padding: .43rem .62rem; }
    .movie-detail-meta { color: var(--peta-text-main); font-size: .92rem; line-height: 1.55; }
    .movie-detail-meta tr + tr { border-top: 1px solid #edf0f5; }
    .movie-detail-meta td { padding: .68rem 0; vertical-align: top; }
    .movie-detail-meta .detail-label { color: var(--peta-text-muted); font-size: .84rem; font-weight: 600; width: 145px; }
    .movie-detail-meta .detail-value { color: var(--peta-text-main); font-weight: 600; }
    .movie-detail-meta i, .movie-detail-section-title i { color: var(--peta-accent) !important; }
    .movie-detail-section-title { border-left: 3px solid var(--peta-accent) !important; color: var(--peta-text-main) !important; font-size: 1rem; font-weight: 800; letter-spacing: .01em; line-height: 1.4; margin-bottom: .7rem; padding-left: .7rem; }
    .movie-detail-description { color: #475569; font-size: .95rem; line-height: 1.75; margin: 0; white-space: pre-line; }
    .movie-detail-page .movie-poster-wrapper { border-radius: .8rem !important; box-shadow: 0 10px 22px rgba(15, 23, 42, .18) !important; }
    .movie-detail-page .btn-peta { font-family: inherit; font-size: .95rem; font-weight: 800; letter-spacing: .025em; line-height: 1.35; }
    .movie-detail-trailer-title { color: var(--peta-text-main); font-size: 1rem; font-weight: 800; letter-spacing: .01em; margin-bottom: 1rem; }
    @media (max-width: 767.98px) { .movie-detail-card { padding: 1rem !important; } .movie-detail-meta .detail-label { width: 118px; } .movie-detail-meta { font-size: .88rem; } }
</style>

<section class="movie-detail-page">
<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-decoration-none"><i class="bi bi-house-door"></i> Trang chủ</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= h($movie['title']) ?></li>
    </ol>
</nav>

<!-- Movie Detail Main Section -->
<div class="card card-cinema movie-detail-card p-4 mb-5">
    <div class="row g-4">
        <!-- Poster & Action -->
        <div class="col-lg-4 col-md-5">
            <div class="movie-poster-wrapper rounded-3 mb-3 shadow">
                <img src="<?= h($posterUrl) ?>" class="movie-poster-img" alt="<?= h($movie['title']) ?>">
                <span class="age-rating-badge <?= $ratingClass ?> fs-6"><?= h($movie['age_rating'] ?? 'P') ?></span>
            </div>
            
            <a href="<?= BASE_URL ?>?action=booking_date&movie_id=<?= $movie['id'] ?>" class="btn btn-peta btn-lg w-100 py-3 text-uppercase shadow">
                <i class="bi bi-ticket-perforated-fill me-2"></i> ĐẶT VÉ NGAY
            </a>
        </div>

        <!-- Movie Infos -->
        <div class="col-lg-8 col-md-7 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark text-uppercase"><?= $movie['status'] === 'now_showing' ? 'Đang chiếu' : ($movie['status'] === 'coming_soon' ? 'Sắp chiếu' : 'Đã kết thúc') ?></span>
                    <span class="badge bg-secondary text-uppercase"><?= h($movie['age_rating'] ?? 'P') ?></span>
                </div>

                <h1 class="movie-detail-title"><?= h($movie['title']) ?></h1>

                <div class="table-responsive mb-4">
                    <table class="table table-borderless movie-detail-meta align-middle mb-0">
                        <tbody>
                            <tr>
                                <td class="detail-label ps-0"><i class="bi bi-tags-fill me-2"></i>Thể loại:</td>
                                <td class="detail-value"><?= h($movie['genres']) ?></td>
                            </tr>
                            <tr>
                                <td class="detail-label ps-0"><i class="bi bi-clock-fill me-2"></i>Thời lượng:</td>
                                <td class="detail-value"><?= h($movie['duration']) ?> phút</td>
                            </tr>
                            <tr>
                                <td class="detail-label ps-0"><i class="bi bi-calendar-event-fill me-2"></i>Khởi chiếu:</td>
                                <td class="detail-value"><?= h(date('d/m/Y', strtotime($movie['release_date'] ?? 'now'))) ?></td>
                            </tr>
                            <tr>
                                <td class="detail-label ps-0"><i class="bi bi-translate me-2"></i>Ngôn ngữ:</td>
                                <td class="detail-value"><?= h($movie['language'] ?? 'Tiếng Việt') ?></td>
                            </tr>
                            <tr>
                                <td class="detail-label ps-0"><i class="bi bi-person-badge-fill me-2"></i>Đạo diễn:</td>
                                <td class="detail-value"><?= h($movie['director'] ?? 'Đang cập nhật') ?></td>
                            </tr>
                            <tr>
                                <td class="detail-label ps-0"><i class="bi bi-people-fill me-2"></i>Diễn viên:</td>
                                <td class="detail-value"><?= h($movie['actors'] ?? 'Đang cập nhật') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <h4 class="movie-detail-section-title">Nội dung phim</h4>
                    <p class="movie-detail-description">
                        <?= h($movie['description'] ?? 'Chưa có mô tả nội dung.') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Trailer Section -->
<?php if (!empty($embedTrailer)): ?>
    <div class="card card-cinema movie-detail-card p-4 mb-5">
        <h4 class="movie-detail-trailer-title"><i class="bi bi-film me-2 text-danger"></i>Trailer phim</h4>
        <div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow">
            <iframe src="<?= h($embedTrailer) ?>" title="YouTube video player" allowfullscreen style="border:0;"></iframe>
        </div>
    </div>
<?php endif; ?>
</section>
