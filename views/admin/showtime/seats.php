<?php
// TODO: Protect this route with Admin/Staff middleware later.

if (!function_exists('renderShowtimeSeatButton')) {
    function renderShowtimeSeatButton($seat)
    {
        $seatTypeName = $seat['seat_type_name'] ?? 'Standard';

        switch ($seatTypeName) {
            case 'VIP':
                $typeClass = 'seat-vip';
                break;

            case 'Couple':
                $typeClass = 'seat-couple';
                break;

            default:
                $typeClass = 'seat-standard';
                break;
        }

        $displayStatus = $seat['display_status'] ?? 'available';

        $statusClass = '';
        $statusText = 'Còn trống';

        if ($displayStatus === 'maintenance') {
            $statusClass = 'seat-maintenance';
            $statusText = 'Bảo trì';
        } elseif ($displayStatus === 'booked') {
            $statusClass = 'seat-booked';
            $statusText = 'Đã đặt';
        }

        $seatNumber = htmlspecialchars(
            $seat['seat_number'] ?? '',
            ENT_QUOTES,
            'UTF-8'
        );

        $title = htmlspecialchars(
            'Ghế '
            . ($seat['seat_number'] ?? '')
            . ' ('
            . $seatTypeName
            . ' - '
            . $statusText
            . ')',
            ENT_QUOTES,
            'UTF-8'
        );
        ?>

        <button
            type="button"
            class="seat-btn <?= $typeClass ?> <?= $statusClass ?>"
            disabled
            title="<?= $title ?>"
        >
            <?= $seatNumber ?>
        </button>

        <?php
    }
}

$groupedSeats = [];

if (!empty($seats)) {
    foreach ($seats as $seat) {
        $rowChar = $seat['row_char'] ?? '';

        if (!isset($groupedSeats[$rowChar])) {
            $groupedSeats[$rowChar] = [];
        }

        $groupedSeats[$rowChar][] = $seat;
    }

    ksort($groupedSeats);

    foreach ($groupedSeats as &$rowSeats) {
        usort(
            $rowSeats,
            function ($firstSeat, $secondSeat) {
                return (int) $firstSeat['col_num']
                    <=> (int) $secondSeat['col_num'];
            }
        );
    }

    unset($rowSeats);
}

// Tính trạng thái suất chiếu
$now = date('Y-m-d H:i:s');
$startTime = $showtime['start_time'] ?? '';
$endTime = $showtime['end_time'] ?? '';

if ($startTime > $now) {
    $showtimeStatusBadge = '<span class="badge bg-info">Sắp chiếu</span>';
} elseif ($startTime <= $now && $endTime >= $now) {
    $showtimeStatusBadge = '<span class="badge bg-success">Đang chiếu</span>';
} else {
    $showtimeStatusBadge = '<span class="badge bg-secondary">Đã chiếu</span>';
}
?>

<style>
.cinema-screen {
    max-width: 760px;
    margin: 0 auto;
    padding: 12px;
    text-align: center;
    font-weight: 700;
    letter-spacing: 4px;
    color: #6c757d;
    background:
        linear-gradient(
            180deg,
            #e9ecef 0%,
            #ffffff 100%
        );
    border-top: 4px solid #0d6efd;
    border-radius: 50% 50% 0 0 / 20px 20px 0 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.seat-map-wrapper {
    min-width: max-content;
    padding: 10px 20px 30px;
}

.seat-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.seat-row-content {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
}

.seat-row-label {
    width: 28px;
    flex: 0 0 28px;
    text-align: center;
    font-weight: 700;
    color: #6c757d;
}

.seat-btn {
    width: 44px;
    height: 40px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 600;
    user-select: none;
    transition: all 0.2s ease-in-out;
}

.seat-btn:disabled {
    cursor: not-allowed;
}

.seat-standard {
    color: #212529;
    background-color: #f8f9fa;
    border: 2px solid #6c757d;
}

.seat-vip {
    color: #664d03;
    background-color: #fff3cd;
    border: 2px solid #ffc107;
}

.seat-couple {
    color: #842029;
    background-color: #f8d7da;
    border: 2px solid #dc3545;
}

.seat-booked {
    color: #ffffff !important;
    background-color: #dc3545 !important;
    border-color: #b02a37 !important;
}

.seat-maintenance {
    color: #ffffff !important;
    background-color: #343a40 !important;
    border-color: #212529 !important;
    opacity: 0.85;
    text-decoration: line-through;
}

.seat-couple-pair {
    display: inline-flex;
    gap: 3px;
    padding: 3px 5px;
    background-color: rgba(220, 53, 69, 0.08);
    border: 1px dashed rgba(220, 53, 69, 0.45);
    border-radius: 10px;
}

@media (max-width: 768px) {
    .seat-btn {
        width: 40px;
        height: 37px;
        font-size: 0.72rem;
    }
}
</style>

<!-- Header & Action Navigation -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
    <div>
        <h4 class="mb-1 fw-bold">
            Sơ đồ ghế theo suất chiếu
        </h4>

        <p class="text-muted mb-0">
            Phim: <strong><?= htmlspecialchars($showtime['movie_title'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
            <span class="mx-1">|</span>
            Phòng: <strong><?= htmlspecialchars($showtime['room_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
            (<?= htmlspecialchars($showtime['room_type'] ?? '', ENT_QUOTES, 'UTF-8') ?>)
            <span class="mx-1">|</span>
            Thời gian: <strong><?= date('d/m/Y H:i', strtotime($showtime['start_time'])) ?> - <?= date('H:i', strtotime($showtime['end_time'])) ?></strong>
            <span class="mx-1">|</span>
            Trạng thái: <?= $showtimeStatusBadge ?>
        </p>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <a href="?action=showtimes" class="btn btn-outline-secondary rounded-3">
            <i class="bi bi-arrow-left me-1"></i>
            Quay lại danh sách suất chiếu
        </a>

        <a href="?action=showtime_show&id=<?= (int)$showtime['id'] ?>" class="btn btn-outline-primary rounded-3">
            <i class="bi bi-eye me-1"></i>
            Xem chi tiết suất chiếu
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3 h-100 justify-content-center">
            <div class="text-muted small mb-1 fw-semibold">Tổng số ghế</div>
            <div class="fs-4 fw-bold text-dark"><?= (int)$totalSeats ?></div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3 h-100 justify-content-center">
            <div class="text-muted small mb-1 fw-semibold">Còn trống</div>
            <div class="fs-4 fw-bold text-success"><?= (int)$availableCount ?></div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3 h-100 justify-content-center">
            <div class="text-muted small mb-1 fw-semibold">Đã đặt (theo Bookings)</div>
            <div class="fs-4 fw-bold text-danger"><?= (int)$bookedCount ?> ghế</div>
            <div class="text-muted" style="font-size: 0.75rem;">(<?= (int)($validBookingCount ?? 0) ?> đơn đặt vé)</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3 h-100 justify-content-center">
            <div class="text-muted small mb-1 fw-semibold">Bảo trì</div>
            <div class="fs-4 fw-bold text-secondary"><?= (int)$maintenanceCount ?></div>
        </div>
    </div>
</div>

<!-- Legend Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap align-items-center justify-content-center gap-4 small">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="seat-btn seat-standard" disabled>A1</button>
                <span>Ghế Standard</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="seat-btn seat-vip" disabled>D1</button>
                <span>Ghế VIP</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="seat-btn seat-couple" disabled>F1</button>
                <span>Ghế Couple</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="seat-btn seat-standard" disabled>A1</button>
                <span>Còn trống</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="seat-btn seat-booked" disabled>B1</button>
                <span>Đã đặt</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="seat-btn seat-maintenance" disabled>C1</button>
                <span>Bảo trì</span>
            </div>
        </div>
    </div>
</div>

<!-- Seat Map Container -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4 overflow-auto">
        <?php if (empty($groupedSeats)): ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-grid-3x3-gap fs-1 d-block mb-2"></i>
                <p class="mb-0 fs-5 fw-semibold">
                    Phòng chiếu này chưa có sơ đồ ghế.
                </p>
            </div>
        <?php else: ?>
            <div class="seat-map-wrapper">
                <div class="mb-5">
                    <div class="cinema-screen">
                        MÀN HÌNH CHÍNH
                    </div>
                </div>

                <div class="d-flex flex-column align-items-center gap-3">
                    <?php foreach ($groupedSeats as $rowChar => $rowSeats): ?>
                        <div class="seat-row">
                            <span class="seat-row-label">
                                <?= htmlspecialchars($rowChar, ENT_QUOTES, 'UTF-8') ?>
                            </span>

                            <div class="seat-row-content">
                                <?php
                                $rowSeatCount = count($rowSeats);

                                for ($index = 0; $index < $rowSeatCount; $index++):
                                    $seat = $rowSeats[$index];

                                    $isCouple = (
                                        strcasecmp(
                                            $seat['seat_type_name'] ?? '',
                                            'Couple'
                                        ) === 0
                                        && !empty($seat['couple_group'])
                                    );

                                    if ($isCouple):
                                        $nextSeat = $rowSeats[$index + 1] ?? null;

                                        $isSameCouple = (
                                            $nextSeat
                                            && !empty($nextSeat['couple_group'])
                                            && $nextSeat['couple_group'] === $seat['couple_group']
                                        );

                                        if ($isSameCouple):
                                ?>
                                            <div class="seat-couple-pair">
                                                <?php
                                                renderShowtimeSeatButton($seat);
                                                renderShowtimeSeatButton($nextSeat);
                                                ?>
                                            </div>
                                <?php
                                            $index++;
                                        else:
                                            renderShowtimeSeatButton($seat);
                                        endif;
                                    else:
                                        renderShowtimeSeatButton($seat);
                                    endif;
                                endfor;
                                ?>
                            </div>

                            <span class="seat-row-label">
                                <?= htmlspecialchars($rowChar, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Bookings List Card -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 fw-bold">
            <i class="bi bi-receipt me-2"></i>
            Danh sách đơn đặt vé theo suất chiếu (Bookings)
        </h5>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Mã đơn hàng</th>
                        <th>Khách hàng</th>
                        <th>Danh sách ghế</th>
                        <th class="text-center">Số ghế</th>
                        <th class="text-end">Tổng tiền</th>
                        <th class="text-center">Trạng thái</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bookings)): ?>
                        <?php foreach ($bookings as $idx => $b): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td>
                                    <span class="fw-semibold text-primary"><?= htmlspecialchars($b['booking_code'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td>
                                    <div><?= htmlspecialchars($b['customer_name'] ?? 'Khách vãng lai', ENT_QUOTES, 'UTF-8') ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($b['customer_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                                </td>
                                <td>
                                    <?php if (!empty($b['seat_labels'])): ?>
                                        <span class="badge text-bg-secondary"><?= htmlspecialchars($b['seat_labels'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">Chưa xếp ghế</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center fw-bold">
                                    <?= (int)($b['ticket_count'] ?? 0) ?>
                                </td>
                                <td class="text-end fw-semibold text-danger">
                                    <?= number_format((float)($b['total_amount'] ?? 0), 0, ',', '.') ?> đ
                                </td>
                                <td class="text-center">
                                    <?php
                                    switch ($b['status']) {
                                        case 'paid':
                                            echo '<span class="badge bg-success">Đã thanh toán</span>';
                                            break;
                                        case 'pending':
                                            echo '<span class="badge bg-warning text-dark">Chờ thanh toán</span>';
                                            break;
                                        case 'cancelled':
                                            echo '<span class="badge bg-danger">Đã hủy</span>';
                                            break;
                                        default:
                                            echo '<span class="badge bg-secondary">' . htmlspecialchars($b['status']) . '</span>';
                                            break;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= !empty($b['created_at']) ? date('d/m/Y H:i', strtotime($b['created_at'])) : '-' ?>
                                    </small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                Chưa có đơn đặt vé nào cho suất chiếu này.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
