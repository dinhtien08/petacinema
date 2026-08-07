<?php
    $clientPage = $_GET['client_page'] ?? '';
    if ($clientPage === 'showtimes') {
        require PATH_VIEW . 'showtimes.php';
        return;
    }
    if ($clientPage === 'foods') {
        require PATH_VIEW . 'foods.php';
        return;
    }

    $movieCategory = $_GET['movie_category'] ?? '';
    $isMovieCategoryPage = in_array($movieCategory, ['now_showing', 'coming_soon', 'special'], true);
    $specialShowtimes = [];

    if ($movieCategory === 'special') {
        $showtimeModel = new ShowtimeModel();

        foreach ($showtimeModel->getAllShowtimes() as $showtime) {
            if (strtotime($showtime['end_time']) < time()) {
                continue;
            }

            $showtimeDetail = $showtimeModel->getDetail((int) $showtime['id']);
            $roomType = $showtimeDetail['room_type'] ?? '';

            if (in_array(strtoupper($roomType), ['2D', '3D'], true)) {
                continue;
            }

            if ($showtimeDetail) {
                $specialShowtimes[] = $showtimeDetail;
            }
        }
    }

    $spiderMovies = array_values(array_filter($nowShowing ?? [], fn($movie) => stripos($movie['title'] ?? '', 'spider') !== false || stripos($movie['title'] ?? '', 'nhện') !== false));
    $bannerPrimaryMovie = $spiderMovies[0] ?? ($nowShowing[0] ?? null);
    $bannerFeatureMovie = $nowShowing[1] ?? $bannerPrimaryMovie;
    $bannerUpcomingMovies = array_slice($comingSoon ?? [], 0, 8);
    $bannerFoodModel = new FoodModel();
    $bannerOffers = array_values(array_filter($bannerFoodModel->getAll(), fn($food) => ($food['status'] ?? '') === 'active'));
    $bannerOffer = $bannerOffers[0] ?? null;
?>

<style>
    #heroBannerCarousel { display: none; }
    .home-cinema-banner { width: 100vw; margin: -1.5rem 0 3rem calc(50% - 50vw); overflow: hidden; background: #090b12; }
    .home-cinema-banner .carousel-item { height: clamp(540px, 47vw, 650px); }
    .home-cinema-banner-slide { height: 100%; position: relative; }
    .home-cinema-film-slide { background: radial-gradient(circle at 80% 38%, #243d61 0, #101827 35%, #080a10 76%); }
    .home-cinema-banner-content { max-width: 650px; padding-top: 8rem; position: relative; z-index: 1; }
    .home-cinema-film-layout { align-items: center; display: grid; grid-template-columns: minmax(0, 45%) minmax(0, 55%); height: 100%; }
    .home-cinema-film-copy { color: #fff; padding: 3rem 2rem 4.5rem 0; position: relative; z-index: 2; }
    .home-cinema-studio { align-items: center; border-left: 4px solid #e11d2e; color: #fff; display: inline-flex; font-size: .72rem; font-weight: 800; letter-spacing: .16em; margin-bottom: 1.35rem; padding: .25rem 0 .25rem .7rem; text-transform: uppercase; }
    .home-cinema-studio b { background: #e11d2e; font-size: .88rem; letter-spacing: .04em; margin-right: .35rem; padding: .25rem .42rem; }
    .home-cinema-film-kicker { color: #fbbf24; font-size: .8rem; font-weight: 800; letter-spacing: .26em; margin-bottom: .7rem; text-transform: uppercase; }
    .home-cinema-banner-title { color: #fff; font-size: clamp(2.4rem, 4.7vw, 5rem); font-weight: 900; letter-spacing: -.055em; line-height: .9; margin-bottom: 1rem; text-shadow: 0 8px 24px rgba(0,0,0,.45); }
    .home-cinema-banner-description { color: #d7dde7; font-size: .95rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
    .home-cinema-film-visual { align-self: stretch; overflow: hidden; position: relative; }
    .home-cinema-film-visual::before { background: linear-gradient(90deg, #090b12 0%, rgba(9,11,18,.58) 14%, transparent 42%); content: ''; inset: 0; position: absolute; z-index: 1; }
    .home-cinema-film-visual img { height: 100%; object-fit: cover; object-position: center; transform: scale(1.03); transition: transform .8s ease; width: 100%; }
    .carousel-item.active .home-cinema-film-visual img { transform: scale(1); }
    .home-cinema-upcoming-slide { background: radial-gradient(circle at 82% -10%, #412423, transparent 38%), linear-gradient(125deg, #080a10, #19131a); }
    .home-cinema-upcoming-content { padding: 3.25rem 0 4.5rem; }
    .home-cinema-upcoming-heading { align-items: baseline; display: flex; justify-content: space-between; margin-bottom: 1.8rem; }
    .home-cinema-brand { color: #fff; font-size: clamp(1.8rem, 3vw, 3.15rem); font-weight: 900; letter-spacing: -.05em; }
    .home-cinema-hot-title { color: #f3f4f6; font-size: clamp(1.35rem, 2.5vw, 2.4rem); font-weight: 800; margin: 0; }
    .home-cinema-hot-title span { color: #ef4444; }
    .home-cinema-upcoming { display: grid; grid-template-columns: repeat(8, minmax(0, 1fr)); gap: clamp(.55rem, 1vw, 1rem); }
    .home-cinema-upcoming-item { color: #fff; min-width: 0; text-decoration: none; }
    .home-cinema-upcoming-poster { aspect-ratio: 2 / 3; background: #252936; border-radius: .65rem; box-shadow: 0 10px 24px rgba(0,0,0,.28); overflow: hidden; }
    .home-cinema-upcoming-item img { display: block; height: 100%; object-fit: cover; transition: transform .3s ease, filter .3s ease; width: 100%; }
    .home-cinema-upcoming-item:hover img { filter: saturate(1.13); transform: scale(1.07); }
    .home-cinema-upcoming-item:hover .home-cinema-upcoming-poster { box-shadow: 0 16px 28px rgba(0,0,0,.6); }
    .home-cinema-upcoming-item span { display: block; font-size: clamp(.68rem, .8vw, .82rem); font-weight: 800; line-height: 1.3; margin-top: .6rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .home-cinema-upcoming-item small { color: #b8c0ce; display: block; font-size: .68rem; margin-top: .18rem; }
    @media (max-width: 991.98px) { .home-cinema-film-layout { grid-template-columns: 48% 52%; } .home-cinema-film-copy { padding-left: 1.5rem; } .home-cinema-upcoming { grid-template-columns: repeat(4, minmax(110px, 1fr)); max-height: 380px; overflow: auto; padding-right: .35rem; } }
    @media (max-width: 767.98px) { .home-cinema-banner { margin-top: -1rem; } .home-cinema-banner .carousel-item { height: 590px; } .home-cinema-banner-content { padding: 7rem 1.5rem 2rem; } .home-cinema-film-layout { display: block; position: relative; } .home-cinema-film-copy { bottom: 0; padding: 2rem 1.5rem 4.25rem; position: absolute; } .home-cinema-film-visual { height: 100%; } .home-cinema-film-visual::after { background: linear-gradient(0deg, #090b12 0%, rgba(9,11,18,.12) 72%); content: ''; inset: 0; position: absolute; } .home-cinema-film-visual::before { background: rgba(9,11,18,.18); } .home-cinema-upcoming-content { padding: 2.2rem 1.5rem 4rem; } .home-cinema-upcoming-heading { align-items: flex-start; flex-direction: column; gap: .35rem; } .home-cinema-upcoming { grid-template-columns: repeat(4, minmax(115px, 1fr)); } }
</style>

<?php if (!$isMovieCategoryPage && $bannerPrimaryMovie): ?>
    <?php
        $primaryImage = !empty($bannerPrimaryMovie['poster']) ? (str_starts_with($bannerPrimaryMovie['poster'], 'http') ? $bannerPrimaryMovie['poster'] : BASE_ASSETS_UPLOADS . $bannerPrimaryMovie['poster']) : '';
        $featureImage = !empty($bannerFeatureMovie['poster']) ? (str_starts_with($bannerFeatureMovie['poster'], 'http') ? $bannerFeatureMovie['poster'] : BASE_ASSETS_UPLOADS . $bannerFeatureMovie['poster']) : '';
        $offerImage = !empty($bannerOffer['image']) ? (str_starts_with($bannerOffer['image'], 'http') ? $bannerOffer['image'] : BASE_ASSETS_UPLOADS . $bannerOffer['image']) : '';
    ?>
    <div id="homeCinemaCarousel" class="carousel slide carousel-fade home-cinema-banner" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators"><button type="button" data-bs-target="#homeCinemaCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Phim đang chiếu"></button><button type="button" data-bs-target="#homeCinemaCarousel" data-bs-slide-to="1" aria-label="Ưu đãi"></button><button type="button" data-bs-target="#homeCinemaCarousel" data-bs-slide-to="2" aria-label="Phim nổi bật"></button><button type="button" data-bs-target="#homeCinemaCarousel" data-bs-slide-to="3" aria-label="Phim sắp chiếu"></button></div>
        <div class="carousel-inner">
            <div class="carousel-item active"><div class="home-cinema-banner-slide home-cinema-film-slide"><div class="container home-cinema-film-layout"><div class="home-cinema-film-copy"><div class="home-cinema-studio"><b>MARVEL</b> STUDIOS</div><div class="home-cinema-film-kicker">Spider-Man</div><h1 class="home-cinema-banner-title text-uppercase"><?= h($bannerPrimaryMovie['title']) ?></h1><p class="home-cinema-banner-description mb-4">Đang khởi chiếu tại rạp</p><a href="<?= BASE_URL ?>?action=booking_date&amp;movie_id=<?= (int) $bannerPrimaryMovie['id'] ?>" class="btn hero-btn-booking btn-lg px-4 py-3 text-uppercase">Đặt vé ngay</a></div><div class="home-cinema-film-visual"><img src="<?= h($primaryImage) ?>" alt="<?= h($bannerPrimaryMovie['title']) ?>"></div></div></div></div>
            <div class="carousel-item"><div class="home-cinema-banner-slide" style="background-image: linear-gradient(90deg, rgba(5,10,20,.92), rgba(5,10,20,.38)), url('<?= h($offerImage) ?>');"><div class="container home-cinema-banner-content"><span class="badge bg-danger text-uppercase px-3 py-2 mb-3">Ưu đãi đặt vé</span><h2 class="home-cinema-banner-title text-uppercase mb-3"><?= h($bannerOffer['name'] ?? 'Ưu đãi tại Petacinema') ?></h2><p class="home-cinema-banner-description mb-4"><?= h($bannerOffer['description'] ?? 'Khám phá combo bắp nước và ưu đãi mới nhất tại Petacinema.') ?></p><a href="<?= BASE_URL ?>?client_page=foods" class="btn hero-btn-booking btn-lg px-4 py-3 text-uppercase">Xem ưu đãi</a></div></div></div>
            <div class="carousel-item"><div class="home-cinema-banner-slide home-cinema-film-slide"><div class="container home-cinema-film-layout"><div class="home-cinema-film-copy"><div class="home-cinema-film-kicker">Phim nổi bật</div><h2 class="home-cinema-banner-title text-uppercase"><?= h($bannerFeatureMovie['title']) ?></h2><p class="home-cinema-banner-description mb-4">Đang khởi chiếu tại rạp</p><a href="<?= BASE_URL ?>?action=booking_date&amp;movie_id=<?= (int) $bannerFeatureMovie['id'] ?>" class="btn hero-btn-booking btn-lg px-4 py-3 text-uppercase">Đặt vé ngay</a></div><div class="home-cinema-film-visual"><img src="<?= h($featureImage) ?>" alt="<?= h($bannerFeatureMovie['title']) ?>"></div></div></div></div>
            <div class="carousel-item"><div class="home-cinema-banner-slide home-cinema-upcoming-slide"><div class="container home-cinema-upcoming-content"><div class="home-cinema-upcoming-heading"><div class="home-cinema-brand">Peta Cinema</div><h2 class="home-cinema-hot-title">Phim Hot <span>Tháng 8</span></h2></div><div class="home-cinema-upcoming"><?php foreach ($bannerUpcomingMovies as $upcomingMovie): ?><?php $upcomingPoster = !empty($upcomingMovie['poster']) ? (str_starts_with($upcomingMovie['poster'], 'http') ? $upcomingMovie['poster'] : BASE_ASSETS_UPLOADS . $upcomingMovie['poster']) : ''; ?><a class="home-cinema-upcoming-item" href="<?= BASE_URL ?>?action=movie_detail&amp;id=<?= (int) $upcomingMovie['id'] ?>"><div class="home-cinema-upcoming-poster"><img src="<?= h($upcomingPoster) ?>" alt="<?= h($upcomingMovie['title']) ?>"></div><span><?= h($upcomingMovie['title']) ?></span><small><?= !empty($upcomingMovie['release_date']) ? date('d/m/Y', strtotime($upcomingMovie['release_date'])) : 'Đang cập nhật' ?></small></a><?php endforeach; ?></div></div></div></div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#homeCinemaCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Trước</span></button>
        <button class="carousel-control-next" type="button" data-bs-target="#homeCinemaCarousel" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Sau</span></button>
    </div>
<?php endif; ?>

<!-- Hero Banner Slideshow (Bootstrap Carousel) -->
<div id="heroBannerCarousel" class="carousel slide carousel-fade mb-5 rounded-4 overflow-hidden border border-danger border-2 shadow-lg" data-bs-ride="carousel" data-bs-interval="3500">
    
    <!-- Indicators -->
    <div class="carousel-indicators mb-3">
        <button type="button" data-bs-target="#heroBannerCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Spider-Man"></button>
        <button type="button" data-bs-target="#heroBannerCarousel" data-bs-slide-to="1" aria-label="Detective Conan"></button>
        <button type="button" data-bs-target="#heroBannerCarousel" data-bs-slide-to="2" aria-label="The Odyssey"></button>
        <button type="button" data-bs-target="#heroBannerCarousel" data-bs-slide-to="3" aria-label="Colony: Bầy Xác Sống"></button>
        <button type="button" data-bs-target="#heroBannerCarousel" data-bs-slide-to="4" aria-label="Minions & Monsters"></button>
    </div>

    <!-- Carousel Inner -->
    <div class="carousel-inner">
        
        <!-- Slide 1: Spider-Man -->
        <div class="carousel-item active">
            <div class="hero-carousel-item p-4 p-md-5 d-flex align-items-center" style="background: linear-gradient(90deg, rgba(8,8,10,0.92) 0%, rgba(12,12,16,0.72) 45%, rgba(10,10,12,0.25) 80%, rgba(8,8,10,0.1) 100%), url('<?= BASE_ASSETS_UPLOADS ?>movie/1785768768-nguoinhen.webp') center/cover no-repeat;">
                <div class="container py-2">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-7 col-md-7">
                            <div class="mb-3">
                                <div class="hero-logo-badge d-inline-flex align-items-center px-3 py-2 rounded-3">
                                    <img src="<?= BASE_ASSETS_UPLOADS ?>logo/logo.png" alt="Petacinema Logo" class="hero-logo-img">
                                </div>
                            </div>
                            <span class="badge bg-danger text-uppercase px-3 py-2 fs-6 mb-2 shadow"><i class="bi bi-fire me-1"></i> BOM TẤN HOT</span>
                            <h1 class="hero-movie-title display-4 text-uppercase mb-3">Spider-Man</h1>
                            <p class="hero-slogan lead mb-4 fs-5">
                                Người nhện trở lại với sứ mệnh giải cứu đa vũ trụ đỉnh cao!
                            </p>
                            <div>
                                <a href="<?= BASE_URL ?>?action=booking_date&movie_id=1" class="btn hero-btn-booking btn-lg px-4 py-3 text-uppercase shadow">
                                    <i class="bi bi-ticket-perforated-fill me-2"></i> ĐẶT VÉ NGAY
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-5 text-center d-none d-md-block">
                            <div class="hero-poster-wrapper mx-auto" style="max-width: 250px; aspect-ratio: 2/3;">
                                <img src="<?= BASE_ASSETS_UPLOADS ?>movie/1785768768-nguoinhen.webp" alt="Spider-Man" class="hero-poster-img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 2: Detective Conan -->
        <div class="carousel-item">
            <div class="hero-carousel-item p-4 p-md-5 d-flex align-items-center" style="background: linear-gradient(90deg, rgba(8,8,10,0.92) 0%, rgba(12,12,16,0.72) 45%, rgba(10,10,12,0.25) 80%, rgba(8,8,10,0.1) 100%), url('<?= BASE_ASSETS_UPLOADS ?>movie/1785804501-conan.webp') center/cover no-repeat;">
                <div class="container py-2">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-7 col-md-7">
                            <div class="mb-3">
                                <div class="hero-logo-badge d-inline-flex align-items-center px-3 py-2 rounded-3">
                                    <img src="<?= BASE_ASSETS_UPLOADS ?>logo/logo.png" alt="Petacinema Logo" class="hero-logo-img">
                                </div>
                            </div>
                            <span class="badge bg-danger text-uppercase px-3 py-2 fs-6 mb-2 shadow"><i class="bi bi-star-fill me-1"></i> PHIM HOẠT HÌNH HOT</span>
                            <h1 class="hero-movie-title display-4 text-uppercase mb-3">Detective Conan</h1>
                            <p class="hero-slogan lead mb-4 fs-5">
                                Thám tử lừng danh vạch trần bí ẩn đen tối nhất lịch sử!
                            </p>
                            <div>
                                <a href="<?= BASE_URL ?>?action=booking_date&movie_id=2" class="btn hero-btn-booking btn-lg px-4 py-3 text-uppercase shadow">
                                    <i class="bi bi-ticket-perforated-fill me-2"></i> ĐẶT VÉ NGAY
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-5 text-center d-none d-md-block">
                            <div class="hero-poster-wrapper mx-auto" style="max-width: 250px; aspect-ratio: 2/3;">
                                <img src="<?= BASE_ASSETS_UPLOADS ?>movie/1785804501-conan.webp" alt="Detective Conan" class="hero-poster-img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 3: The Odyssey -->
        <div class="carousel-item">
            <div class="hero-carousel-item p-4 p-md-5 d-flex align-items-center" style="background: linear-gradient(90deg, rgba(8,8,10,0.92) 0%, rgba(12,12,16,0.72) 45%, rgba(10,10,12,0.25) 80%, rgba(8,8,10,0.1) 100%), url('<?= BASE_ASSETS_UPLOADS ?>movie/1785805673-TheOdyssey.webp') center/cover no-repeat;">
                <div class="container py-2">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-7 col-md-7">
                            <div class="mb-3">
                                <div class="hero-logo-badge d-inline-flex align-items-center px-3 py-2 rounded-3">
                                    <img src="<?= BASE_ASSETS_UPLOADS ?>logo/logo.png" alt="Petacinema Logo" class="hero-logo-img">
                                </div>
                            </div>
                            <span class="badge bg-danger text-uppercase px-3 py-2 fs-6 mb-2 shadow"><i class="bi bi-compass-fill me-1"></i> SỬ THI PHIÊU LƯU</span>
                            <h1 class="hero-movie-title display-4 text-uppercase mb-3">The Odyssey</h1>
                            <p class="hero-slogan lead mb-4 fs-5">
                                Hành trình sử thi tráng lệ chinh phục vinh quang và vùng đất mới!
                            </p>
                            <div>
                                <a href="<?= BASE_URL ?>?action=booking_date&movie_id=3" class="btn hero-btn-booking btn-lg px-4 py-3 text-uppercase shadow">
                                    <i class="bi bi-ticket-perforated-fill me-2"></i> ĐẶT VÉ NGAY
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-5 text-center d-none d-md-block">
                            <div class="hero-poster-wrapper mx-auto" style="max-width: 250px; aspect-ratio: 2/3;">
                                <img src="<?= BASE_ASSETS_UPLOADS ?>movie/1785805673-TheOdyssey.webp" alt="The Odyssey" class="hero-poster-img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 4: Colony: Bầy Xác Sống -->
        <div class="carousel-item">
            <div class="hero-carousel-item p-4 p-md-5 d-flex align-items-center" style="background: linear-gradient(90deg, rgba(8,8,10,0.92) 0%, rgba(12,12,16,0.72) 45%, rgba(10,10,12,0.25) 80%, rgba(8,8,10,0.1) 100%), url('<?= BASE_ASSETS_UPLOADS ?>movie/1785806693-zombies.webp') center/cover no-repeat;">
                <div class="container py-2">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-7 col-md-7">
                            <div class="mb-3">
                                <div class="hero-logo-badge d-inline-flex align-items-center px-3 py-2 rounded-3">
                                    <img src="<?= BASE_ASSETS_UPLOADS ?>logo/logo.png" alt="Petacinema Logo" class="hero-logo-img">
                                </div>
                            </div>
                            <span class="badge bg-danger text-uppercase px-3 py-2 fs-6 mb-2 shadow"><i class="bi bi-shield-exclamation me-1"></i> KINH DỊ KINH HOÀNG</span>
                            <h1 class="hero-movie-title display-4 text-uppercase mb-3">Colony: Bầy Xác Sống</h1>
                            <p class="hero-slogan lead mb-4 fs-5">
                                Cuộc chiến sinh tồn nghẹt thở chống lại thảm họa xác sống!
                            </p>
                            <div>
                                <a href="<?= BASE_URL ?>?action=booking_date&movie_id=4" class="btn hero-btn-booking btn-lg px-4 py-3 text-uppercase shadow">
                                    <i class="bi bi-ticket-perforated-fill me-2"></i> ĐẶT VÉ NGAY
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-5 text-center d-none d-md-block">
                            <div class="hero-poster-wrapper mx-auto" style="max-width: 250px; aspect-ratio: 2/3;">
                                <img src="<?= BASE_ASSETS_UPLOADS ?>movie/1785806693-zombies.webp" alt="Colony: Bầy Xác Sống" class="hero-poster-img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 5: Minions & Monsters -->
        <div class="carousel-item">
            <div class="hero-carousel-item p-4 p-md-5 d-flex align-items-center" style="background: linear-gradient(90deg, rgba(8,8,10,0.92) 0%, rgba(12,12,16,0.72) 45%, rgba(10,10,12,0.25) 80%, rgba(8,8,10,0.1) 100%), url('<?= BASE_ASSETS_UPLOADS ?>movie/1785768963-minions.webp') center/cover no-repeat;">
                <div class="container py-2">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-7 col-md-7">
                            <div class="mb-3">
                                <div class="hero-logo-badge d-inline-flex align-items-center px-3 py-2 rounded-3">
                                    <img src="<?= BASE_ASSETS_UPLOADS ?>logo/logo.png" alt="Petacinema Logo" class="hero-logo-img">
                                </div>
                            </div>
                            <span class="badge bg-danger text-uppercase px-3 py-2 fs-6 mb-2 shadow"><i class="bi bi-emoji-smile-fill me-1"></i> HÀI HƯỚC GIA ĐÌNH</span>
                            <h1 class="hero-movie-title display-4 text-uppercase mb-3">Minions & Monsters</h1>
                            <p class="hero-slogan lead mb-4 fs-5">
                                Cuộc phiêu lưu siêu quậy, hài hước và bùng nổ năng lượng!
                            </p>
                            <div>
                                <a href="<?= BASE_URL ?>?action=booking_date&movie_id=1" class="btn hero-btn-booking btn-lg px-4 py-3 text-uppercase shadow">
                                    <i class="bi bi-ticket-perforated-fill me-2"></i> ĐẶT VÉ NGAY
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-5 text-center d-none d-md-block">
                            <div class="hero-poster-wrapper mx-auto" style="max-width: 250px; aspect-ratio: 2/3;">
                                <img src="<?= BASE_ASSETS_UPLOADS ?>movie/1785768963-minions.webp" alt="Minions & Monsters" class="hero-poster-img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#heroBannerCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroBannerCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<?php if ($movieCategory !== 'coming_soon' && $movieCategory !== 'special'): ?>
<!-- Section: Phim Đang Chiếu -->
<div class="mb-5" id="phim-dang-chieu">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-2 border-danger-subtle pb-3">
        <h2 class="h3 text-dark fw-bold mb-0 text-uppercase" style="font-family: 'Poppins', sans-serif;">
            <i class="bi bi-play-circle-fill me-2 text-danger"></i> PHIM ĐANG CHIẾU
        </h2>
        <span class="badge bg-danger text-white fs-6 px-3 py-2 rounded-pill"><?= count($nowShowing ?? []) ?> bộ phim</span>
    </div>

    <?php if (empty($nowShowing)): ?>
        <div class="alert alert-light text-center py-4 rounded-3 border text-secondary shadow-sm" role="alert">
            <i class="bi bi-info-circle fs-4 d-block mb-2 text-danger"></i>
            Hiện chưa có phim đang chiếu. Vui lòng quay lại sau!
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($nowShowing as $movie): ?>
                <?php
                    $posterUrl = '';
                    if (!empty($movie['poster'])) {
                        $posterUrl = str_starts_with($movie['poster'], 'http') ? $movie['poster'] : BASE_ASSETS_UPLOADS . $movie['poster'];
                    } else {
                        $posterUrl = 'https://via.placeholder.com/300x450/e2e8f0/0f172a?text=' . urlencode($movie['title']);
                    }
                    $ratingClass = 'badge-rating-' . ($movie['age_rating'] ?? 'P');
                ?>
                <div class="col">
                    <div class="card card-cinema h-100">
                        <div class="movie-poster-wrapper">
                            <a href="<?= BASE_URL ?>?action=movie_detail&id=<?= $movie['id'] ?>">
                                <img src="<?= h($posterUrl) ?>" class="movie-poster-img" alt="<?= h($movie['title']) ?>">
                            </a>
                            <span class="age-rating-badge <?= $ratingClass ?>"><?= h($movie['age_rating'] ?? 'P') ?></span>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="card-title text-dark text-truncate mb-2" title="<?= h($movie['title']) ?>">
                                    <a href="<?= BASE_URL ?>?action=movie_detail&id=<?= $movie['id'] ?>" class="text-dark text-decoration-none">
                                        <?= h($movie['title']) ?>
                                    </a>
                                </h5>
                                <p class="card-text text-secondary small mb-2 text-truncate" title="<?= h($movie['genres']) ?>">
                                    <i class="bi bi-tags me-1 text-danger"></i> <?= h($movie['genres']) ?>
                                </p>
                                <p class="card-text text-secondary small mb-3">
                                    <i class="bi bi-clock me-1 text-danger"></i> <?= h($movie['duration']) ?> phút
                                </p>
                            </div>
                            <div>
                                <a href="<?= BASE_URL ?>?action=booking_date&movie_id=<?= $movie['id'] ?>" class="btn btn-peta w-100">
                                    <i class="bi bi-ticket-perforated-fill me-1"></i> ĐẶT VÉ NGAY
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php endif; ?>

<?php if ($movieCategory === 'special'): ?>
    <div class="mb-5">
        <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-2 border-danger-subtle pb-3">
            <h2 class="h3 text-dark mb-0 text-uppercase"><i class="bi bi-stars me-2 text-danger"></i>SUẤT CHIẾU ĐẶC BIỆT</h2>
            <span class="badge bg-danger text-white fs-6 px-3 py-2 rounded-pill"><?= count($specialShowtimes) ?> suất</span>
        </div>

        <?php if (empty($specialShowtimes)): ?>
            <div class="alert alert-light text-center py-4 rounded-3 border text-secondary shadow-sm" role="alert">
                <i class="bi bi-stars fs-4 d-block mb-2 text-danger"></i>
                Hiện chưa có suất chiếu đặc biệt.
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($specialShowtimes as $showtime): ?>
                    <?php
                        $posterUrl = !empty($showtime['poster'])
                            ? (str_starts_with($showtime['poster'], 'http') ? $showtime['poster'] : BASE_ASSETS_UPLOADS . $showtime['poster'])
                            : 'https://via.placeholder.com/300x450/e2e8f0/0f172a?text=' . urlencode($showtime['movie_title']);
                    ?>
                    <div class="col">
                        <div class="card card-cinema h-100">
                            <div class="movie-poster-wrapper">
                                <img src="<?= h($posterUrl) ?>" class="movie-poster-img" alt="<?= h($showtime['movie_title']) ?>">
                                <span class="age-rating-badge bg-danger text-white"><?= h($showtime['room_type']) ?></span>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <h5 class="card-title text-dark mb-2"><?= h($showtime['movie_title']) ?></h5>
                                    <p class="card-text text-secondary small mb-2"><i class="bi bi-stars me-1 text-danger"></i><?= h($showtime['room_type']) ?></p>
                                    <p class="card-text text-secondary small mb-2"><i class="bi bi-calendar3 me-1 text-danger"></i><?= date('d/m/Y', strtotime($showtime['start_time'])) ?></p>
                                    <p class="card-text text-secondary small mb-3"><i class="bi bi-clock me-1 text-danger"></i><?= date('H:i', strtotime($showtime['start_time'])) ?> · <?= h($showtime['room_name']) ?></p>
                                </div>
                                <a href="<?= BASE_URL ?>?action=booking_date&movie_id=<?= (int) $showtime['movie_id'] ?>&date=<?= h(date('Y-m-d', strtotime($showtime['start_time']))) ?>&showtime_id=<?= (int) $showtime['id'] ?>" class="btn btn-peta w-100"><i class="bi bi-ticket-perforated-fill me-1"></i>ĐẶT VÉ</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($movieCategory !== 'now_showing' && $movieCategory !== 'special'): ?>
<!-- Section: Phim Sắp Chiếu -->
<div class="mb-5" id="phim-sap-chieu">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-2 border-danger-subtle pb-3">
        <h2 class="h3 text-dark fw-bold mb-0 text-uppercase" style="font-family: 'Poppins', sans-serif;">
            <i class="bi bi-calendar-event-fill me-2 text-danger"></i> PHIM SẮP CHIẾU
        </h2>
        <span class="badge bg-dark text-white fs-6 px-3 py-2 rounded-pill"><?= count($comingSoon ?? []) ?> bộ phim</span>
    </div>

    <?php if (empty($comingSoon)): ?>
        <div class="alert alert-light text-center py-4 rounded-3 border text-secondary shadow-sm" role="alert">
            <i class="bi bi-info-circle fs-4 d-block mb-2 text-danger"></i>
            Chưa có phim sắp chiếu.
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($comingSoon as $movie): ?>
                <?php
                    $posterUrl = '';
                    if (!empty($movie['poster'])) {
                        $posterUrl = str_starts_with($movie['poster'], 'http') ? $movie['poster'] : BASE_ASSETS_UPLOADS . $movie['poster'];
                    } else {
                        $posterUrl = 'https://via.placeholder.com/300x450/e2e8f0/0f172a?text=' . urlencode($movie['title']);
                    }
                    $ratingClass = 'badge-rating-' . ($movie['age_rating'] ?? 'P');
                ?>
                <div class="col">
                    <div class="card card-cinema h-100">
                        <div class="movie-poster-wrapper">
                            <a href="<?= BASE_URL ?>?action=movie_detail&id=<?= $movie['id'] ?>">
                                <img src="<?= h($posterUrl) ?>" class="movie-poster-img" alt="<?= h($movie['title']) ?>">
                            </a>
                            <span class="age-rating-badge <?= $ratingClass ?>"><?= h($movie['age_rating'] ?? 'P') ?></span>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="card-title text-dark text-truncate mb-2" title="<?= h($movie['title']) ?>">
                                    <a href="<?= BASE_URL ?>?action=movie_detail&id=<?= $movie['id'] ?>" class="text-dark text-decoration-none">
                                        <?= h($movie['title']) ?>
                                    </a>
                                </h5>
                                <p class="card-text text-secondary small mb-2 text-truncate">
                                    <i class="bi bi-tags me-1 text-danger"></i> <?= h($movie['genres']) ?>
                                </p>
                                <p class="card-text text-secondary small mb-2">
                                    <i class="bi bi-clock me-1 text-danger"></i> <?= h($movie['duration']) ?> phút
                                </p>
                                <p class="card-text text-secondary small mb-3">
                                    <i class="bi bi-calendar3 me-1 text-danger"></i> Khởi chiếu: <?= h(date('d/m/Y', strtotime($movie['release_date'] ?? 'now'))) ?>
                                </p>
                                <?php if (!empty($movie['trailer'])): ?>
                                    <a href="<?= h($movie['trailer']) ?>" target="_blank" rel="noopener noreferrer" class="small text-danger text-decoration-none fw-semibold d-inline-block mb-3"><i class="bi bi-play-btn-fill me-1"></i>Trailer</a>
                                <?php endif; ?>
                            </div>
                            <div>
                                <a href="<?= BASE_URL ?>?action=movie_detail&id=<?= $movie['id'] ?>" class="btn btn-outline-peta w-100">
                                    <i class="bi bi-info-circle-fill me-1"></i> Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>
