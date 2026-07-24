<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Người dùng</h4>
        <p class="text-muted mb-0">Quản lý tài khoản hệ thống</p>
    </div>
    <a href="<?= BASE_URL ?>?action=users_add" class="btn btn-danger">
        <i class="bi bi-plus-lg me-1"></i> Thêm người dùng
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($listUser)) : ?>
                        <?php foreach ($listUser as $user) : ?>
                            <?php
                            $roleClass = match ($user['role']) {
                                'admin' => 'bg-danger',
                                'staff' => 'bg-primary',
                                default => 'bg-secondary',
                            };
                            $roleLabel = match ($user['role']) {
                                'admin' => 'Quản trị viên',
                                'staff' => 'Nhân viên',
                                default => 'Người dùng',
                            };
                            ?>
                            <tr>
                                <td class="ps-4"><?= $user['id'] ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($user['fullname']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <span class="badge <?= $roleClass ?>"><?= $roleLabel ?></span>
                                </td>
                                <td>
                                    <?php if ($user['status'] === 'active') : ?>
                                        <span class="badge bg-success">Hoạt động</span>
                                    <?php else : ?>
                                        <span class="badge bg-secondary">Không hoạt động</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $user['created_at'] ?></td>
                                <td class="text-end pe-4">
                                    <a href="<?= BASE_URL ?>?action=users_edit&id=<?= $user['id'] ?>"
                                        class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>?action=users_delete&id=<?= $user['id'] ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Bạn có chắc muốn xóa người dùng này?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">Chưa có người dùng nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
