<header class="header">

    <div class="container-fluid px-4 h-100">

        <div class="d-flex align-items-center justify-content-between h-100">

            <!-- Logo -->
            <div class="header-logo d-flex align-items-center">

                <a href="?action=staff_dashboard" class="text-decoration-none">

                    <img
                        src="<?= BASE_ASSETS_UPLOADS ?>logo/logo.png"
                        alt="PETACINEMA"
                        class="logo">

                </a>

            </div>

            <?php
            $currentAction = $_GET['action'] ?? '';
            $keyword = $_GET['keyword'] ?? '';

            $targetAction = '';
            $placeholder = '';
            $isSearchable = true;

            if ($currentAction === 'staff_movies' || strpos($currentAction, 'staff_movie') === 0) {
                $targetAction = $currentAction === 'staff_movies' ? 'staff_movies' : 'staff_movie_list';
                $placeholder = 'Search movie title, genre, director...';
            } elseif ($currentAction === 'staff_showtimes' || strpos($currentAction, 'staff_showtime') === 0) {
                $targetAction = 'staff_showtimes';
                $placeholder = 'Search movie...';
            } elseif ($currentAction === 'staff_bookings' || strpos($currentAction, 'staff_booking') === 0) {
                $targetAction = $currentAction === 'staff_bookings' ? 'staff_bookings' : 'staff_booking_list';
                $placeholder = 'Search booking code or customer...';
            } else {
                $isSearchable = false;
                $placeholder = 'Search disabled...';
            }
            ?>

            <!-- Search -->
            <form method="GET" action="" class="header-search mx-5">
                <input type="hidden" name="action" value="<?= htmlspecialchars($targetAction ?: $currentAction) ?>">
                <?php
                if ($isSearchable) {
                    foreach ($_GET as $key => $val) {
                        if (!in_array($key, ['action', 'keyword']) && is_string($val) && $val !== '') {
                            echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($val) . '">' . "\n";
                        }
                    }
                }
                ?>
                <div class="input-group">
                    <button class="input-group-text bg-white border-end-0 border" type="submit" <?= !$isSearchable ? 'disabled' : '' ?>>
                        <i class="bi bi-search"></i>
                    </button>
                    <input
                        type="text"
                        name="keyword"
                        class="form-control border-start-0"
                        placeholder="<?= htmlspecialchars($placeholder) ?>"
                        value="<?= htmlspecialchars($keyword) ?>"
                        <?= !$isSearchable ? 'disabled' : '' ?>>
                </div>
            </form>

            <!-- User -->
            <div class="header-user d-flex align-items-center">

                <!-- Notification -->
                <button class="btn btn-light border rounded-circle me-4">

                    <i class="bi bi-bell"></i>

                </button>

                <!-- User -->

                <div class="dropdown">

                    <a
                        href="#"
                        class="d-flex align-items-center text-decoration-none text-dark"
                        data-bs-toggle="dropdown">

                        <img
                            src="https://i.pravatar.cc/45"
                            class="rounded-circle border me-3"
                            width="45"
                            height="45">

                        <div class="text-start">

                            <!-- Sau này thay bằng PHP -->

                            <div class="fw-semibold">

                                <?= $_SESSION['user']['fullname'] ?? 'Staff Member' ?>

                            </div>

                            <small class="text-muted">

                                <?= ucfirst($_SESSION['user']['role'] ?? 'Staff') ?>

                            </small>

                        </div>

                        <i class="bi bi-chevron-down ms-3 text-secondary"></i>

                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow">

                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-person me-2"></i>
                                Profile
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-gear me-2"></i>
                                Settings
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item text-danger" href="?action=logout">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Logout
                            </a>
                        </li>

                    </ul>

                </div>

            </div>

        </div>

    </div>

</header>