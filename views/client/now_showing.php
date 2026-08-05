<div class="container py-4">

    <!-- Header & Filter Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1"><i class="bi bi-film me-2 text-danger"></i>Phim Đang Chiếu</h2>
            <p class="text-muted small mb-0">Thưởng thức các bom tấn điện ảnh hấp dẫn nhất tại rạp</p>
        </div>

        <!-- Form Tìm kiếm & Lọc -->
        <form method="GET" action="" class="d-flex flex-wrap gap-2">
            <input type="hidden" name="action" value="now_showing">
            
            <input 
                type="text" 
                name="keyword" 
                class="form-control bg-dark text-white border-secondary" 
                placeholder="Tìm tên phim..." 
                value="<?= h($_GET['keyword'] ?? '') ?>"
                style="max-width: 200px;">

            <select name="genre" class="form-select bg-dark text-white border-secondary" style="max-width: 160px;">
                <option value="">-- Tất cả thể loại --</option>
                <option value="Hành động" <?= ($_GET['genre'] ?? '') === 'Hành động' ? 'selected' : '' ?>>Hành động</option>
                <option value="Khoa học viễn tưởng" <?= ($_GET['genre'] ?? '') === 'Khoa học viễn tưởng' ? 'selected' : '' ?>>Viễn tưởng</option>
                <option value="Hoạt hình" <?= ($_GET['genre'] ?? '') === 'Hoạt hình' ? 'selected' : '' ?>>Hoạt hình</option>
                <option value="Phiêu lưu" <?= ($_GET['genre'] ?? '') === 'Phiêu lưu' ? 'selected' : '' ?>>Phiêu lưu</option>
            </select>

            <button type="submit" class="btn btn-peta-primary">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>

    <!-- Movie Card Grid -->
    <?php if (!empty($movies)): ?>
        <div class="row g-4">
            <?php foreach ($movies as $movie): ?>
                <?php 
                    $posterUrl = !empty($movie['poster']) ? BASE_ASSETS_UPLOADS . $movie['poster'] : 'https://placehold.co/400x600/1e293b/ffffff?text=' . urlencode($movie['title']);
                    $ageBadge  = $movie['age_rating'] ?? 'P';
                    $releaseDate = !empty($movie['release_date']) ? date('d/m/Y', strtotime($movie['release_date'])) : 'Đang chiếu';
                ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="peta-card h-100 d-flex flex-direction-column">
                        <div class="position-relative overflow-hidden" style="aspect-ratio: 2/3;">
                            <img src="<?= $posterUrl ?>" alt="<?= h($movie['title']) ?>" class="w-100 h-100 object-fit-cover">
                            <span class="badge bg-danger position-absolute top-0 start-0 m-3 px-2 py-1 fs-7"><?= h($ageBadge) ?></span>
                            <span class="badge bg-dark bg-opacity-75 text-warning border border-warning position-absolute top-0 end-0 m-3 px-2 py-1">
                                <i class="bi bi-star-fill me-1"></i>9.5
                            </span>
                        </div>
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <h5 class="fw-bold text-white text-truncate mb-2" title="<?= h($movie['title']) ?>"><?= h($movie['title']) ?></h5>
                            <div class="small text-muted mb-3">
                                <div><i class="bi bi-clock me-1 text-danger"></i> <?= h($movie['duration']) ?> phút</div>
                                <div><i class="bi bi-tag me-1 text-info"></i> <?= h($movie['genres']) ?></div>
                                <div><i class="bi bi-calendar-event me-1 text-secondary"></i> Khởi chiếu: <?= $releaseDate ?></div>
                            </div>
                            <div class="mt-auto d-flex gap-2">
                                <a href="?action=movie_show&id=<?= $movie['id'] ?>" class="btn btn-peta-outline btn-sm flex-fill">
                                    <i class="bi bi-info-circle me-1"></i> Chi tiết
                                </a>
                                <a href="?action=movie_show&id=<?= $movie['id'] ?>" class="btn btn-peta-primary btn-sm flex-fill">
                                    <i class="bi bi-ticket-perforated me-1"></i> Đặt vé
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-secondary text-center py-5">
            <i class="bi bi-camera-reels fs-1 d-block mb-2"></i>
            Không tìm thấy phim đang chiếu nào phù hợp.
        </div>
    <?php endif; ?>

</div>
