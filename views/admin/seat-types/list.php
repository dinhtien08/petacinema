<div class="d-flex justify-content-between align-items-center mb-1">
    <div>
        <h4 class="mb-1 fw-bold">Quản lý loại ghế</h4>
        <p class="text-muted mb-0">Quản lý các loại ghế trong phòng chiếu.</p>
    </div>
    <a href="?action=seatTypeAdd" class="btn btn-danger rounded-3 px-4">
        <i class="bi bi-plus-lg me-1"></i> Thêm loại ghế
    </a>
</div>

<div class="card mt-4">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên loại ghế</th>
                    <th>Phụ thu</th>
                    <th>Mô tả</th>
                    <th>Số ghế</th>
                    <th class="text-end">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                <?php 
                $Stt=0;
                foreach ($listSeatType as $seatType): 
                $Stt++;
                ?>
                    <tr>
                        <td><?= $Stt ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($seatType['name']) ?></td>
                        <td><?= number_format((float)$seatType['surcharge'], 0, ',', '.') ?> đ</td>
                        <td><?= htmlspecialchars($seatType['description'] ?? '') ?></td>
                        <td><?= (int)($seatType['total_seats'] ?? 0) ?></td>
                        <td class="text-end">
                            <a href="?action=seatTypeEdit&id=<?= $seatType['id'] ?>" class="btn btn-outline-primary btn-sm me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="?action=seatTypeDelete&id=<?= $seatType['id'] ?>" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($listSeatType)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Chưa có loại ghế.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
