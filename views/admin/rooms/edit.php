<?php
$allowedCapacities = RoomModel::allowedCapacities();

$isLayoutLocked = (
    !empty($hasShowtimes)
    || !empty($hasTickets)
);

$currentRoomTypeId = (int) (
    $old['room_type_id']
    ?? $room['room_type_id']
);

$currentTotalSeats = (int) (
    $old['total_seats']
    ?? $room['total_seats']
);
?>

<div class="mb-4">
    <h4 class="mb-1 fw-bold">
        Sửa thông tin phòng chiếu
    </h4>

    <p class="text-muted mb-0">
        Cập nhật phòng chiếu
        <strong>
            <?= htmlspecialchars(
                $room['name'] ?? '',
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </strong>.
    </p>
</div>

<?php if ($isLayoutLocked): ?>
    <div
        class="alert alert-warning mb-4
               d-flex align-items-center"
        role="alert"
    >
        <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>

        <div>
            <strong>Lưu ý:</strong>
            Phòng đã có suất chiếu hoặc vé liên quan.
            Bạn chỉ có thể thay đổi
            <strong>Tên phòng</strong>.
        </div>
    </div>
<?php elseif (!empty($hasSeats)): ?>
    <div
        class="alert alert-info mb-4
               d-flex align-items-center"
        role="alert"
    >
        <i class="bi bi-info-circle-fill fs-4 me-3"></i>

        <div>
            <strong>Thông báo:</strong>
            Nếu thay đổi loại phòng hoặc sức chứa,
            sơ đồ ghế hiện tại sẽ bị xóa và sinh lại.
        </div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-4">
        <form
            method="POST"
            action="?action=roomEditProcess&id=<?= (int) $room['id'] ?>"
            id="roomEditForm"
        >
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Tên phòng
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="text"
                    name="name"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        $old['name']
                            ?? $room['name']
                            ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    maxlength="100"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Loại phòng
                    <span class="text-danger">*</span>
                </label>

                <select
                    name="room_type_id"
                    id="room_type_id"
                    class="form-select"
                    required
                    <?= $isLayoutLocked ? 'disabled' : '' ?>
                >
                    <option value="">
                        -- Chọn loại phòng --
                    </option>

                    <?php foreach ($listRoomType as $roomType): ?>
                        <option
                            value="<?= (int) $roomType['id'] ?>"
                            data-name="<?= htmlspecialchars(
                                $roomType['name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            <?= (
                                $currentRoomTypeId
                                === (int) $roomType['id']
                            ) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars(
                                $roomType['name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if ($isLayoutLocked): ?>
                    <input
                        type="hidden"
                        name="room_type_id"
                        value="<?= (int) $room['room_type_id'] ?>"
                    >
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Sức chứa
                    <span class="text-danger">*</span>
                </label>

                <select
                    name="total_seats"
                    id="total_seats"
                    class="form-select"
                    required
                    <?= $isLayoutLocked ? 'disabled' : '' ?>
                >
                    <?php foreach ($allowedCapacities as $capacity): ?>
                        <option
                            value="<?= (int) $capacity ?>"
                            <?= (
                                $currentTotalSeats
                                === (int) $capacity
                            ) ? 'selected' : '' ?>
                        >
                            <?= (int) $capacity ?> ghế
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if ($isLayoutLocked): ?>
                    <input
                        type="hidden"
                        name="total_seats"
                        value="<?= (int) $room['total_seats'] ?>"
                    >
                <?php endif; ?>

                <div
                    id="capacityHelp"
                    class="form-text mt-2"
                >
                    Phòng thường: 3 hàng đầu Standard,
                    hàng cuối Couple, các hàng còn lại VIP.
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a
                    href="?action=rooms"
                    class="btn btn-light px-4"
                >
                    Hủy
                </a>

                <a
                    href="?action=roomSeats&id=<?= (int) $room['id'] ?>"
                    class="btn btn-outline-secondary px-4"
                >
                    <i class="bi bi-grid-3x3-gap me-1"></i>
                    Xem sơ đồ ghế
                </a>

                <button
                    type="submit"
                    class="btn btn-danger px-4"
                >
                    <i class="bi bi-check-lg me-1"></i>
                    Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (!$isLayoutLocked): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const roomTypeSelect =
        document.getElementById('room_type_id');

    const capacitySelect =
        document.getElementById('total_seats');

    const capacityHelp =
        document.getElementById('capacityHelp');

    function updateLayoutOptions() {
        const selectedOption =
            roomTypeSelect.options[
                roomTypeSelect.selectedIndex
            ];

        const typeName = selectedOption
            ? selectedOption.dataset.name || ''
            : '';

        const isGoldClass =
            typeName.trim().toLowerCase() === 'gold class';

        Array.from(capacitySelect.options)
            .forEach(function (option) {
                if (!option.value) {
                    return;
                }

                const disabled =
                    isGoldClass
                    && option.value !== '60';

                option.hidden = disabled;
                option.disabled = disabled;
            });

        if (isGoldClass) {
            capacitySelect.value = '60';

            capacityHelp.innerHTML =
                '<strong class="text-warning">'
                + '<i class="bi bi-star-fill me-1"></i>'
                + 'Gold Class:</strong> '
                + '60 ghế Couple, tương đương 30 cặp.';
        } else {
            const selectedCapacity =
                capacitySelect.options[
                    capacitySelect.selectedIndex
                ];

            if (
                selectedCapacity
                && selectedCapacity.disabled
            ) {
                capacitySelect.value = '';
            }

            capacityHelp.textContent =
                'Phòng thường: 3 hàng đầu Standard, '
                + 'hàng cuối Couple, các hàng còn lại VIP.';
        }
    }

    roomTypeSelect.addEventListener(
        'change',
        updateLayoutOptions
    );

    updateLayoutOptions();
});
</script>
<?php endif; ?>