<div class="container py-4">

    <!-- Header & Filter Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1"><i class="bi bi-calendar-event me-2 text-warning"></i>Phim Sắp Chiếu</h2>
            <p class="text-muted small mb-0">Các tác phẩm điện ảnh chuẩn bị ra mắt khán giả</p>
        </div>

        <form method="GET" action="" class="d-flex flex-wrap gap-2">
            <input type="hidden" name="action" value="upcoming">
            
            <input 
                type="text" 
                name="keyword" 
                class="form-control bg-dark text-white border-secondary" 
                placeholder="Tìm tên phim..." 
                value="<?= h($_GET['keyword'] ?? '') ?>"
                style="max-width: 200px;">

            <button type="submit" class="btn btn-peta-secondary">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>

    <!-- Movie Card Grid -->
    <?php if (!empty($movies)): ?>
        <div class="row g-4">
            <?php foreach ($movies as $movie): ?>
                <?php 
                    $posterUrl   = !empty($movie['poster']) ? BASE_ASSETS_UPLOADS . $movie['poster'] : 'https://placehold.co/400x600/1e293b/ffffff?text=' . urlencode($movie['title']);
                    $ageBadge    = $movie['age_rating'] ?? 'P';
                    $releaseDate = !empty($movie['release_date']) ? date('d/m/Y', strtotime($movie['release_date'])) : 'Sắp khởi chiếu';
                ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="peta-card h-100 d-flex flex-column">
                        <div class="position-relative overflow-hidden" style="aspect-ratio: 2/3;">
                            <img src="<?= $posterUrl ?>" alt="<?= h($movie['title']) ?>" class="w-100 h-100 object-fit-cover">
                            <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-3 px-2 py-1 fs-7">Sắp Khởi Chiếu</span>
                            <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-80 text-warning text-center py-2 fw-semibold small border-top border-secondary">
                                <i class="bi bi-calendar-check me-1"></i> Khởi chiếu: <?= $releaseDate ?>
                            </div>
                        </div>
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <h5 class="fw-bold text-white text-truncate mb-2" title="<?= h($movie['title']) ?>"><?= h($movie['title']) ?></h5>
                            <div class="small text-muted mb-3">
                                <div><i class="bi bi-clock me-1"></i> <?= h($movie['duration']) ?> phút</div>
                                <div><i class="bi bi-tag me-1"></i> <?= h($movie['genres']) ?></div>
                            </div>
                            <div class="mt-auto d-flex gap-2">
                                <a href="?action=movie_show&id=<?= $movie['id'] ?>" class="btn btn-peta-outline btn-sm flex-fill">
                                    Chi tiết
                                </a>
                                <button type="button" class="btn btn-peta-secondary btn-sm flex-fill" onclick="alert('Đã đăng ký nhận thông báo khi phim ra mắt!')">
                                    <i class="bi bi-bell me-1"></i> Nhắc tôi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-secondary text-center py-5">
            <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
            Chưa có thông tin phim sắp chiếu.
        </div>
    <?php endif; ?>

</div>
