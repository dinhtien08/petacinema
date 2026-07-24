<div class="mb-4">
    <a href="<?= BASE_URL ?>?action=users_list" class="text-decoration-none text-muted">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
    </a>
    <h4 class="fw-bold mt-2 mb-1">Sửa người dùng</h4>
    <p class="text-muted mb-0">Cập nhật thông tin người dùng</p>
</div>

<div class="card">
    <div class="card-body p-4">
        <form action="<?= BASE_URL ?>?action=users_editUser" method="POST">
            <input type="hidden" name="id" value="<?= $edit['id'] ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Họ và tên</label>
                    <input type="text" name="fullname" class="form-control"
                        value="<?= htmlspecialchars($edit['fullname']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control"
                        value="<?= htmlspecialchars($edit['email']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Mật khẩu</label>
                    <input type="password" name="password" class="form-control"
                        value="<?= htmlspecialchars($edit['password']) ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Vai trò</label>
                    <select name="role" class="form-select" required>
                        <option value="user" <?= $edit['role'] === 'user' ? 'selected' : '' ?>>Người dùng</option>
                        <option value="staff" <?= $edit['role'] === 'staff' ? 'selected' : '' ?>>Nhân viên</option>
                        <option value="admin" <?= $edit['role'] === 'admin' ? 'selected' : '' ?>>Quản trị viên</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Trạng thái</label>
                    <select name="status" class="form-select" required>
                        <option value="active" <?= $edit['status'] === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="inactive" <?= $edit['status'] === 'inactive' ? 'selected' : '' ?>>Không hoạt động</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-check-lg me-1"></i> Cập nhật
                </button>
                <a href="<?= BASE_URL ?>?action=users_list" class="btn btn-light border">Hủy</a>
            </div>

        </form>
    </div>
</div>
