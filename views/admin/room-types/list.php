<div class="d-flex justify-content-between align-items-center mb-1">
    <div>
        <h4 class="mb-1 fw-bold">Quản lý loại phòng</h4>
        <p class="text-muted mb-0">Quản lý các loại phòng chiếu của rạp.</p>
    </div>
    <a href="?action=roomTypeAdd" class="btn btn-danger rounded-3 px-4">
        <i class="bi bi-plus-lg me-1"></i> Thêm loại phòng
    </a>
</div>

<div class="card mt-4">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Tên loại phòng</th>
                    <th>Phụ thu</th>
                    <th>Mô tả</th>
                    <th>Số phòng</th>
                    <th class="text-end">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($listRoomType as $roomType): ?>
                    <tr>
                        <td><?= $roomType['id'] ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($roomType['name']) ?></td>
                        <td><?= number_format((float)$roomType['price_modifier'], 0, ',', '.') ?> đ</td>
                        <td><?= htmlspecialchars($roomType['description'] ?? '') ?></td>
                        <td><?= (int)($roomType['total_rooms'] ?? 0) ?></td>
                        <td class="text-end">
                            <a href="?action=roomTypeEdit&id=<?= $roomType['id'] ?>" class="btn btn-outline-primary btn-sm me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="?action=roomTypeDelete&id=<?= $roomType['id'] ?>" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($listRoomType)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Chưa có loại phòng.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
