<div class="container py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1"><i class="bi bi-ticket-perforated me-2 text-danger"></i>Vé Của Tôi</h2>
            <p class="text-muted small mb-0">Quản lý các vé xem phim đã đặt tại PETACINEMA</p>
        </div>
    </div>

    <?php $flash = get_flash(); ?>
    <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> mb-4">
            <i class="bi bi-info-circle me-2"></i><?= h($flash['message']) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($myTickets)): ?>
        <div class="row g-4">
            <?php foreach ($myTickets as $ticket): ?>
                <?php 
                    $posterUrl = !empty($ticket['movie_poster']) ? BASE_ASSETS_UPLOADS . $ticket['movie_poster'] : 'https://placehold.co/150x220/1e293b/ffffff?text=Movie';
                    $status    = $ticket['status'] ?? 'pending';
                    $statusBadge = match($status) {
                        'paid'      => '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Đã thanh toán</span>',
                        'cancelled' => '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Đã hủy</span>',
                        default     => '<span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Chờ thanh toán</span>'
                    };
                ?>
                <div class="col-lg-6">
                    <div class="peta-card p-3 d-flex gap-3 align-items-center">
                        <img src="<?= $posterUrl ?>" alt="Poster" class="rounded-3 object-fit-cover" style="width: 110px; height: 160px;">
                        
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-secondary font-monospace"><?= h($ticket['booking_code']) ?></span>
                                <?= $statusBadge ?>
                            </div>

                            <h5 class="fw-bold text-white mb-2"><?= h($ticket['movie_title']) ?></h5>
                            
                            <div class="small text-muted mb-2">
                                <div><i class="bi bi-calendar3 me-1 text-danger"></i> Suất chiếu: <strong class="text-white"><?= date('H:i - d/m/Y', strtotime($ticket['start_time'])) ?></strong></div>
                                <div><i class="bi bi-display me-1 text-info"></i> Phòng: <strong><?= h($ticket['room_name'] ?? 'Phòng chiếu') ?></strong></div>
                                <div><i class="bi bi-grid-3x3-gap me-1 text-warning"></i> Ghế chọn: <strong class="text-warning"><?= h($ticket['seat_numbers'] ?? 'N/A') ?></strong></div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-top border-secondary pt-2 mt-2">
                                <span class="small text-muted">Tổng tiền:</span>
                                <span class="fw-bold text-danger fs-5"><?= number_format($ticket['total_amount'] ?? 0, 0, ',', '.') ?> VNĐ</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-secondary text-center py-5 rounded-4">
            <i class="bi bi-ticket-dashed fs-1 d-block mb-3 text-muted"></i>
            <h5 class="text-white">Bạn chưa có lịch sử đặt vé nào</h5>
            <p class="text-muted small mb-3">Hãy chọn bộ phim yêu thích và đặt vé ngay hôm nay!</p>
            <a href="<?= BASE_URL ?>" class="btn btn-peta-primary">
                <i class="bi bi-film me-1"></i> Khám phá phim ngay
            </a>
        </div>
    <?php endif; ?>

</div>
