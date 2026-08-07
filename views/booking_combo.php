<?php
    $foodModel = new FoodModel();
    $variantModel = new FoodVariantModel();
    $foodsById = [];
    foreach ($foodModel->getAll() as $food) {
        if (($food['status'] ?? '') === 'active') {
            $foodsById[(int) $food['id']] = $food;
        }
    }

    $groupForFood = static function (string $name): string {
        $normalized = mb_strtolower($name, 'UTF-8');
        if (str_contains($normalized, 'combo')) return 'combo';
        if (str_contains($normalized, 'bắp') || str_contains($normalized, 'popcorn')) return 'popcorn';
        return 'drink';
    };
    $products = ['popcorn' => [], 'drink' => [], 'combo' => []];
    foreach ($variantModel->getAll() as $variant) {
        $food = $foodsById[(int) $variant['food_id']] ?? null;
        if (!$food || (int) $variant['stock'] <= 0) continue;
        $variant['food'] = $food;
        $variant['group'] = $groupForFood((string) $food['name']);
        $products[$variant['group']][] = $variant;
    }

    $quantities = [];
    foreach ($_POST['food_quantities'] ?? [] as $variantId => $quantity) {
        $quantities[(int) $variantId] = max(0, (int) $quantity);
    }

    $foodTotal = 0;
    foreach ($products as $groupProducts) {
        foreach ($groupProducts as $product) {
            $id = (int) $product['id'];
            $quantities[$id] = min((int) $product['stock'], $quantities[$id] ?? 0);
            $foodTotal += (float) $product['price'] * $quantities[$id];
        }
    }

    $ticketLines = [];
    $handledCoupleGroups = [];
    foreach ($selectedSeatsForCombo as $seat) {
        $coupleGroup = trim((string) ($seat['couple_group'] ?? ''));
        $isCouple = ($seat['seat_type_name'] ?? '') === 'Couple' && $coupleGroup !== '';
        if ($isCouple && isset($handledCoupleGroups[$coupleGroup])) continue;
        $lineSeats = $isCouple
            ? array_values(array_filter($selectedSeatsForCombo, fn($item) => ($item['couple_group'] ?? '') === $coupleGroup))
            : [$seat];
        if ($isCouple) $handledCoupleGroups[$coupleGroup] = true;
        $unitPrice = (float) ($seatPriceById[(int) $lineSeats[0]['id']] ?? 0);
        $lineTotal = array_sum(array_map(fn($item) => (float) ($seatPriceById[(int) $item['id']] ?? 0), $lineSeats));
        $ticketLines[] = [
            'name' => $isCouple ? 'Ghế Couple' : (($seat['seat_type_name'] ?? '') === 'VIP' ? 'Ghế VIP' : 'Ghế thường'),
            'seats' => implode(' - ', array_column($lineSeats, 'seat_number')),
            'quantity' => count($lineSeats),
            'unit_price' => $unitPrice,
            'total' => $lineTotal,
        ];
    }
    $user = $_SESSION['user'] ?? [];
    $grandTotal = $ticketTotalForCombo + $foodTotal;
    $groupLabels = ['popcorn' => ['Bắp', 'cup-hot'], 'drink' => ['Nước', 'cup-straw'], 'combo' => ['Combo', 'gift']];
    $poster = trim((string) ($movie['poster'] ?? ''));
?>

<style>
    .confirmation-section { border: 1px solid var(--peta-card-border); border-radius: 1rem; background: #fff; padding: 1.25rem; }
    .combo-card { overflow: hidden; border: 1px solid var(--peta-card-border); border-radius: .9rem; transition: transform .25s ease, box-shadow .25s ease; }
    .combo-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(15, 23, 42, .12); }
    .combo-card-image { width: 100%; height: 150px; object-fit: cover; background: #f1f5f9; }
    .combo-card-placeholder { height: 150px; color: var(--peta-accent); background: #fff1f2; }
    .combo-quantity { border: 1px solid var(--peta-card-border); border-radius: .6rem; overflow: hidden; }
    .combo-quantity button { width: 2.15rem; border: 0; background: #fff1f2; color: var(--peta-accent); font-size: 1.2rem; font-weight: 700; transition: all .2s ease; }
    .combo-quantity button:hover { background: var(--peta-accent); color: #fff; }
    .combo-quantity output { min-width: 2rem; text-align: center; font-weight: 700; line-height: 2.3rem; }
    .combo-tabs { border-bottom: 1px solid var(--peta-card-border); gap: .5rem; }
    .combo-tab { border: 0; border-radius: .6rem .6rem 0 0; background: transparent; color: var(--peta-text-muted); font-weight: 700; padding: .65rem 1rem; transition: background-color .2s ease, color .2s ease; }
    .combo-tab:hover { background: #fff1f2; color: var(--peta-accent); }
    .combo-tab.active { background: var(--peta-accent); color: #fff; }
    .combo-tab-panel[hidden] { display: none !important; }
    .order-panel { position: sticky; top: 94px; border: 1px solid #fecdd3; border-radius: 1rem; }
    .order-poster { width: 92px; height: 132px; border-radius: .7rem; object-fit: cover; background: #e2e8f0; }
    .price-row { padding: .6rem 0; border-bottom: 1px dashed #e2e8f0; }
    .price-row:last-of-type { border-bottom: 0; }
    .grand-total { background: #fff1f2; color: var(--peta-accent); border-radius: .7rem; }
</style>

<nav aria-label="breadcrumb" class="mb-4 small"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-danger text-decoration-none">Trang chủ</a></li><li class="breadcrumb-item active">Xác nhận đặt vé</li></ol></nav>

<form id="combo-form" method="POST" action="<?= BASE_URL ?>?action=booking_date">
    <input type="hidden" name="movie_id" value="<?= (int) $movie['id'] ?>"><input type="hidden" name="date" value="<?= h($selectedDate) ?>"><input type="hidden" name="showtime_id" value="<?= (int) $selectedShowtimeId ?>"><input type="hidden" name="booking_step" value="adjust_combo"><input type="hidden" name="seat_numbers" value="<?= h(implode(',', array_column($selectedSeatsForCombo, 'seat_number'))) ?>">
    <div class="row g-4 align-items-start">
        <main class="col-lg-8">
            <section class="confirmation-section mb-4">
                <h2 class="h5 text-dark mb-3"><i class="bi bi-person-vcard-fill text-danger me-2"></i>THÔNG TIN THANH TOÁN</h2>
                <div class="row g-3 small"><div class="col-md-4"><span class="d-block text-secondary">Họ và tên</span><strong><?= h($user['fullname'] ?? '') ?></strong></div><div class="col-md-4"><span class="d-block text-secondary">Số điện thoại</span><strong><?= h($user['phone'] ?? 'Chưa cập nhật') ?></strong></div><div class="col-md-4"><span class="d-block text-secondary">Email</span><strong class="text-break"><?= h($user['email'] ?? '') ?></strong></div></div>
            </section>

            <section class="confirmation-section">
                <div class="d-flex justify-content-between align-items-center mb-4"><h2 class="h5 text-dark mb-0"><i class="bi bi-gift-fill text-danger me-2"></i>COMBO ƯU ĐÃI</h2><span class="small text-secondary">Chọn thêm theo nhu cầu</span></div>
                <div class="combo-tabs d-flex mb-4" role="tablist" aria-label="Danh mục combo">
                    <button type="button" class="combo-tab active" data-combo-tab="combo" role="tab" aria-selected="true">🎁 Combo</button>
                    <button type="button" class="combo-tab" data-combo-tab="drink" role="tab" aria-selected="false">🥤 Nước</button>
                    <button type="button" class="combo-tab" data-combo-tab="popcorn" role="tab" aria-selected="false">🍿 Bắp</button>
                </div>
                <?php foreach ($groupLabels as $group => [$label, $icon]): ?>
                    <div class="combo-tab-panel mb-1" data-combo-panel="<?= h($group) ?>"<?= $group === 'combo' ? '' : ' hidden' ?>><h3 class="h6 text-dark mb-3"><i class="bi bi-<?= $icon ?>-fill text-danger me-2"></i><?= $label ?></h3>
                    <?php if (empty($products[$group])): ?><p class="small text-secondary mb-0">Hiện chưa có sản phẩm đang hoạt động.</p>
                    <?php else: ?><div class="row row-cols-1 row-cols-sm-2 g-3">
                        <?php foreach ($products[$group] as $product): ?>
                            <?php $food = $product['food']; $id = (int) $product['id']; $image = trim((string) ($food['image'] ?? '')); ?>
                            <div class="col"><article class="card combo-card h-100 bg-white">
                                <?php if ($image !== ''): ?><img class="combo-card-image" src="<?= h(str_starts_with($image, 'http') ? $image : BASE_ASSETS_UPLOADS . $image) ?>" alt="<?= h($food['name']) ?>"><?php else: ?><div class="combo-card-placeholder d-flex align-items-center justify-content-center"><i class="bi bi-cup-straw fs-2"></i></div><?php endif; ?>
                                <div class="card-body d-flex flex-column"><h4 class="h6 text-dark mb-1"><?= h($food['name']) ?><?= !empty($product['size']) ? ' · ' . h($product['size']) : '' ?></h4><p class="small text-secondary mb-3"><?= h($food['description'] ?: 'Đang cập nhật mô tả.') ?></p><div class="mt-auto d-flex justify-content-between align-items-center gap-2"><strong class="text-danger"><?= number_format((float) $product['price'], 0, ',', '.') ?> VNĐ</strong><div class="combo-quantity d-flex align-items-stretch"><button type="button" class="quantity-change" data-variant-id="<?= $id ?>" data-delta="-1" aria-label="Giảm số lượng">−</button><output id="quantity-<?= $id ?>"><?= (int) $quantities[$id] ?></output><input type="hidden" class="food-quantity" id="food-<?= $id ?>" name="food_quantities[<?= $id ?>]" value="<?= (int) $quantities[$id] ?>" data-price="<?= h((string) $product['price']) ?>" data-name="<?= h($food['name']) ?>" data-size="<?= h((string) ($product['size'] ?? '')) ?>"><button type="button" class="quantity-change" data-variant-id="<?= $id ?>" data-delta="1" data-max="<?= (int) $product['stock'] ?>" aria-label="Tăng số lượng">+</button></div></div></div>
                            </article></div>
                        <?php endforeach; ?>
                    </div><?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </section>
        </main>

        <aside class="col-lg-4"><div class="card order-panel p-3 p-md-4">
            <h2 class="h5 text-dark border-bottom pb-3 mb-3">THÔNG TIN ĐƠN HÀNG</h2>
            <div class="d-flex gap-3 mb-3"><?php if ($poster !== ''): ?><img class="order-poster" src="<?= h(str_starts_with($poster, 'http') ? $poster : BASE_ASSETS_UPLOADS . $poster) ?>" alt="<?= h($movie['title']) ?>"><?php else: ?><div class="order-poster d-flex align-items-center justify-content-center text-secondary"><i class="bi bi-film fs-2"></i></div><?php endif; ?><div class="small"><h3 class="h6 text-dark mb-2"><?= h($movie['title']) ?></h3><p class="mb-1"><span class="text-secondary">Định dạng:</span> <?= h($selectedShowtime['room_type_name'] ?? '-') ?></p><p class="mb-1"><span class="text-secondary">Thể loại:</span> <?= h($movie['genres']) ?></p><p class="mb-0"><span class="text-secondary">Thời lượng:</span> <?= h($movie['duration']) ?> phút</p></div></div>
            <div class="small border-top pt-3 mb-3"><p class="mb-1"><span class="text-secondary">Rạp chiếu:</span> PETACINEMA</p><p class="mb-1"><span class="text-secondary">Ngày chiếu:</span> <?= date('d/m/Y', strtotime($selectedDate)) ?></p><p class="mb-1"><span class="text-secondary">Giờ chiếu:</span> <?= date('H:i', strtotime($selectedShowtime['start_time'])) ?></p><p class="mb-0"><span class="text-secondary">Ghế:</span> <?= h(implode(', ', array_column($selectedSeatsForCombo, 'seat_number'))) ?></p></div>
            <h3 class="h6 text-dark border-top pt-3 mb-2">CHI TIẾT GIÁ</h3><div class="small" id="price-breakdown">
                <?php foreach ($ticketLines as $line): ?><div class="price-row d-flex justify-content-between gap-2"><div><strong class="d-block text-dark"><?= h($line['name']) ?> · <?= h($line['seats']) ?></strong><span><?= $line['quantity'] ?> × <?= number_format($line['unit_price'], 0, ',', '.') ?> VNĐ</span></div><strong class="text-nowrap"><?= number_format($line['total'], 0, ',', '.') ?> VNĐ</strong></div><?php endforeach; ?>
                <div id="selected-combo-lines"></div></div>
            <div class="d-flex justify-content-between small mt-3"><span>Tiền vé</span><strong><?= number_format($ticketTotalForCombo, 0, ',', '.') ?> VNĐ</strong></div><div class="d-flex justify-content-between small mt-2"><span>Tiền Combo</span><strong id="food-total"><?= number_format($foodTotal, 0, ',', '.') ?> VNĐ</strong></div><div class="grand-total d-flex justify-content-between mt-3 px-3 py-3 fw-bold"><span>TỔNG THANH TOÁN</span><span id="grand-total"><?= number_format($grandTotal, 0, ',', '.') ?> VNĐ</span></div>
            <div class="d-grid gap-2 mt-3"><a href="<?= BASE_URL ?>?action=booking_date&amp;movie_id=<?= (int) $movie['id'] ?>&amp;date=<?= h($selectedDate) ?>&amp;showtime_id=<?= (int) $selectedShowtimeId ?>" class="btn btn-outline-peta">Quay lại chọn ghế</a><button type="button" class="btn btn-peta">Tiếp tục thanh toán <i class="bi bi-arrow-right ms-1"></i></button></div>
        </div></aside>
    </div>
</form>

<script>
    const formatMoney = (amount) => new Intl.NumberFormat('vi-VN').format(amount) + ' VNĐ';
    const ticketTotal = <?= json_encode($ticketTotalForCombo) ?>;
    const escapeHtml = (value) => String(value).replace(/[&<>'"]/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[char]));
    const updateComboTotal = () => {
        let foodTotal = 0;
        const comboLines = document.getElementById('selected-combo-lines');
        const lines = [];
        document.querySelectorAll('.food-quantity').forEach((input) => {
            const quantity = Number(input.value);
            const price = Number(input.dataset.price);
            foodTotal += quantity * price;
            if (quantity > 0) {
                const name = `${input.dataset.name}${input.dataset.size ? ' · ' + input.dataset.size : ''}`;
                lines.push(`<div class="price-row d-flex justify-content-between gap-2"><div><strong class="d-block text-dark">${escapeHtml(name)} × ${quantity}</strong><span>${quantity} × ${formatMoney(price)}</span></div><strong class="text-nowrap">${formatMoney(quantity * price)}</strong></div>`);
            }
        });
        comboLines.innerHTML = lines.join('');
        document.getElementById('food-total').textContent = formatMoney(foodTotal);
        document.getElementById('grand-total').textContent = formatMoney(ticketTotal + foodTotal);
    };
    document.querySelectorAll('.quantity-change').forEach((button) => button.addEventListener('click', () => {
        const id = button.dataset.variantId;
        const input = document.getElementById(`food-${id}`);
        const output = document.getElementById(`quantity-${id}`);
        const max = Number(button.dataset.max || input.closest('.combo-quantity').querySelector('[data-max]')?.dataset.max || 999999);
        const next = Math.max(0, Math.min(max, Number(input.value) + Number(button.dataset.delta)));
        input.value = next;
        output.textContent = next;
        updateComboTotal();
    }));
    document.querySelectorAll('[data-combo-tab]').forEach((tab) => tab.addEventListener('click', () => {
        const group = tab.dataset.comboTab;
        document.querySelectorAll('[data-combo-tab]').forEach((item) => {
            const isActive = item === tab;
            item.classList.toggle('active', isActive);
            item.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });
        document.querySelectorAll('[data-combo-panel]').forEach((panel) => {
            panel.hidden = panel.dataset.comboPanel !== group;
        });
    }));
    updateComboTotal();
</script>
