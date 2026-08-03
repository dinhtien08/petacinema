<?php

if (!function_exists('renderSeatButton')) {
    function renderSeatButton($seat)
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

        $isMaintenance =
            ($seat['status'] ?? 'available')
            === 'maintenance';

        $statusClass = $isMaintenance
            ? 'seat-maintenance'
            : '';

        $statusText = $isMaintenance
            ? 'Bảo trì'
            : 'Hoạt động';

        $seatNumber = htmlspecialchars(
            $seat['seat_number'] ?? '',
            ENT_QUOTES,
            'UTF-8'
        );

        $seatTypeSafe = htmlspecialchars(
            $seatTypeName,
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

        <span
            class="seat-btn <?= $typeClass ?> <?= $statusClass ?>"
            title="<?= $title ?>"
        >
            <?= $seatNumber ?>
        </span>

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

<div
    class="d-flex flex-wrap justify-content-between
           align-items-center gap-3 mb-3"
>
    <div>
        <h4 class="mb-1 fw-bold">
            Sơ đồ ghế:
            <?= htmlspecialchars(
                $room['name'] ?? '',
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </h4>

        <p class="text-muted mb-0">
            Loại phòng:

            <span class="badge text-bg-primary">
                <?= htmlspecialchars(
                    $room['room_type_name'] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>

            <span class="mx-1">|</span>

            Sức chứa:
            <strong>
                <?= (int) ($room['total_seats'] ?? 0) ?>
                ghế
            </strong>

            <span class="mx-1">|</span>

            Ghế hiện tại:
            <strong>
                <?= (int) ($seatCount ?? 0) ?>
                ghế
            </strong>
        </p>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <a
            href="?action=staff_rooms"
            class="btn btn-outline-secondary rounded-3"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Quay lại
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div
            class="d-flex flex-wrap align-items-center
                   justify-content-center gap-4 small"
        >
            <div class="d-flex align-items-center gap-2">
                <span class="seat-btn seat-standard">A1</span>
                <span>Standard</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="seat-btn seat-vip">D1</span>
                <span>VIP</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="seat-btn seat-couple">F1</span>
                <span>Couple</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="seat-btn seat-maintenance">A2</span>
                <span>Maintenance</span>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4 overflow-auto">
        <?php if (empty($groupedSeats)): ?>
            <div class="text-center text-muted py-5">
                <i
                    class="bi bi-grid-3x3-gap fs-1
                           d-block mb-2"
                ></i>

                <p class="mb-0">
                    Phòng chiếu này chưa được sinh ghế.
                </p>
            </div>
        <?php else: ?>
            <div class="seat-map-wrapper">
                <div class="mb-5">
                    <div class="cinema-screen">
                        MÀN HÌNH CHÍNH
                    </div>
                </div>

                <div
                    class="d-flex flex-column
                           align-items-center gap-3"
                >
                    <?php foreach (
                        $groupedSeats
                        as $rowChar => $rowSeats
                    ): ?>
                        <div class="seat-row">
                            <span class="seat-row-label">
                                <?= htmlspecialchars(
                                    $rowChar,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </span>

                            <div class="seat-row-content">
                                <?php
                                $rowSeatCount = count($rowSeats);

                                for (
                                    $index = 0;
                                    $index < $rowSeatCount;
                                    $index++
                                ):
                                    $seat = $rowSeats[$index];

                                    $isCouple = (
                                        strcasecmp(
                                            $seat['seat_type_name'],
                                            'Couple'
                                        ) === 0
                                        && !empty(
                                            $seat['couple_group']
                                        )
                                    );

                                    if ($isCouple):
                                        $nextSeat =
                                            $rowSeats[$index + 1]
                                            ?? null;

                                        $isSameCouple = (
                                            $nextSeat
                                            && !empty(
                                                $nextSeat['couple_group']
                                            )
                                            && $nextSeat['couple_group']
                                                === $seat['couple_group']
                                        );

                                        if ($isSameCouple):
                                ?>
                                            <div class="seat-couple-pair">
                                                <?php
                                                renderSeatButton($seat);
                                                renderSeatButton($nextSeat);
                                                ?>
                                            </div>

                                <?php
                                            $index++;
                                        else:
                                            renderSeatButton($seat);
                                        endif;
                                    else:
                                        renderSeatButton($seat);
                                    endif;
                                endfor;
                                ?>
                            </div>

                            <span class="seat-row-label">
                                <?= htmlspecialchars(
                                    $rowChar,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>