<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add User - Admin</title>
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
                <h4 class="fw-bold mt-2 mb-1">Add User</h4>
                <p class="text-muted mb-0">Create a new user account</p>
            </div>

            <div class="card">
                <div class="card-body p-4">
                    <form action="<?= BASE_URL ?>?action=users_addUser" method="POST">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Full Name</label>
                                <input type="text" name="fullname" class="form-control" placeholder="Enter full name" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Enter email" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="user">User</option>
                                    <option value="staff">Staff</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-check-lg me-1"></i> Save
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
