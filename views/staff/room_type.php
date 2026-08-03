<div class="d-flex justify-content-between align-items-center mb-1">
    <div>
        <h4 class="mb-1 fw-bold">Quản lý loại phòng</h4>
        <p class="text-muted mb-0">Quản lý các loại phòng chiếu của rạp.</p>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên loại phòng</th>
                    <th>Phụ thu</th>
                    <th>Mô tả</th>
                    <th>Số phòng</th>
                </tr>
                </thead>
                <tbody>
                <?php 
                $stt=0;
                foreach ($listRoomType as $roomType ):
                $stt++;
                ?>
                    <tr>
                        <td><?= $stt ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($roomType['name']) ?></td>
                        <td><?= number_format((float)$roomType['price_modifier'], 0, ',', '.') ?> đ</td>
                        <td><?= htmlspecialchars($roomType['description'] ?? '') ?></td>
                        <td><?= (int)($roomType['total_rooms'] ?? 0) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($listRoomType)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Chưa có loại phòng.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>