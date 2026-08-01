<?php
$allowedCapacities = RoomModel::allowedCapacities();
?>

<div class="mb-4">
    <h4 class="mb-1 fw-bold">
        Thêm phòng chiếu mới
    </h4>

    <p class="text-muted mb-0">
        Tạo phòng chiếu mới. Ghế sẽ được hệ thống
        sinh tự động theo layout đã chọn.
    </p>
</div>

<div class="card">
    <div class="card-body p-4">
        <form
            method="POST"
            action="?action=roomAddProcess"
            id="roomAddForm"
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
                        $old['name'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    placeholder="Ví dụ: Phòng 01, Phòng IMAX..."
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
                                (int) ($old['room_type_id'] ?? 0)
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
                >
                    <option value="">
                        -- Chọn layout sức chứa --
                    </option>

                    <?php foreach ($allowedCapacities as $capacity): ?>
                        <option
                            value="<?= (int) $capacity ?>"
                            <?= (
                                (int) ($old['total_seats'] ?? 0)
                                === (int) $capacity
                            ) ? 'selected' : '' ?>
                        >
                            <?= (int) $capacity ?> ghế
                        </option>
                    <?php endforeach; ?>
                </select>

                <div
                    class="form-text text-muted mt-2"
                    id="capacityHelp"
                >
                    Phòng thường: 3 hàng đầu Standard,
                    hàng cuối Couple, các hàng còn lại VIP.
                    Gold Class gồm 60 ghế Couple.
                </div>
            </div>

            <div class="d-flex gap-2">
                <a
                    href="?action=rooms"
                    class="btn btn-light px-4"
                >
                    Hủy
                </a>

                <button
                    type="submit"
                    class="btn btn-danger px-4"
                >
                    <i class="bi bi-save me-1"></i>
                    Lưu phòng và sinh ghế
                </button>
            </div>
        </form>
    </div>
</div>

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