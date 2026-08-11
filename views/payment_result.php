<style>
    .payment-result-card { max-width: 720px; margin: 2rem auto 4rem; border: 1px solid #e2e8f0; border-radius: 1rem; background: #fff; }
    .payment-result-icon { width: 68px; height: 68px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; }
    .payment-result-icon.success { background: #dcfce7; color: #15803d; }
    .payment-result-icon.failed { background: #fee2e2; color: #dc2626; }
    .booking-code-box { border-radius: .8rem; background: #f8fafc; border: 1px dashed #cbd5e1; }
</style>

<section class="payment-result-card p-4 p-md-5 text-center shadow-sm">
    <div class="payment-result-icon <?= $success ? 'success' : 'failed' ?> mb-3">
        <i class="bi <?= $success ? 'bi-check-lg' : 'bi-x-lg' ?>"></i>
    </div>

    <h1 class="h4 mb-2"><?= $success ? 'Thanh toán thành công' : 'Thanh toán chưa thành công' ?></h1>
    <p class="text-secondary mb-4"><?= h($message) ?></p>

    <?php if ($booking): ?>
        <div class="booking-code-box p-3 mb-4 text-start">
            <div class="small text-secondary">Mã đặt vé</div>
            <div class="fw-bold fs-5 text-dark mb-2"><?= h($booking['booking_code']) ?></div>
            <div class="small"><span class="text-secondary">Phim:</span> <?= h($booking['movie_title']) ?></div>
            <div class="small"><span class="text-secondary">Suất:</span> <?= date('d/m/Y H:i', strtotime((string) $booking['start_time'])) ?></div>
            <?php
                $seatList = array_values(array_filter(array_map('trim', explode(',', (string) ($booking['seat_numbers'] ?? '')))));
                $visibleSeats = array_slice($seatList, 0, 8);
                $hiddenSeatCount = max(0, count($seatList) - count($visibleSeats));
            ?>
            <div class="small" title="<?= h((string) ($booking['seat_numbers'] ?? '')) ?>">
                <span class="text-secondary">Ghế:</span>
                <?= !empty($visibleSeats) ? h(implode(', ', $visibleSeats)) : '-' ?>
                <?php if ($hiddenSeatCount > 0): ?>
                    <span class="text-secondary">+<?= $hiddenSeatCount ?> ghế</span>
                <?php endif; ?>
            </div>
            <div class="small"><span class="text-secondary">Tổng tiền:</span> <?= number_format((float) $booking['total_amount'], 0, ',', '.') ?> VNĐ</div>
        </div>
    <?php endif; ?>

    <div class="d-flex flex-wrap justify-content-center gap-2">
        <?php if ($success): ?>
            <a href="<?= BASE_URL ?>?action=my_tickets" class="btn btn-peta">Vé của tôi</a>
            <a href="<?= BASE_URL ?>" class="btn btn-outline-peta">Về trang chủ</a>
        <?php else: ?>
            <a href="<?= h($retryUrl) ?>" class="btn btn-peta">Chọn lại ghế</a>
            <a href="<?= BASE_URL ?>" class="btn btn-outline-peta">Về trang chủ</a>
        <?php endif; ?>
    </div>
</section>