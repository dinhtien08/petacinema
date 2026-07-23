<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Users - Admin</title>
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

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Users</h4>
                    <p class="text-muted mb-0">Manage system users</p>
                </div>
                <a href="<?= BASE_URL ?>?action=users_add" class="btn btn-danger">
                    <i class="bi bi-plus-lg me-1"></i> Add User
                </a>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($listUser)) : ?>
                                    <?php foreach ($listUser as $user) : ?>
                                        <tr>
                                            <td class="ps-4"><?= $user['id'] ?></td>
                                            <td class="fw-semibold"><?= htmlspecialchars($user['fullname']) ?></td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td>
                                                <?php
                                                $roleClass = match ($user['role']) {
                                                    'admin' => 'bg-danger',
                                                    'staff' => 'bg-primary',
                                                    default => 'bg-secondary',
                                                };
                                                ?>
                                                <span class="badge <?= $roleClass ?>"><?= ucfirst($user['role']) ?></span>
                                            </td>
                                            <td>
                                                <?php if ($user['status'] === 'active') : ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else : ?>
                                                    <span class="badge bg-secondary">Inactive</span>
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
                                                    onclick="return confirm('Are you sure you want to delete this user?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">No users found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
