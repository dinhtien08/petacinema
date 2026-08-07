<?php
$bookings = $bookings ?? [];
?>

<style>
    .my-ticket-page { max-width: 980px; margin: 0 auto 3rem; }
    .my-ticket-heading { font-weight: 800; letter-spacing: -.02em; }
    .my-ticket-card { border: 1px solid #e2e8f0; border-radius: 16px; background: #fff; overflow: hidden; box-shadow: 0 6px 18px rgba(15, 23, 42, .06); }
    .my-ticket-poster { width: 132px; min-width: 132px; aspect-ratio: 2 / 3; object-fit: cover; background: #e2e8f0; }
    .my-ticket-code { display: inline-block; padding: .5rem .75rem; border: 1px dashed #cbd5e1; border-radius: 10px; background: #f8fafc; font-weight: 800; letter-spacing: .03em; }
    .my-ticket-label { color: #64748b; font-size: .82rem; font-weight: 600; }
    .my-ticket-value { color: #0f172a; font-weight: 700; }
    .my-ticket-format { display: inline-flex; align-items: center; padding: .28rem .6rem; border-radius: 999px; background: #fff1f2; color: #e50914; font-size: .78rem; font-weight: 800; }
    .my-ticket-status { display: inline-flex; align-items: center; gap: .35rem; font-size: .82rem; font-weight: 700; }
    .my-ticket-foods { margin: 0; padding-left: 1.1rem; }
    .my-ticket-foods li + li { margin-top: .25rem; }
    .my-ticket-empty { border: 1px dashed #cbd5e1; border-radius: 16px; background: #fff; padding: 4rem 1rem; text-align: center; }
    @media (max-width: 767.98px) {
        .my-ticket-card-inner { flex-direction: column; }
        .my-ticket-poster { width: 100%; height: 260px; aspect-ratio: auto; }
    }
</style>

<section class="my-ticket-page">
    <div class="d-flex align-items-end justify-content-between gap-3 flex-wrap mb-4">
        <div>
            <h1 class="h3 my-ticket-heading mb-1">Vé của tôi</h1>
            <p class="text-secondary mb-0">Các giao dịch đặt vé đã thanh toán thành công.</p>
        </div>
    </div>

    <?php if (empty($bookings)): ?>
        <div class="my-ticket-empty">
            <i class="bi bi-ticket-perforated fs-1 text-secondary d-block mb-3"></i>
            <h2 class="h5 mb-2">Bạn chưa có giao dịch nào</h2>
            <p class="text-secondary mb-4">Sau khi thanh toán vé thành công, thông tin vé sẽ xuất hiện tại đây.</p>
            <a href="<?= BASE_URL ?>?client_page=showtimes" class="btn btn-peta">Đặt vé ngay</a>
        </div>
    <?php else: ?>
        <div class="d-grid gap-3">
            <?php foreach ($bookings as $booking): ?>
                <?php
                $poster = '';
                if (!empty($booking['movie_poster'])) {
                    $poster = str_starts_with((string) $booking['movie_poster'], 'http')
                        ? (string) $booking['movie_poster']
                        : BASE_ASSETS_UPLOADS . $booking['movie_poster'];
                }

                $ticketCount = (int) ($booking['ticket_count'] ?? 0);
                $isCheckedIn = ($booking['checkin_status'] ?? 'pending') === 'checked_in';
                $checkinText = $isCheckedIn ? 'Đã check-in' : 'Chưa check-in';
                $checkinIcon = $isCheckedIn
                    ? 'bi-check-circle-fill text-success'
                    : 'bi-clock text-secondary';

                $foods = $booking['foods'] ?? [];
                $foodTotal = 0;
                foreach ($foods as $food) {
                    $foodTotal += (float) ($food['price_at_booking'] ?? 0) * (int) ($food['quantity'] ?? 0);
                }
                $ticketTotal = max(0, (float) ($booking['total_amount'] ?? 0) - $foodTotal);
                ?>

                <article class="my-ticket-card">
                    <div class="d-flex my-ticket-card-inner">
                        <?php if ($poster !== ''): ?>
                            <img class="my-ticket-poster" src="<?= h($poster) ?>" alt="Poster <?= h($booking['movie_title'] ?? 'phim') ?>">
                        <?php else: ?>
                            <div class="my-ticket-poster d-flex align-items-center justify-content-center text-secondary">
                                <i class="bi bi-film fs-1"></i>
                            </div>
                        <?php endif; ?>

                        <div class="p-3 p-md-4 flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                                <div>
                                    <span class="my-ticket-format mb-2"><?= h($booking['room_type_name'] ?? '-') ?></span>
                                    <h2 class="h5 mb-1"><?= h($booking['movie_title'] ?? '-') ?></h2>
                                    <div class="text-secondary small">
                                        <?= date('d/m/Y', strtotime((string) $booking['start_time'])) ?>
                                        · <?= date('H:i', strtotime((string) $booking['start_time'])) ?>
                                    </div>
                                </div>

                                <div class="text-md-end">
                                    <div class="my-ticket-label mb-1">Mã đặt vé</div>
                                    <div class="my-ticket-code"><?= h($booking['booking_code']) ?></div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6 col-md-3">
                                    <div class="my-ticket-label">Ghế</div>
                                    <div class="my-ticket-value"><?= h($booking['seat_numbers'] ?: '-') ?></div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="my-ticket-label">Số lượng vé</div>
                                    <div class="my-ticket-value"><?= $ticketCount ?> vé</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="my-ticket-label">Thanh toán</div>
                                    <div class="my-ticket-status text-success"><i class="bi bi-check-circle-fill"></i> Đã thanh toán</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="my-ticket-label">Check-in</div>
                                    <div class="my-ticket-status"><i class="bi <?= $checkinIcon ?>"></i> <?= h($checkinText) ?></div>
                                    <?php if ($isCheckedIn && !empty($booking['checked_in_at'])): ?>
                                        <div class="small text-secondary mt-1">
                                            <?= date('d/m/Y H:i', strtotime((string) $booking['checked_in_at'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if (!empty($foods)): ?>
                                <div class="border-top pt-3 mb-3">
                                    <div class="my-ticket-label mb-2">Đồ ăn &amp; thức uống</div>
                                    <ul class="my-ticket-foods small">
                                        <?php foreach ($foods as $food): ?>
                                            <li>
                                                <?= h($food['food_name']) ?>
                                                <?= !empty($food['variant_size']) ? ' (' . h($food['variant_size']) . ')' : '' ?>
                                                × <?= (int) $food['quantity'] ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="border-top pt-3 d-flex justify-content-between gap-3 flex-wrap align-items-end">
                                <div class="small text-secondary">
                                    <div>Tiền vé: <strong class="text-dark"><?= number_format($ticketTotal, 0, ',', '.') ?> VNĐ</strong></div>
                                    <?php if ($foodTotal > 0): ?>
                                        <div>Đồ ăn: <strong class="text-dark"><?= number_format($foodTotal, 0, ',', '.') ?> VNĐ</strong></div>
                                    <?php endif; ?>
                                    <?php if (!empty($booking['payment_time'])): ?>
                                        <div class="mt-1">Thanh toán lúc <?= date('d/m/Y H:i', strtotime((string) $booking['payment_time'])) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="text-end">
                                    <div class="my-ticket-label">Tổng thanh toán</div>
                                    <div class="fs-5 fw-bold text-danger"><?= number_format((float) $booking['total_amount'], 0, ',', '.') ?> VNĐ</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>