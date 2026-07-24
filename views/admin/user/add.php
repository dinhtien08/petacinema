<div class="mb-4">
    <a href="<?= BASE_URL ?>?action=users_list" class="text-decoration-none text-muted">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
    </a>
    <h4 class="fw-bold mt-2 mb-1">Thêm người dùng</h4>
    <p class="text-muted mb-0">Tạo tài khoản người dùng mới</p>
</div>

<div class="card">
    <div class="card-body p-4">
        <form action="<?= BASE_URL ?>?action=users_addUser" method="POST">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Họ và tên</label>
                    <input type="text" name="fullname" class="form-control" placeholder="Nhập họ và tên" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Nhập email" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Vai trò</label>
                    <select name="role" class="form-select" required>
                        <option value="user">Người dùng</option>
                        <option value="staff">Nhân viên</option>
                        <option value="admin">Quản trị viên</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Trạng thái</label>
                    <select name="status" class="form-select" required>
                        <option value="active">Hoạt động</option>
                        <option value="inactive">Không hoạt động</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-check-lg me-1"></i> Lưu
                </button>
                <a href="<?= BASE_URL ?>?action=users_list" class="btn btn-light border">Hủy</a>
            </div>

        </form>
    </div>
</div>
