<style>
    /* Hero Banner Carousel */
    .hero-carousel .carousel-item {
        height: 480px;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(0deg, #090d16 0%, rgba(9, 13, 22, 0.4) 60%, rgba(9, 13, 22, 0.7) 100%);
    }

    .hero-content {
        position: relative;
        z-index: 2;
        padding-top: 130px;
    }

    /* Section Styling */
    .section-title {
        font-weight: 800;
        font-size: 1.75rem;
        letter-spacing: -0.5px;
        position: relative;
        display: inline-block;
        margin-bottom: 25px;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 0;
        width: 45px;
        height: 3.5px;
        background: #ef4444;
        border-radius: 2px;
    }

    /* Movie Cards */
    .movie-card {
        background: #121824;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .movie-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
        border-color: rgba(239, 68, 68, 0.4);
    }

    .poster-wrapper {
        position: relative;
        aspect-ratio: 2/3;
        overflow: hidden;
        background: #1a2232;
    }

    .movie-poster {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .movie-card:hover .movie-poster {
        transform: scale(1.06);
    }

    .age-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 6px;
        backdrop-filter: blur(8px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    .badge-P { background: rgba(34, 197, 94, 0.9); color: #fff; }
    .badge-T13 { background: rgba(234, 179, 8, 0.9); color: #000; }
    .badge-T16 { background: rgba(249, 115, 22, 0.9); color: #fff; }
    .badge-T18 { background: rgba(239, 68, 68, 0.9); color: #fff; }

    .rating-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(15, 23, 42, 0.85);
        color: #f59e0b;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid rgba(245, 158, 11, 0.3);
        backdrop-filter: blur(8px);
    }

    .movie-info {
        padding: 18px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .movie-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 8px;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .movie-meta {
        font-size: 0.85rem;
        color: #94a3b8;
        margin-bottom: 15px;
    }

    /* Theater Cards */
    .room-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        padding: 20px;
        transition: all 0.2s ease;
    }

    .room-card:hover {
        background: rgba(255, 255, 255, 0.06);
        border-color: rgba(239, 68, 68, 0.3);
    }
</style>

<!-- Banner Slider (Nổi bật) -->
<?php if (!empty($featured)): ?>
<div id="heroCarousel" class="carousel slide hero-carousel mb-5" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php foreach ($featured as $index => $movie): ?>
            <?php 
                $posterUrl = !empty($movie['poster']) ? BASE_ASSETS_UPLOADS . $movie['poster'] : 'https://placehold.co/1200x500/1e293b/ffffff?text=' . urlencode($movie['title']);
            ?>
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>" style="background-image: url('<?= $posterUrl ?>');">
                <div class="hero-overlay"></div>
                <div class="container hero-content">
                    <div class="row">
                        <div class="col-lg-7">
                            <span class="badge bg-danger mb-2 px-3 py-2 text-uppercase tracking-wider">Phim Nổi Bật</span>
                            <h1 class="display-4 fw-extrabold text-white mb-2"><?= h($movie['title']) ?></h1>
                            <p class="text-light opacity-75 mb-3">
                                <i class="bi bi-clock me-1 text-warning"></i> <?= h($movie['duration']) ?> phút
                                <span class="mx-2">•</span>
                                <i class="bi bi-tag me-1 text-info"></i> <?= h($movie['genres']) ?>
                            </p>
                            <p class="text-light opacity-90 mb-4 d-none d-md-block text-truncate-2" style="max-width: 600px;">
                                <?= h($movie['description'] ?? 'Trải nghiệm điện ảnh sắc nét cùng hệ thống âm thanh vòm đỉnh cao tại PETACINEMA.') ?>
                            </p>
                            <div class="d-flex gap-3">
                                <a href="?action=movie_show&id=<?= $movie['id'] ?>" class="btn btn-peta-primary btn-lg">
                                    <i class="bi bi-ticket-perforated-fill me-2"></i> Đặt Vé Ngay
                                </a>
                                <?php if (!empty($movie['trailer'])): ?>
                                    <a href="<?= h($movie['trailer']) ?>" target="_blank" class="btn btn-peta-outline btn-lg">
                                        <i class="bi bi-play-circle me-2"></i> Xem Trailer
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>
<?php endif; ?>

<div class="container">

    <!-- 1. PHIM ĐANG CHIẾU -->
    <section id="phim-dang-chieu" class="mb-5 pt-3">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="section-title text-white">Phim Đang Chiếu</h2>
                <p class="text-muted mb-0 small">Các tác phẩm điện ảnh đỉnh cao đang khởi chiếu tại rạp</p>
            </div>
        </div>

        <?php if (!empty($nowShowing)): ?>
            <div class="row g-4">
                <?php foreach ($nowShowing as $movie): ?>
                    <?php 
                        $posterUrl = !empty($movie['poster']) ? BASE_ASSETS_UPLOADS . $movie['poster'] : 'https://placehold.co/400x600/1e293b/ffffff?text=' . urlencode($movie['title']);
                        $ageBadge = $movie['age_rating'] ?? 'P';
                    ?>
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="movie-card">
                            <div class="poster-wrapper">
                                <img src="<?= $posterUrl ?>" alt="<?= h($movie['title']) ?>" class="movie-poster">
                                <span class="age-badge badge-<?= h($ageBadge) ?>"><?= h($ageBadge) ?></span>
                                <span class="rating-badge"><i class="bi bi-star-fill me-1"></i> 9.5</span>
                            </div>
                            <div class="movie-info">
                                <h3 class="movie-title" title="<?= h($movie['title']) ?>"><?= h($movie['title']) ?></h3>
                                <div class="movie-meta">
                                    <i class="bi bi-clock me-1 text-danger"></i> <?= h($movie['duration']) ?> phút
                                    <span class="mx-1">•</span>
                                    <span class="text-truncate d-inline-block align-bottom" style="max-width: 120px;"><?= h($movie['genres']) ?></span>
                                </div>
                                <div class="mt-auto">
                                    <a href="?action=movie_show&id=<?= $movie['id'] ?>" class="btn btn-peta-primary w-100">
                                        <i class="bi bi-ticket-perforated me-1"></i> Đặt Vé
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-secondary text-center py-4">Hiện chưa có phim đang chiếu nào.</div>
        <?php endif; ?>
    </section>

    <!-- 2. PHIM SẮP CHIẾU -->
    <section id="phim-sap-chieu" class="mb-5 pt-3">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="section-title text-white">Phim Sắp Chiếu</h2>
                <p class="text-muted mb-0 small">Sắp ra mắt trong thời gian tới</p>
            </div>
        </div>

        <?php if (!empty($comingSoon)): ?>
            <div class="row g-4">
                <?php foreach ($comingSoon as $movie): ?>
                    <?php 
                        $posterUrl = !empty($movie['poster']) ? BASE_ASSETS_UPLOADS . $movie['poster'] : 'https://placehold.co/400x600/1e293b/ffffff?text=' . urlencode($movie['title']);
                        $ageBadge = $movie['age_rating'] ?? 'P';
                        $releaseDate = !empty($movie['release_date']) ? date('d/m/Y', strtotime($movie['release_date'])) : 'Sắp chiếu';
                    ?>
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="movie-card">
                            <div class="poster-wrapper">
                                <img src="<?= $posterUrl ?>" alt="<?= h($movie['title']) ?>" class="movie-poster">
                                <span class="age-badge badge-<?= h($ageBadge) ?>"><?= h($ageBadge) ?></span>
                                <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-warning text-center py-1 fw-semibold small">
                                    <i class="bi bi-calendar-check me-1"></i> Khởi chiếu: <?= $releaseDate ?>
                                </div>
                            </div>
                            <div class="movie-info">
                                <h3 class="movie-title" title="<?= h($movie['title']) ?>"><?= h($movie['title']) ?></h3>
                                <div class="movie-meta">
                                    <i class="bi bi-clock me-1"></i> <?= h($movie['duration']) ?> phút
                                    <span class="mx-1">•</span>
                                    <span><?= h($movie['genres']) ?></span>
                                </div>
                                <div class="mt-auto">
                                    <a href="?action=movie_show&id=<?= $movie['id'] ?>" class="btn btn-peta-outline w-100">
                                        <i class="bi bi-info-circle me-1"></i> Chi Tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-secondary text-center py-4">Hiện chưa có dữ liệu phim sắp chiếu.</div>
        <?php endif; ?>
    </section>

    <!-- 3. HỆ THỐNG PHÒNG RẠP (CỤM RẠP) -->
    <section id="cum-rap" class="mb-5 pt-3">
        <h2 class="section-title text-white">Hệ Thống Phòng Rạp</h2>
        <p class="text-muted mb-4 small">Trải nghiệm các phòng chiếu tiêu chuẩn quốc tế tại PETACINEMA</p>

        <?php if (!empty($rooms)): ?>
            <div class="row g-4">
                <?php foreach ($rooms as $room): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="room-card d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="fw-bold text-white mb-1"><?= h($room['name']) ?></h4>
                                <span class="badge bg-danger text-uppercase me-2"><?= h($room['room_type_name']) ?></span>
                                <span class="text-muted small"><i class="bi bi-grid-3x3-gap me-1"></i> <?= h($room['total_seats']) ?> ghế</span>
                            </div>
                            <i class="bi bi-display fs-1 text-danger opacity-75"></i>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- 4. KHUYẾN MÃI & UY TÍN -->
    <section class="mb-4">
        <h2 class="section-title text-white">Tin Tức & Ưu Đãi</h2>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card bg-dark text-white border-0 overflow-hidden rounded-4">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #1e293b, #0f172a);">
                        <span class="badge bg-warning text-dark mb-2">ƯU ĐÃI THÀNH VIÊN</span>
                        <h4 class="fw-bold">Thứ 3 Hàng Tuần - Đồng Giá Vé 45K</h4>
                        <p class="text-muted small mb-3">Tất cả các suất chiếu vào ngày Thứ 3 hàng tuần chỉ từ 45.000 VNĐ dành cho thành viên PETACINEMA.</p>
                        <a href="#" class="btn btn-sm btn-peta-outline">Xem chi tiết</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-dark text-white border-0 overflow-hidden rounded-4">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #831843, #500724);">
                        <span class="badge bg-info text-dark mb-2">COMBO BẮP NƯỚC</span>
                        <h4 class="fw-bold">Combo Đôi Tiết Kiệm Đến 30%</h4>
                        <p class="text-pink small mb-3 opacity-75">Thưởng thức bắp rang bơ nóng hổi và nước ngọt sảng khoái cùng bạn bè với mức giá siêu ưu đãi.</p>
                        <a href="#" class="btn btn-sm btn-peta-outline">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
