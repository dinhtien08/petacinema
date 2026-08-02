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

        $safeSeatType = htmlspecialchars(
            $seatTypeName,
            ENT_QUOTES,
            'UTF-8'
        );

        $coupleGroup = htmlspecialchars(
            $seat['couple_group'] ?? '',
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
            data-seat-id="<?= (int) $seat['id'] ?>"
            data-seat-number="<?= $seatNumber ?>"
            data-seat-type="<?= $safeSeatType ?>"
            data-couple-group="<?= $coupleGroup ?>"
            data-status="<?= htmlspecialchars(
                $seat['status'] ?? 'available',
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
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
    cursor: pointer;
    user-select: none;
    transition: all 0.2s ease-in-out;
}

.seat-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.seat-btn:disabled {
    cursor: wait;
    opacity: 0.65;
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
            href="?action=rooms"
            class="btn btn-outline-secondary rounded-3"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Quay lại
        </a>

        <?php if ($hasTickets): ?>
            <button
                type="button"
                class="btn btn-secondary rounded-3"
                disabled
                title="Phòng đã có vé bán"
            >
                <i class="bi bi-lock me-1"></i>
                Không thể sinh lại ghế
            </button>
        <?php else: ?>
            <a
                href="?action=roomGenerateSeats&id=<?= (int) $room['id'] ?>"
                class="btn btn-danger rounded-3"
                onclick="return confirm(
                    'Bạn có chắc chắn muốn sinh lại ghế? '
                    + 'Toàn bộ ghế hiện tại sẽ bị xóa và tạo mới.'
                );"
            >
                <i class="bi bi-arrow-clockwise me-1"></i>

                <?= $seatCount > 0
                    ? 'Sinh lại ghế'
                    : 'Sinh ghế tự động' ?>
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div
            class="d-flex flex-wrap align-items-center
                   justify-content-center gap-4 small"
        >
            <div class="d-flex align-items-center gap-2">
                <span
                    class="seat-btn seat-standard"
                    style="cursor: default;"
                >
                    A1
                </span>

                <span>Standard</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span
                    class="seat-btn seat-vip"
                    style="cursor: default;"
                >
                    D1
                </span>

                <span>VIP</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span
                    class="seat-btn seat-couple"
                    style="cursor: default;"
                >
                    F1
                </span>

                <span>Couple</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span
                    class="seat-btn seat-maintenance"
                    style="cursor: default;"
                >
                    A2
                </span>

                <span>Maintenance</span>
            </div>
        </div>

        <div class="text-center text-muted small mt-3">
            <i class="bi bi-info-circle me-1"></i>

            Nhấn vào ghế để chuyển giữa
            <strong>available</strong>
            và
            <strong>maintenance</strong>.
            Ghế Couple sẽ cập nhật cả hai ghế trong cặp.
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

                <p class="mb-3">
                    Phòng chiếu này chưa được sinh ghế.
                </p>

                <?php if (!$hasTickets): ?>
                    <a
                        href="?action=roomGenerateSeats&id=<?= (int) $room['id'] ?>"
                        class="btn btn-danger"
                    >
                        <i class="bi bi-plus-circle me-1"></i>
                        Sinh ghế tự động
                    </a>
                <?php endif; ?>
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
                                            <div
                                                class="seat-couple-pair"
                                                data-couple-group="<?= htmlspecialchars(
                                                    $seat['couple_group'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                            >
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll(
        '.seat-btn[data-seat-id]'
    );

    buttons.forEach(function (button) {
        button.addEventListener('click', async function () {
            const seatId = this.dataset.seatId;

            if (!seatId) {
                alert('Không tìm thấy ID ghế.');
                return;
            }

            const clickedButton = this;
            clickedButton.disabled = true;

            const formData = new FormData();
            formData.append('seat_id', seatId);

            try {
                const response = await fetch(
                    '?action=roomToggleSeat',
                    {
                        method: 'POST',
                        body: formData
                    }
                );

                const responseText = await response.text();

                console.log('HTTP status:', response.status);
                console.log('Response:', responseText);

                let data;

                try {
                    data = JSON.parse(responseText);
                } catch (error) {
                    throw new Error(
                        'Server không trả về JSON hợp lệ. Nội dung: '
                        + responseText
                    );
                }

                if (!response.ok || !data.success) {
                    throw new Error(
                        data.message
                        || 'Không thể cập nhật trạng thái ghế.'
                    );
                }

                if (!Array.isArray(data.seats)) {
                    throw new Error(
                        'Response không có danh sách ghế đã cập nhật.'
                    );
                }

                data.seats.forEach(function (updatedSeat) {
                    const targetButton = document.querySelector(
                        '.seat-btn[data-seat-id="'
                        + updatedSeat.id
                        + '"]'
                    );

                    if (!targetButton) {
                        return;
                    }

                    targetButton.dataset.status =
                        updatedSeat.status;

                    const isMaintenance =
                        updatedSeat.status === 'maintenance';

                    targetButton.classList.toggle(
                        'seat-maintenance',
                        isMaintenance
                    );

                    const seatType =
                        targetButton.dataset.seatType || '';

                    targetButton.title =
                        'Ghế '
                        + updatedSeat.seat_number
                        + ' ('
                        + seatType
                        + ' - '
                        + (
                            isMaintenance
                            ? 'Bảo trì'
                            : 'Hoạt động'
                        )
                        + ')';
                });
            } catch (error) {
                console.error(error);
                alert(error.message);
            } finally {
                clickedButton.disabled = false;
            }
        });
    });
});
</script>