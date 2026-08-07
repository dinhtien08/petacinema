<?php
    $posterUrl = '';
    if (!empty($movie['poster'])) {
        $posterUrl = str_starts_with($movie['poster'], 'http') ? $movie['poster'] : BASE_ASSETS_UPLOADS . $movie['poster'];
    } else {
        $posterUrl = 'https://via.placeholder.com/300x450/e2e8f0/0f172a?text=' . urlencode($movie['title']);
    }

    function formatDayOfWeek($dateStr) {
        $days = [
            'Sunday' => 'CN',
            'Monday' => 'T2',
            'Tuesday' => 'T3',
            'Wednesday' => 'T4',
            'Thursday' => 'T5',
            'Friday' => 'T6',
            'Saturday' => 'T7'
        ];
        $dayEng = date('l', strtotime($dateStr));
        $dayVie = $days[$dayEng] ?? $dayEng;
        return $dayVie . ', ' . date('d/m/Y', strtotime($dateStr));
    }
    $ratingClass = 'badge-rating-' . ($movie['age_rating'] ?? 'P');
    $selectedShowtimeId = (int) ($_REQUEST['showtime_id'] ?? 0);
    $selectedShowtime = null;

    foreach ($showtimes as $showtime) {
        if ((int) $showtime['id'] === $selectedShowtimeId) {
            $selectedShowtime = $showtime;
            break;
        }
    }

    // Khi quay lại từ bước chọn đồ ăn, giữ lại các ghế khách đã chọn trước đó.
    $preselectedSeatNumbers = [];
    $preselectedSeatRaw = trim((string) ($_GET['selected_seats'] ?? ''));
    if ($selectedShowtime && $preselectedSeatRaw !== '') {
        $preselectedSeatNumbers = preg_split('/[\s,;]+/', strtoupper($preselectedSeatRaw));
        $preselectedSeatNumbers = array_values(array_unique(array_filter(array_map('trim', $preselectedSeatNumbers))));
    }

    $showtimeSeats = [];
    $seatRows = [];
    $seatPriceById = [];

    // Dùng chung phụ thu loại ghế để hiển thị "giá từ" và tính đúng giá vé.
    $seatTypeModel = new SeatTypeModel();
    $surchargeByType = [];
    foreach ($seatTypeModel->getAll() as $seatType) {
        $surchargeByType[(int) $seatType['id']] = (float) $seatType['surcharge'];
    }

    if ($selectedShowtime) {
        $seatModel = new SeatModel();
        $showtimeSeats = $seatModel->getSeatsForShowtime(
            $selectedShowtimeId,
            (int) $selectedShowtime['room_id']
        );

        foreach ($showtimeSeats as $seat) {
            $seatRows[$seat['row_char']][] = $seat;
        }

        ksort($seatRows);
        foreach ($seatRows as &$rowSeats) {
            usort($rowSeats, fn($first, $second) => (int) $first['col_num'] <=> (int) $second['col_num']);
        }
        unset($rowSeats);

        foreach ($showtimeSeats as $seat) {
            $seatPriceById[(int) $seat['id']] = (float) $selectedShowtime['base_price']
                + (float) ($selectedShowtime['room_price_modifier'] ?? 0)
                + ($surchargeByType[(int) $seat['seat_type_id']] ?? 0);
        }
    }

    $comboError = '';
    $selectedSeatsForCombo = [];
    $ticketTotalForCombo = 0;
    $showComboStep = false;

    if (
        $selectedShowtime
        && $_SERVER['REQUEST_METHOD'] === 'POST'
        && in_array($_POST['booking_step'] ?? '', ['combo', 'adjust_combo'], true)
    ) {
        $seatNumbers = preg_split('/[\s,;]+/', strtoupper(trim((string) ($_POST['seat_numbers'] ?? ''))));
        $seatNumbers = array_values(array_unique(array_filter(array_map('trim', $seatNumbers))));
        $seatByNumber = [];
        foreach ($showtimeSeats as $seat) {
            $seatByNumber[$seat['seat_number']] = $seat;
        }

        if (empty($seatNumbers)) {
            $comboError = 'Vui lòng chọn ít nhất một ghế trước khi chọn combo.';
        } else {
            foreach ($seatNumbers as $seatNumber) {
                $seat = $seatByNumber[$seatNumber] ?? null;
                if (!$seat || ($seat['display_status'] ?? '') !== 'available') {
                    $comboError = 'Một hoặc nhiều ghế đã chọn không còn khả dụng. Vui lòng chọn lại.';
                    break;
                }
                $selectedSeatsForCombo[] = $seat;
            }
        }

        if ($comboError === '') {
            $selectedSeatNumbers = array_flip(array_column($selectedSeatsForCombo, 'seat_number'));
            foreach ($selectedSeatsForCombo as $seat) {
                if (($seat['seat_type_name'] ?? '') !== 'Couple' || empty($seat['couple_group'])) {
                    continue;
                }
                foreach ($showtimeSeats as $pairedSeat) {
                    if (($pairedSeat['couple_group'] ?? '') === $seat['couple_group'] && !isset($selectedSeatNumbers[$pairedSeat['seat_number']])) {
                        $comboError = 'Ghế Couple phải được chọn đầy đủ cả cặp.';
                        break 2;
                    }
                }
            }
        }

        if ($comboError === '') {
            foreach ($selectedSeatsForCombo as $seat) {
                $ticketTotalForCombo += $seatPriceById[(int) $seat['id']] ?? 0;
            }
            $showComboStep = true;
        }
    }
?>

<?php if ($showComboStep): ?>
    <?php require PATH_VIEW . 'booking_combo.php'; return; ?>
<?php endif; ?>

<style>
    .client-screen {
        max-width: 720px;
        margin: 0 auto 1.5rem;
        padding: 0.75rem 1rem;
        border-top: 4px solid var(--peta-accent);
        border-radius: 50% 50% 0 0 / 18px 18px 0 0;
        background: linear-gradient(180deg, #f1f5f9 0%, #ffffff 100%);
        color: var(--peta-text-muted);
        font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.22rem;
        text-align: center;
    }

    .client-seat-map { min-width: max-content; padding: 0.5rem 1rem 1.5rem; }
    .client-seat-row { display: flex; align-items: center; justify-content: center; gap: 0.6rem; }
    .client-seat-row + .client-seat-row { margin-top: 0.6rem; }
    .client-seat-row-label { width: 1.5rem; color: var(--peta-text-muted); font-weight: 700; text-align: center; }
    .client-seat-row-content { display: flex; gap: 0.45rem; }
    .client-seat {
        width: 2.7rem;
        height: 2.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #94a3b8;
        border-radius: 0.5rem;
        background: #ffffff;
        color: #222222;
        font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 0.75rem;
        font-weight: 600;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
    }
    .client-seat:not(:disabled):hover { transform: translateY(-2px); border-color: var(--peta-accent); box-shadow: 0 4px 10px rgba(229, 9, 20, 0.2); }
    .client-seat.is-selected, .client-seat.client-seat-vip.is-selected, .client-seat.client-seat-couple.is-selected { background: var(--peta-accent); border-color: var(--peta-accent); color: #ffffff; }
    .client-seat-vip { flex-direction: column; background: #fff8e1; border-color: #f59e0b; color: #7c5200; line-height: 1; }
    .client-seat-vip small { margin-top: 0.15rem; font-size: 0.52rem; font-weight: 700; }
    .client-seat-couple { background: #fff1f2; border-color: #fb7185; color: #9f1239; }
    .client-seat-couple-pair { display: inline-flex; gap: 0.2rem; padding: 0.2rem; border: 1px dashed #fb7185; border-radius: 0.65rem; }
    .client-seat-booked { background: #64748b; border-color: #475569; color: #ffffff; cursor: not-allowed; opacity: 0.85; }
    .client-seat-maintenance { background: #334155; border-color: #1e293b; color: #ffffff; cursor: not-allowed; opacity: 0.75; text-decoration: line-through; }
    .seat-price-line { padding: .7rem 0; border-bottom: 1px dashed #e2e8f0; }
    .seat-price-line:last-child { border-bottom: 0; }
    .seat-price-line-name { color: var(--peta-text-main); font-weight: 700; }
    .seat-price-line-price { color: var(--peta-text-muted); font-size: .83rem; }
    .seat-price-total { background: #fff1f2; border-radius: .65rem; color: var(--peta-accent); }
    .showtime-format-list { display: grid; gap: 1.2rem; }
    .showtime-format-group { border: 1px solid #e2e8f0; border-radius: .9rem; background: #f8fafc; padding: 1rem; }
    .showtime-format-heading { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .65rem; margin-bottom: .8rem; }
    .showtime-format-badge { display: inline-flex; align-items: center; min-width: 86px; justify-content: center; border-radius: .55rem; background: #111827; color: #fff; font-size: .9rem; font-weight: 800; letter-spacing: .04em; padding: .45rem .75rem; }
    .showtime-format-note { color: var(--peta-text-muted); font-size: .82rem; }
    .showtime-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(135px, 1fr)); gap: .85rem; }
    .showtime-slot { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 112px; padding: .8rem; border: 1px solid #fecdd3; border-radius: .8rem; background: #fff; text-decoration: none; transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
    .showtime-slot:hover { transform: translateY(-3px); border-color: var(--peta-accent); box-shadow: 0 8px 18px rgba(229, 9, 20, .14); }
    .showtime-slot.is-selected { border: 2px solid var(--peta-accent); background: #fff1f2; }
    .showtime-slot.is-soldout { cursor: not-allowed; border-color: #fecaca; background: #fef2f2; opacity: .78; }
    .showtime-slot-time { color: var(--peta-text-main); font-size: 1.35rem; font-weight: 800; }
    .seat-availability { margin-top: .2rem; font-size: .78rem; font-weight: 700; }
    .seat-availability.is-high { color: #16a34a; }
    .seat-availability.is-low { color: #ea580c; }
    .seat-availability.is-none { color: #dc2626; }
    .peta-booking-dialog { border: 0; border-radius: 1rem; box-shadow: 0 18px 52px rgba(15,23,42,.3); max-width: 520px; padding: 0; width: min(92vw, 520px); }
    .peta-booking-dialog::backdrop { background: rgba(15, 23, 42, .58); }
    .peta-booking-dialog-content { background: #fff; border-radius: 1rem; overflow: hidden; }
    .seat-booking-overview { border: 1px solid var(--peta-card-border); border-radius: 1rem; background: #fff; box-shadow: 0 10px 28px rgba(15, 23, 42, .06); font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; padding: 1.15rem; }
    .seat-booking-overview dt { color: var(--peta-text-muted); font-size: .78rem; font-weight: 600; }
    .seat-booking-overview dd { color: var(--peta-text-main); font-size: .9rem; font-weight: 700; margin-bottom: 0; }
    .seat-category-summary { background: #f8fafc; border-radius: .7rem; }
    /* Trang chọn ghế: dùng flex để hai cột chỉ cao theo đúng nội dung. */
    .booking-seat-card {
        height: auto !important;
        min-height: 0 !important;
        align-self: flex-start;
    }
    .seat-layout {
        display: flex;
        align-items: flex-start;
        gap: 1.5rem;
        width: 100%;
        height: auto !important;
        min-height: 0 !important;
    }
    .seat-main {
        order: 1;
        flex: 1 1 auto;
        width: 0;
        min-width: 0;
        height: auto !important;
        min-height: 0 !important;
    }
    .seat-booking-overview {
        order: 2;
        flex: 0 0 300px;
        width: 300px;
        position: sticky;
        top: 92px;
        z-index: 1;
        align-self: flex-start;
        height: auto !important;
        min-height: 0 !important;
    }
    .seat-sidebar-order { border-top: 1px solid var(--peta-card-border); margin-top: 1rem; padding-top: 1rem; }
    @media (max-width: 991.98px) {
        .seat-layout { display: flex; flex-direction: column; }
        .seat-main { order: 1; width: 100%; }
        .seat-booking-overview { position: static; order: 2; width: 100%; flex-basis: auto; margin-top: 1rem; margin-bottom: 0; }
        .seat-booking-overview .row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 576px) {
        .client-seat-map { padding: 0.5rem 0 1rem; }
        .client-seat { width: 2.25rem; height: 2.2rem; font-size: 0.68rem; }
        .client-seat-row-content { gap: 0.3rem; }
        .client-seat-row { gap: 0.35rem; }
        .client-seat-couple-pair { gap: 0.1rem; padding: 0.1rem; }
    }
</style>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4 small">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-danger text-decoration-none fw-medium"><i class="bi bi-house-door"></i> Trang chủ</a></li>
        <li class="breadcrumb-item text-secondary">Đặt vé</li>
        <li class="breadcrumb-item text-dark fw-bold active" aria-current="page"><?= h($movie['title']) ?></li>
    </ol>
</nav>

<!-- Movie Header Summary Card -->
<div class="card card-cinema p-3 mb-4 shadow-sm">
    <div class="d-flex align-items-center gap-3">
        <img src="<?= h($posterUrl) ?>" style="width: 75px; height: 105px; object-fit: cover;" class="rounded-3 shadow-sm" alt="<?= h($movie['title']) ?>">
        <div>
            <h1 class="h3 text-dark fw-bold mb-2"><?= h($movie['title']) ?></h1>
            <?php $movieFormat = $selectedShowtime['room_type_name'] ?? ($showtimes[0]['room_type_name'] ?? ''); ?>
            <p class="text-secondary small mb-0"><?= h(implode(' • ', array_filter([
                $movie['genres'] ?? '',
                !empty($movie['duration']) ? $movie['duration'] . ' phút' : '',
                trim($movieFormat . ' ' . ($movie['language'] ?? '')),
            ]))) ?><?php if (!empty($movie['age_rating'])): ?> <span class="badge <?= $ratingClass ?> ms-1"><?= h($movie['age_rating']) ?></span><?php endif; ?></p>
        </div>
    </div>
</div>

<div class="card card-cinema p-4 mb-5 shadow-sm<?= $selectedShowtime ? ' booking-seat-card' : '' ?>">
    <?php if (!$selectedShowtime): ?>
    <form method="GET" action="<?= BASE_URL ?>" class="mb-4">
        <input type="hidden" name="action" value="booking_date">
        <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">

        <?php if (empty($availableDates)): ?>
            <div class="alert alert-light border text-center py-4 rounded-3 text-secondary shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-4 d-block mb-2 text-danger"></i> Hiện chưa có suất chiếu nào còn hiệu lực cho bộ phim này. Vui lòng quay lại sau!
            </div>
        <?php else: ?>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($availableDates as $d): ?>
                    <?php $isActive = ($d === $selectedDate); ?>
                    <button type="submit" name="date" value="<?= h($d) ?>" class="btn <?= $isActive ? 'btn-peta' : 'btn-outline-peta' ?> px-3 py-2 text-start">
                        <i class="bi bi-calendar-event me-1"></i>
                        <strong><?= formatDayOfWeek($d) ?></strong>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </form>

    <?php if (empty($selectedDate)): ?>
        <div class="alert alert-light border text-center py-4 text-secondary shadow-sm" role="alert">
            <i class="bi bi-hand-index-thumb fs-4 d-block mb-2 text-danger"></i>
            Vui lòng chọn một ngày chiếu ở trên để xem danh sách suất chiếu khả dụng.
        </div>
    <?php elseif (empty($showtimes)): ?>
        <div class="alert alert-light border text-center py-4 text-secondary shadow-sm" role="alert">
            <i class="bi bi-info-circle fs-4 d-block mb-2 text-danger"></i>
            Không tìm thấy suất chiếu hợp lệ cho ngày <strong><?= formatDayOfWeek($selectedDate) ?></strong>.
        </div>
    <?php else: ?>
        <?php
            $availabilitySeatModel = $seatModel ?? new SeatModel();
            $roomTypeOrder = ['2D' => 1, '3D' => 2, 'IMAX' => 3, 'GOLD CLASS' => 4];
            $showtimesByRoomType = [];

            foreach ($showtimes as $st) {
                $roomTypeName = trim((string) ($st['room_type_name'] ?? 'Khác'));
                $showtimesByRoomType[$roomTypeName][] = $st;
            }

            uksort($showtimesByRoomType, function ($first, $second) use ($roomTypeOrder) {
                $firstOrder = $roomTypeOrder[strtoupper($first)] ?? 99;
                $secondOrder = $roomTypeOrder[strtoupper($second)] ?? 99;
                return $firstOrder <=> $secondOrder ?: strcasecmp($first, $second);
            });
        ?>
        <div class="showtime-format-list">
            <?php foreach ($showtimesByRoomType as $roomTypeName => $roomTypeShowtimes): ?>
                <section class="showtime-format-group" aria-label="Suất chiếu <?= h($roomTypeName) ?>">
                    <div class="showtime-format-heading">
                        <div class="d-flex align-items-center gap-2">
                            <span class="showtime-format-badge"><?= h($roomTypeName) ?></span>
                            <span class="showtime-format-note">Chọn giờ chiếu phù hợp</span>
                        </div>
                    </div>
                    <div class="showtime-grid">
                        <?php foreach ($roomTypeShowtimes as $st): ?>
                            <?php
                                $startTime = date('H:i', strtotime($st['start_time']));
                                $seatsForSlot = $availabilitySeatModel->getSeatsForShowtime((int) $st['id'], (int) $st['room_id']);
                                $availableSeatRows = array_values(array_filter(
                                    $seatsForSlot,
                                    fn($seat) => ($seat['display_status'] ?? '') === 'available'
                                ));
                                $availableSeats = count($availableSeatRows);
                                $availabilityClass = $availableSeats === 0 ? 'is-none' : ($availableSeats < 30 ? 'is-low' : 'is-high');
                                $isFull = $availableSeats === 0;

                                $priceSeats = !empty($availableSeatRows) ? $availableSeatRows : $seatsForSlot;
                                $seatSurcharges = array_map(
                                    fn($seat) => (float) ($surchargeByType[(int) ($seat['seat_type_id'] ?? 0)] ?? 0),
                                    $priceSeats
                                );
                                $minimumSeatSurcharge = !empty($seatSurcharges) ? min($seatSurcharges) : 0;
                                $startingPrice = (float) $st['base_price']
                                    + (float) ($st['room_price_modifier'] ?? 0)
                                    + $minimumSeatSurcharge;
                                $formattedStartingPrice = number_format($startingPrice, 0, ',', '.') . ' đ';
                            ?>
                            <?php if ($isFull): ?>
                                <span class="showtime-slot is-soldout" aria-disabled="true">
                                    <span class="showtime-slot-time"><?= $startTime ?></span>
                                    <span class="seat-availability <?= $availabilityClass ?>">Hết ghế</span>
                                </span>
                            <?php else: ?>
                                <button
                                    type="button"
                                    class="showtime-slot<?= (int) $st['id'] === $selectedShowtimeId ? ' is-selected' : '' ?>"
                                    data-booking-showtime-choice
                                    data-url="<?= BASE_URL ?>?action=booking_date&amp;movie_id=<?= (int) $movie['id'] ?>&amp;date=<?= h($selectedDate) ?>&amp;showtime_id=<?= (int) $st['id'] ?>"
                                    data-time="<?= h($startTime) ?>"
                                    data-format="<?= h($roomTypeName) ?>"
                                    data-price="<?= h($formattedStartingPrice) ?>"
                                >
                                    <span class="showtime-slot-time"><?= $startTime ?></span>
                                    <span class="seat-availability <?= $availabilityClass ?>">Còn <?= $availableSeats ?> ghế</span>
                                </button>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>

        <dialog class="peta-booking-dialog" id="bookingShowtimeDialog" aria-labelledby="bookingShowtimeDialogTitle">
    <div class="peta-booking-dialog-content">
        <div class="modal-header border-0 pb-0">
            <h2 class="modal-title h5 text-dark" id="bookingShowtimeDialogTitle">Xác nhận suất chiếu</h2>
            <button type="button" class="btn-close" data-booking-dialog-close aria-label="Đóng"></button>
        </div>
        <div class="modal-body text-center">
            <h3 class="h4 text-danger fw-bold mb-3"><?= h($movie['title']) ?></h3>
            <p class="mb-1"><span class="text-secondary">Ngày chiếu:</span> <?= h(date('d/m/Y', strtotime($selectedDate))) ?></p>
            <p class="mb-1"><span class="text-secondary">Định dạng:</span> <strong id="booking-modal-format"></strong></p>
            <p class="mb-1"><span class="text-secondary">Giờ chiếu:</span> <strong id="booking-modal-time"></strong></p>
            <p class="mb-0"><span class="text-secondary">Giá vé:</span> <strong>Từ <span id="booking-modal-price"></span></strong></p>
        </div>
        <div class="modal-footer border-0 justify-content-center">
            <a id="booking-modal-confirm" class="btn btn-peta px-4" href="#">Đồng ý</a>
        </div>
    </div>
</dialog>
    <?php endif; ?>
    <?php endif; ?>

        <?php if ($selectedShowtime): ?>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom pb-3 mb-4">
                <div>
                    <span class="text-secondary small d-block mb-1">Suất chiếu đã chọn</span>
                    <strong class="text-dark"><?= h($selectedShowtime['room_type_name'] ?? '') ?> · <?= date('d/m/Y', strtotime($selectedDate)) ?> · <?= date('H:i', strtotime($selectedShowtime['start_time'])) ?></strong>
                </div>
            </div>

            <?php if (!empty($movie['age_rating']) && strtoupper($movie['age_rating']) !== 'P'): ?>
                <div class="alert alert-warning border-0 small mb-3"><i class="bi bi-exclamation-triangle-fill me-2"></i>Theo quy định của Cục Điện ảnh, phim này không dành cho khán giả dưới <?= h(preg_replace('/\D+/', '', $movie['age_rating'])) ?: h($movie['age_rating']) ?> tuổi.</div>
            <?php endif; ?>

            <?php if (($_GET['combo_timeout'] ?? '') === '1'): ?>
                <div class="alert alert-warning border-0 shadow-sm small mb-3">
                    <i class="bi bi-clock-history me-2"></i>
                    Đã hết 5 phút chọn đồ ăn. Các ghế bạn chọn trước đó đã được khôi phục; vui lòng kiểm tra lại rồi tiếp tục.
                </div>
            <?php endif; ?>

            <div class="seat-layout">
            <aside class="seat-booking-overview">
                <div class="row row-cols-1 g-3 flex-grow-1"><div class="col"><dl><dt>Phim</dt><dd><?= h($movie['title']) ?></dd></dl></div><div class="col"><dl><dt>Định dạng</dt><dd><?= h($selectedShowtime['room_type_name']) ?></dd></dl></div><div class="col"><dl><dt>Thể loại</dt><dd><?= h($movie['genres']) ?></dd></dl></div><div class="col"><dl><dt>Thời lượng</dt><dd><?= h($movie['duration']) ?> phút</dd></dl></div><div class="col"><dl><dt>Rạp chiếu</dt><dd>PETACINEMA</dd></dl></div><div class="col"><dl><dt>Ngày chiếu</dt><dd><?= date('d/m/Y', strtotime($selectedDate)) ?></dd></dl></div><div class="col"><dl><dt>Giờ chiếu</dt><dd><?= date('H:i', strtotime($selectedShowtime['start_time'])) ?></dd></dl></div><div class="col"><dl><dt>Ghế đã chọn</dt><dd id="seat-overview-selected">Chưa chọn</dd></dl></div></div>
                <div class="seat-sidebar-order"><h5 class="h6 text-dark mb-2">Thông tin đặt vé</h5><div id="selected-seat-price-details" class="small text-secondary">Chưa chọn ghế.</div><div id="seat-category-summary" class="seat-category-summary small p-3 mt-3 text-secondary">Chưa chọn ghế.</div><div class="seat-price-total d-flex justify-content-between align-items-center px-3 py-2 mt-3 fw-bold"><span>Tổng tiền ghế</span><span id="selected-seat-total">0 VNĐ</span></div></div>
            </aside>

            <div class="seat-main">
            <h4 class="h5 text-dark mb-3"><i class="bi bi-grid-3x3-gap-fill me-2 text-danger"></i>Chọn ghế</h4>

            <?php if ($comboError !== ''): ?>
                <div class="alert alert-danger border-0 shadow-sm"><?= h($comboError) ?></div>
            <?php endif; ?>

            <div class="card card-cinema p-3 p-md-4">
                <div class="d-flex flex-wrap justify-content-center gap-3 gap-md-4 small text-secondary mb-4">
                    <span class="d-flex align-items-center gap-2"><span class="client-seat" aria-hidden="true"></span> Ghế thường</span>
                    <span class="d-flex align-items-center gap-2"><span class="client-seat client-seat-vip" aria-hidden="true"></span> Ghế VIP</span>
                    <span class="d-flex align-items-center gap-2"><span class="client-seat-couple-pair" aria-hidden="true"><span class="client-seat client-seat-couple"></span><span class="client-seat client-seat-couple"></span></span> Ghế Couple</span>
                    <span class="d-flex align-items-center gap-2"><span class="client-seat client-seat-booked" aria-hidden="true"></span> Đã đặt</span>
                    <span class="d-flex align-items-center gap-2"><span class="client-seat is-selected" aria-hidden="true"></span> Đang chọn</span>
                </div>

                <?php if (empty($showtimeSeats)): ?>
                    <div class="alert alert-light border text-center text-secondary mb-0">
                        Chưa có sơ đồ ghế cho suất chiếu này.
                    </div>
                <?php else: ?>
                    <form method="POST" action="<?= BASE_URL ?>?action=booking_date" id="seat-selection-form">
                        <input type="hidden" name="movie_id" value="<?= (int) $movie['id'] ?>">
                        <input type="hidden" name="date" value="<?= h($selectedDate) ?>">
                        <input type="hidden" name="showtime_id" value="<?= (int) $selectedShowtimeId ?>">
                        <input type="hidden" name="booking_step" value="combo">
                        <input type="hidden" name="seat_numbers" id="selected-seat-numbers" value="">
                    <div class="client-screen">MÀN HÌNH</div>
                    <div class="overflow-auto">
                        <div class="client-seat-map mx-auto">
                            <?php foreach ($seatRows as $rowLabel => $rowSeats): ?>
                                <div class="client-seat-row">
                                    <span class="client-seat-row-label"><?= h($rowLabel) ?></span>
                                    <div class="client-seat-row-content">
                                        <?php $renderedCoupleGroups = []; ?>
                                        <?php foreach ($rowSeats as $seat): ?>
                                            <?php
                                                $seatType = $seat['seat_type_name'] ?? 'Standard';
                                                $isCouple = $seatType === 'Couple';
                                                $coupleGroup = trim((string) ($seat['couple_group'] ?? ''));

                                                if ($isCouple && $coupleGroup !== '' && isset($renderedCoupleGroups[$coupleGroup])) {
                                                    continue;
                                                }

                                                $seatsToRender = [$seat];
                                                if ($isCouple && $coupleGroup !== '') {
                                                    $renderedCoupleGroups[$coupleGroup] = true;
                                                    $seatsToRender = array_filter(
                                                        $rowSeats,
                                                        fn($rowSeat) => ($rowSeat['couple_group'] ?? '') === $coupleGroup
                                                    );
                                                }

                                                $coupleUnavailable = false;
                                                if ($isCouple) {
                                                    foreach ($seatsToRender as $coupleSeat) {
                                                        if (($coupleSeat['display_status'] ?? 'available') !== 'available') {
                                                            $coupleUnavailable = true;
                                                            break;
                                                        }
                                                    }
                                                }
                                            ?>
                                            <?php if ($isCouple): ?><span class="client-seat-couple-pair<?= $coupleUnavailable ? ' is-unavailable' : '' ?>" title="Ghế Couple">❤<?php endif; ?>
                                            <?php foreach ($seatsToRender as $seatToRender): ?>
                                                <?php
                                                    $status = ($isCouple && $coupleUnavailable)
                                                        ? 'booked'
                                                        : ($seatToRender['display_status'] ?? 'available');
                                                    $isVip = ($seatToRender['seat_type_name'] ?? '') === 'VIP';
                                                    $seatClasses = 'client-seat';
                                                    if ($isVip) {
                                                        $seatClasses .= ' client-seat-vip';
                                                    } elseif (($seatToRender['seat_type_name'] ?? '') === 'Couple') {
                                                        $seatClasses .= ' client-seat-couple';
                                                    }
                                                    if ($status === 'booked') {
                                                        $seatClasses .= ' client-seat-booked';
                                                    } elseif ($status === 'maintenance') {
                                                        $seatClasses .= ' client-seat-maintenance';
                                                    }
                                                ?>
                                                <button
                                                    type="button"
                                                    class="<?= $seatClasses ?>"
                                                    data-seat-number="<?= h($seatToRender['seat_number']) ?>"
                                                    data-seat-type="<?= h($seatToRender['seat_type_name'] ?? 'Standard') ?>"
                                                    data-seat-price="<?= h((string) ($seatPriceById[(int) $seatToRender['id']] ?? 0)) ?>"
                                                    <?= $isCouple ? 'data-couple-group="' . h($coupleGroup) . '"' : '' ?>
                                                    <?= $status === 'available' ? '' : 'disabled' ?>
                                                    title="Ghế <?= h($seatToRender['seat_number']) ?><?= $isVip ? ' (VIP)' : (($seatToRender['seat_type_name'] ?? '') === 'Couple' ? ' (Couple)' : '') ?>"
                                                ><span><?= h($seatToRender['seat_number']) ?></span><?php if ($isVip): ?><small>VIP</small><?php endif; ?></button>
                                            <?php endforeach; ?>
                                            <?php if ($isCouple): ?></span><?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                    <span class="client-seat-row-label"><?= h($rowLabel) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <p class="small text-secondary text-center mb-3">Bạn có thể chọn hoặc bỏ chọn các ghế còn trống trước khi tiếp tục.</p>
                    <div class="border-top pt-3 text-center small">
                        <span class="text-secondary">Ghế: </span><strong id="selected-seat-label" class="text-dark">Chưa chọn</strong>
                        <span class="text-secondary ms-3">Loại ghế: </span><strong id="selected-seat-type" class="text-danger">-</strong>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-peta px-4 py-2 fw-bold">
                            TIẾP TỤC <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                    </form>
                <?php endif; ?>
            </div>
            </div><!-- /.seat-main -->
            </div><!-- /.seat-layout -->
        <?php endif; ?>
</div>

<script>
    const bookingDialog = document.getElementById('bookingShowtimeDialog');
    const bookingConfirmLink = document.getElementById('booking-modal-confirm');

    document.querySelectorAll('[data-booking-showtime-choice]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();

            document.getElementById('booking-modal-format').textContent = button.dataset.format || '';
            document.getElementById('booking-modal-time').textContent = button.dataset.time || '';
            document.getElementById('booking-modal-price').textContent = button.dataset.price || '';
            bookingConfirmLink.href = button.dataset.url || '#';

            if (bookingDialog && !bookingDialog.open) {
                bookingDialog.showModal();
            }
        });
    });

    document.querySelector('[data-booking-dialog-close]')?.addEventListener('click', () => {
        bookingDialog?.close();
    });

    bookingDialog?.addEventListener('click', (event) => {
        if (event.target === bookingDialog) {
            bookingDialog.close();
        }
    });

    const updateSelectedSeatSummary = () => {
        const selectedButtons = [...document.querySelectorAll('.client-seat[data-seat-number].is-selected')];
        const seatLabel = document.getElementById('selected-seat-label');
        const seatType = document.getElementById('selected-seat-type');
        const seatNumbersInput = document.getElementById('selected-seat-numbers');
        const priceDetails = document.getElementById('selected-seat-price-details');
        const seatTotal = document.getElementById('selected-seat-total');
        const overviewSelected = document.getElementById('seat-overview-selected');
        const categorySummary = document.getElementById('seat-category-summary');

        if (!seatLabel || !seatType) {
            return;
        }

        if (selectedButtons.length === 0) {
            seatLabel.textContent = 'Chưa chọn';
            seatType.textContent = '-';
            if (seatNumbersInput) seatNumbersInput.value = '';
            if (priceDetails) priceDetails.textContent = 'Chưa chọn ghế.';
            if (seatTotal) seatTotal.textContent = '0 VNĐ';
            if (overviewSelected) overviewSelected.textContent = 'Chưa chọn';
            if (categorySummary) categorySummary.textContent = 'Chưa chọn ghế.';
            return;
        }

        const selectedGroups = new Map();
        const selectedSingles = [];

        selectedButtons.forEach((button) => {
            const coupleGroup = button.dataset.coupleGroup || '';
            if (coupleGroup) {
                if (!selectedGroups.has(coupleGroup)) {
                    selectedGroups.set(coupleGroup, []);
                }
                selectedGroups.get(coupleGroup).push(button.dataset.seatNumber);
            } else {
                selectedSingles.push(button.dataset.seatNumber);
            }
        });

        const coupleLabels = [...selectedGroups.values()].map((seats) => seats.join(' - '));
        seatLabel.textContent = [...selectedSingles, ...coupleLabels].join(', ');
        if (overviewSelected) overviewSelected.textContent = [...selectedSingles, ...coupleLabels].join(', ');

        const types = new Set(selectedButtons.map((button) => button.dataset.seatType));
        seatType.textContent = [...types].join(', ');
        if (seatNumbersInput) seatNumbersInput.value = selectedButtons.map((button) => button.dataset.seatNumber).join(',');

        const formatMoney = (amount) => new Intl.NumberFormat('vi-VN').format(amount) + ' VNĐ';
        let total = 0;
        const lines = [];
        selectedButtons.filter((button) => !button.dataset.coupleGroup).forEach((button) => {
            const price = Number(button.dataset.seatPrice || 0);
            total += price;
            const type = button.dataset.seatType || 'Standard';
            const title = type === 'VIP' ? `Ghế VIP ${button.dataset.seatNumber}` : `Ghế ${button.dataset.seatNumber}`;
            lines.push({ title, detail: `1 × ${formatMoney(price)}`, total: price });
        });
        selectedGroups.forEach((seats, group) => {
            const groupButtons = selectedButtons.filter((button) => button.dataset.coupleGroup === group);
            const unitPrice = Number(groupButtons[0]?.dataset.seatPrice || 0);
            const lineTotal = groupButtons.reduce((sum, button) => sum + Number(button.dataset.seatPrice || 0), 0);
            total += lineTotal;
            lines.push({
                title: `Ghế Couple ${seats.join(' - ')}`,
                detail: `${groupButtons.length} × ${formatMoney(unitPrice)}`,
                total: lineTotal
            });
        });

        if (priceDetails) {
            priceDetails.innerHTML = lines.map((line) =>
                `<div class="seat-price-line d-flex justify-content-between gap-3"><div><div class="seat-price-line-name">${line.title}</div><div class="seat-price-line-price">${line.detail}</div></div><strong class="text-dark text-nowrap">${formatMoney(line.total)}</strong></div>`
            ).join('');
        }
        if (seatTotal) seatTotal.textContent = formatMoney(total);

        if (categorySummary) {
            const categories = {
                Standard: { label: 'Ghế thường', count: 0, total: 0 },
                VIP: { label: 'Ghế VIP', count: 0, total: 0 },
                Couple: { label: 'Ghế đôi', count: 0, total: 0 },
            };
            selectedButtons.forEach((button) => {
                const type = button.dataset.seatType || 'Standard';
                const category = categories[type] || categories.Standard;
                category.count += 1;
                category.total += Number(button.dataset.seatPrice || 0);
            });
            categorySummary.innerHTML = Object.values(categories)
                .filter((category) => category.count > 0)
                .map((category) => `<div class="d-flex justify-content-between"><span>${category.label} × ${category.count}</span><strong class="text-dark">${formatMoney(category.total)}</strong></div>`)
                .join('');
        }
    };

    document.querySelectorAll('.client-seat[data-seat-number]:not(:disabled)').forEach((seatButton) => {
        seatButton.addEventListener('click', () => {
            const coupleGroup = seatButton.dataset.coupleGroup || '';

            if (coupleGroup) {
                const coupleButtons = [...document.querySelectorAll('.client-seat[data-couple-group]:not(:disabled)')]
                    .filter((button) => button.dataset.coupleGroup === coupleGroup);
                const shouldSelect = !coupleButtons.every((button) => button.classList.contains('is-selected'));
                coupleButtons.forEach((button) => button.classList.toggle('is-selected', shouldSelect));
            } else {
                seatButton.classList.toggle('is-selected');
            }

            updateSelectedSeatSummary();
        });
    });

    // Khôi phục các ghế đã chọn nếu khách quay lại từ bước chọn đồ ăn.
    const preselectedSeatNumbers = new Set(<?= json_encode($preselectedSeatNumbers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>);
    if (preselectedSeatNumbers.size > 0) {
        document.querySelectorAll('.client-seat[data-seat-number]:not(:disabled)').forEach((button) => {
            if (preselectedSeatNumbers.has(button.dataset.seatNumber)) {
                button.classList.add('is-selected');
            }
        });

        // Nếu một ghế Couple được khôi phục thì luôn khôi phục đủ cả cặp.
        const selectedCoupleGroups = new Set(
            [...document.querySelectorAll('.client-seat[data-couple-group].is-selected')]
                .map((button) => button.dataset.coupleGroup)
                .filter(Boolean)
        );
        selectedCoupleGroups.forEach((group) => {
            document.querySelectorAll('.client-seat[data-couple-group]:not(:disabled)').forEach((button) => {
                if (button.dataset.coupleGroup === group) {
                    button.classList.add('is-selected');
                }
            });
        });

        updateSelectedSeatSummary();
    }

</script>
