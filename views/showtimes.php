<?php
    $today = date('Y-m-d');
    $days = [];
    for ($offset = 0; $offset < 14; $offset++) {
        $date = date('Y-m-d', strtotime('+' . $offset . ' day'));
        $days[$date] = [];
    }

    $movieModel = new MovieModel();
    $showtimeModel = new ShowtimeModel();
    $seatModel = new SeatModel();
    $nowShowing = [];
    foreach ($movieModel->getNowShowingMovies() as $movie) {
        $nowShowing[(int) $movie['id']] = $movie;
    }

    foreach ($showtimeModel->getAllShowtimes() as $showtime) {
        $showDate = date('Y-m-d', strtotime($showtime['start_time']));
        if (!array_key_exists($showDate, $days) || strtotime($showtime['end_time']) < time()) {
            continue;
        }
        $detail = $showtimeModel->getDetail((int) $showtime['id']);
        $movieId = (int) ($detail['movie_id'] ?? 0);
        if (!$detail || !isset($nowShowing[$movieId])) {
            continue;
        }
        $availableSeats = count(array_filter(
            $seatModel->getSeatsForShowtime((int) $detail['id'], (int) $detail['room_id']),
            fn($seat) => ($seat['display_status'] ?? '') === 'available'
        ));
        if (!isset($days[$showDate][$movieId])) {
            $days[$showDate][$movieId] = ['movie' => $nowShowing[$movieId], 'showtimes' => []];
        }
        $detail['available_seats'] = $availableSeats;
        $days[$showDate][$movieId]['showtimes'][] = $detail;
    }
    $shortDays = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
?>

<style>
    .cinema-date-tabs { display: flex; gap: .7rem; overflow-x: auto; padding-bottom: .75rem; scrollbar-width: thin; }
    .cinema-date-tab { flex: 0 0 88px; border: 1px solid var(--peta-card-border); border-radius: .75rem; background: #fff; color: var(--peta-text-main); font-weight: 700; padding: .7rem .45rem; text-align: center; transition: all .2s ease; }
    .cinema-date-tab:hover, .cinema-date-tab.active { background: var(--peta-accent); border-color: var(--peta-accent); color: #fff; transform: translateY(-2px); }
    .cinema-date-tab small { display: block; font-weight: 600; opacity: .85; }
    .cinema-schedule-panel[hidden] { display: none !important; }
    .cinema-late-banner { background: #fff1f2; border-left: 4px solid var(--peta-accent); border-radius: .5rem; color: #9f1239; font-size: .9rem; font-weight: 700; padding: .8rem 1rem; }
    .cinema-movie-row { border: 1px solid var(--peta-card-border); border-radius: 1rem; background: #fff; padding: 1.25rem; }
    .cinema-movie-poster { width: 125px; height: 185px; border-radius: .75rem; object-fit: cover; background: #e2e8f0; }
    .cinema-showtime-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(105px, 1fr)); gap: .7rem; }
    .cinema-showtime { border: 1px solid #dbeafe; border-radius: .7rem; background: #fff; min-height: 80px; padding: .65rem .4rem; text-align: center; transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
    .cinema-showtime:not(:disabled):hover { border-color: var(--peta-accent); box-shadow: 0 8px 18px rgba(229, 9, 20, .14); transform: translateY(-2px); }
    .cinema-showtime-time { color: var(--peta-text-main); display: block; font-size: 1.2rem; font-weight: 800; }
    .cinema-availability { display: block; font-size: .75rem; font-weight: 700; margin-top: .25rem; }
    .cinema-availability.high { color: #16a34a; }.cinema-availability.low { color: #ea580c; }.cinema-availability.none { color: #dc2626; }
    .cinema-showtime:disabled { background: #fef2f2; border-color: #fecaca; cursor: not-allowed; opacity: .85; }
    .schedule-modal-dialog { max-width: 540px; width: 92vw; }
    .schedule-modal .modal-content { border: 0; border-radius: 1rem; overflow: hidden; box-shadow: 0 18px 52px rgba(15,23,42,.3); }
    .schedule-modal.fade .modal-dialog { transform: scale(.96); transition: transform .2s ease-out; }.schedule-modal.show .modal-dialog { transform: scale(1); }
    @media (max-width: 575.98px) { .cinema-movie-poster { width: 92px; height: 138px; } .cinema-date-tab { flex-basis: 74px; } .cinema-showtime-grid { grid-template-columns: repeat(3, 1fr); } }
</style>

<nav aria-label="breadcrumb" class="mb-4 small"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-danger text-decoration-none">Trang chủ</a></li><li class="breadcrumb-item active">Lịch chiếu theo rạp</li></ol></nav>
<section class="mb-5">
    <div class="mb-4"><span class="badge bg-danger text-uppercase px-3 py-2 mb-2">Petacinema schedule</span><h1 class="h2 text-dark text-uppercase mb-1">Lịch chiếu theo rạp</h1><p class="text-secondary mb-0">Chọn ngày để xem các phim đang có lịch chiếu.</p></div>
    <div class="cinema-date-tabs mb-3" role="tablist" aria-label="Chọn ngày chiếu">
        <?php $dateIndex = 0; foreach (array_keys($days) as $date): ?><button type="button" class="cinema-date-tab<?= $date === $today ? ' active' : '' ?>" data-schedule-date="<?= h($date) ?>" role="tab" aria-selected="<?= $date === $today ? 'true' : 'false' ?>"><strong><?= date('d/m', strtotime($date)) ?></strong><small><?= $shortDays[(int) date('w', strtotime($date))] ?></small></button><?php $dateIndex++; endforeach; ?>
    </div>
    <div class="cinema-late-banner mb-4"><i class="bi bi-moon-stars-fill me-2"></i>Suất chiếu muộn từ 22h00</div>

    <?php foreach ($days as $date => $movies): ?>
        <div class="cinema-schedule-panel" data-schedule-panel="<?= h($date) ?>"<?= $date === $today ? '' : ' hidden' ?>>
            <?php if (empty($movies)): ?><div class="alert alert-light border text-center py-5 text-secondary"><i class="bi bi-calendar-x fs-2 d-block text-danger mb-2"></i>Chưa có suất chiếu nào trong ngày này.</div>
            <?php else: ?><div class="d-grid gap-4">
                <?php foreach ($movies as $item): ?>
                    <?php $movie = $item['movie']; $poster = trim((string) ($movie['poster'] ?? '')); ?>
                    <article class="cinema-movie-row"><div class="d-flex gap-3 gap-md-4 align-items-start">
                        <?php if ($poster !== ''): ?><img class="cinema-movie-poster" src="<?= h(str_starts_with($poster, 'http') ? $poster : BASE_ASSETS_UPLOADS . $poster) ?>" alt="<?= h($movie['title']) ?>"><?php else: ?><div class="cinema-movie-poster d-flex align-items-center justify-content-center text-secondary"><i class="bi bi-film fs-2"></i></div><?php endif; ?>
                        <div class="flex-grow-1 min-w-0"><h2 class="h4 text-dark fw-bold mb-2"><?= h($movie['title']) ?></h2><p class="small text-secondary mb-1"><?= h($movie['genres']) ?> <span class="mx-1">•</span> <?= h($movie['duration']) ?> phút</p><p class="small text-secondary mb-3"><?= h($item['showtimes'][0]['room_type'] ?? '') ?> <?= h($movie['language'] ?? '') ?></p><div class="cinema-showtime-grid">
                            <?php foreach ($item['showtimes'] as $showtime): ?>
                                <?php $available = (int) $showtime['available_seats']; $status = $available === 0 ? 'none' : ($available <= 30 ? 'low' : 'high'); ?>
                                <button type="button" class="cinema-showtime"<?= $available === 0 ? ' disabled' : '' ?> data-showtime-choice data-booking-url="<?= BASE_URL ?>?action=booking_date&amp;movie_id=<?= (int) $movie['id'] ?>&amp;date=<?= h($date) ?>&amp;showtime_id=<?= (int) $showtime['id'] ?>" data-title="<?= h($movie['title']) ?>" data-date="<?= h(date('d/m/Y', strtotime($date))) ?>" data-time="<?= h(date('H:i', strtotime($showtime['start_time']))) ?>" data-room="<?= h($showtime['room_name']) ?>"><span class="cinema-showtime-time"><?= date('H:i', strtotime($showtime['start_time'])) ?></span><span class="cinema-availability <?= $status ?>"><?= $available === 0 ? 'Hết ghế' : 'Còn ' . $available . ' ghế' ?></span></button>
                            <?php endforeach; ?>
                        </div></div>
                    </div></article>
                <?php endforeach; ?>
            </div><?php endif; ?>
        </div>
    <?php endforeach; ?>
</section>

<div class="modal fade schedule-modal" id="scheduleConfirmModal" tabindex="-1" aria-labelledby="scheduleConfirmTitle" aria-hidden="true"><div class="modal-dialog modal-dialog-centered schedule-modal-dialog"><div class="modal-content"><div class="modal-header border-0 pb-0"><h2 class="modal-title h5 text-dark" id="scheduleConfirmTitle">Bạn đang đặt vé xem phim</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button></div><div class="modal-body text-center pt-2"><h3 class="h4 text-danger fw-bold text-uppercase" id="schedule-modal-title"></h3><p class="mb-1"><span class="text-secondary">Ngày chiếu:</span> <strong id="schedule-modal-date"></strong></p><p class="mb-1"><span class="text-secondary">Giờ chiếu:</span> <strong id="schedule-modal-time"></strong></p><p class="mb-1"><span class="text-secondary">Rạp:</span> <strong>PETACINEMA</strong></p><p class="mb-0"><span class="text-secondary"></span> <strong id="schedule-modal-room"></strong></p></div><div class="modal-footer border-0 justify-content-center"><a id="schedule-modal-confirm" href="#" class="btn btn-peta px-4">Đồng ý</a></div></div></div></div>

<script>
    window.addEventListener('load', () => {
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('scheduleConfirmModal'));
        document.querySelectorAll('[data-schedule-date]').forEach((tab) => tab.addEventListener('click', () => {
            const date = tab.dataset.scheduleDate;
            document.querySelectorAll('[data-schedule-date]').forEach((item) => { const active = item === tab; item.classList.toggle('active', active); item.setAttribute('aria-selected', active ? 'true' : 'false'); });
            document.querySelectorAll('[data-schedule-panel]').forEach((panel) => { panel.hidden = panel.dataset.schedulePanel !== date; });
        }));
        document.querySelectorAll('[data-showtime-choice]').forEach((button) => button.addEventListener('click', () => {
            document.getElementById('schedule-modal-title').textContent = button.dataset.title;
            document.getElementById('schedule-modal-date').textContent = button.dataset.date;
            document.getElementById('schedule-modal-time').textContent = button.dataset.time;
            document.getElementById('schedule-modal-confirm').href = button.dataset.bookingUrl;
            modal.show();
        }));
    }, { once: true });
</script>
