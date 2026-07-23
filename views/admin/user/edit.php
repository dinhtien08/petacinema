<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit User - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_ASSETS ?>css/admin.css">
</head>

<body>

    <?php require PATH_VIEW . 'admin/layout/sidebar.php'; ?>

    <div class="main-wrapper">

        <?php require PATH_VIEW . 'admin/layout/header.php'; ?>

        <main class="content">

            <div class="mb-4">
                <a href="<?= BASE_URL ?>?action=users_list" class="text-decoration-none text-muted">
                    <i class="bi bi-arrow-left me-1"></i> Back to Users
                </a>
                <h4 class="fw-bold mt-2 mb-1">Edit User</h4>
                <p class="text-muted mb-0">Update user information</p>
            </div>

            <div class="card">
                <div class="card-body p-4">
                    <form action="<?= BASE_URL ?>?action=users_editUser" method="POST">
                        <input type="hidden" name="id" value="<?= $edit['id'] ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Full Name</label>
                                <input type="text" name="fullname" class="form-control"
                                    value="<?= htmlspecialchars($edit['fullname']) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="<?= htmlspecialchars($edit['email']) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control"
                                    value="<?= htmlspecialchars($edit['password']) ?>" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="user" <?= $edit['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                    <option value="staff" <?= $edit['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                                    <option value="admin" <?= $edit['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="active" <?= $edit['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= $edit['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-check-lg me-1"></i> Update
                            </button>
                            <a href="<?= BASE_URL ?>?action=users_list" class="btn btn-light border">Cancel</a>
                        </div>

                    </form>
                </div>
            </div>

        </main>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
