<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Quản lý phòng chiếu</h4>
        <p class="text-muted mb-0">
            Danh sách phòng chiếu và cấu hình ghế trong hệ thống PETACINEMA.
        </p>
    </div>

    <a href="?action=roomAdd" class="btn btn-danger rounded-3 px-4">
        <i class="bi bi-plus-lg me-1"></i>
        Thêm phòng chiếu
    </a>
</div>

<div class="card">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="70">ID</th>
                        <th>Tên phòng</th>
                        <th>Loại phòng</th>
                        <th>Layout</th>
                        <th>Ghế hoạt động</th>
                        <th class="text-end" width="220">Thao tác</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (!empty($listRoom)): ?>

                    <?php foreach ($listRoom as $room): ?>

                        <?php
                        $totalSeats       = (int)$room['total_seats'];
                        $generatedSeats   = (int)$room['generated_seats'];
                        $availableSeats   = (int)$room['available_seats'];
                        $maintenanceSeats = (int)$room['maintenance_seats'];

                        if ($generatedSeats == 0) {
                            $badge = 'secondary';
                        } elseif ($maintenanceSeats > 0) {
                            $badge = 'warning';
                        } else {
                            $badge = 'success';
                        }
                        ?>

                        <tr>

                            <td>
                                #<?= $room['id'] ?>
                            </td>

                            <td class="fw-semibold">
                                <?= htmlspecialchars($room['name']) ?>
                            </td>

                            <td>
                                <span class="badge bg-info">
                                    <?= htmlspecialchars($room['room_type_name']) ?>
                                </span>
                            </td>

                            <td>
                                <?= $totalSeats ?> ghế
                            </td>

                            <td>

                                <span class="badge bg-<?= $badge ?>">
                                    <?= $availableSeats ?> / <?= $totalSeats ?>
                                </span>

                                <?php if ($maintenanceSeats > 0): ?>

                                    <div class="small text-danger mt-1">
                                        <i class="bi bi-tools"></i>
                                        <?= $maintenanceSeats ?> ghế bảo trì
                                    </div>

                                <?php endif; ?>

                            </td>

                            <td class="text-end">

                                <a href="?action=roomSeats&id=<?= $room['id'] ?>"
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-grid-3x3-gap"></i>
                                </a>

                                <a href="?action=roomEdit&id=<?= $room['id'] ?>"
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <a href="?action=roomDelete&id=<?= $room['id'] ?>"
                                   class="btn btn-outline-danger btn-sm"
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa phòng <?= htmlspecialchars($room['name'], ENT_QUOTES) ?>?');">
                                    <i class="bi bi-trash"></i>
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            Chưa có phòng chiếu nào.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>
        </div>
    </div>
</div>