<?php
$currentAction = $_GET['action'] ?? '';
?>
<aside class="sidebar">

    <!-- Menu -->
    <div class="sidebar-menu">

        <small class="menu-title">GENERAL</small>

        <a href="?action=dashboard" class="<?= ($currentAction === 'dashboard' || $currentAction === '') ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i>
            Dashboard
        </a>

        <small class="menu-title">MOVIE</small>

        <a href="?action=movies" class="<?= (strpos($currentAction, 'movies') === 0) ? 'active' : '' ?>">
            <i class="bi bi-film"></i>
            Movies
        </a>

        <a href="?action=showtimes" class="<?= (strpos($currentAction, 'showtimes') === 0) ? 'active' : '' ?>">
            <i class="bi bi-calendar-event"></i>
            Showtimes
        </a>

        <small class="menu-title">CINEMA</small>

        <a href="?action=rooms" class="<?= (strpos($currentAction, 'rooms') === 0) ? 'active' : '' ?>">
            <i class="bi bi-door-open"></i>
            Rooms
        </a>

        <a href="?action=room-types" class="<?= (strpos($currentAction, 'room-types') === 0) ? 'active' : '' ?>">
            <i class="bi bi-building"></i>
            Room Types
        </a>

        <a href="?action=seat-types" class="<?= (strpos($currentAction, 'seat-types') === 0) ? 'active' : '' ?>">
            <i class="bi bi-grid-3x3-gap"></i>
            Seat Types
        </a>

        <small class="menu-title">BOOKING</small>

        <a href="?action=bookings" class="<?= (strpos($currentAction, 'bookings') === 0) ? 'active' : '' ?>">
            <i class="bi bi-ticket-perforated"></i>
            Bookings
        </a>

        <a href="?action=payments" class="<?= (strpos($currentAction, 'payments') === 0) ? 'active' : '' ?>">
            <i class="bi bi-credit-card"></i>
            Payments
        </a>

        <small class="menu-title">SERVICE</small>

        <a href="?action=food_list" class="<?= (strpos($currentAction, 'food') === 0) ? 'active' : '' ?>">
            <i class="bi bi-cup-hot"></i>
            Foods
        </a>

        <small class="menu-title">SYSTEM</small>

        <a href="?action=users_list" class="<?= (strpos($currentAction, 'users') === 0) ? 'active' : '' ?>">
            <i class="bi bi-people"></i>
            Users
        </a>

    </div>

    <!-- Logout -->
    <div class="sidebar-footer">

        <a href="?action=logout" class="logout-link">

            <i class="bi bi-box-arrow-right"></i>

            Logout

        </a>

    </div>

</aside>