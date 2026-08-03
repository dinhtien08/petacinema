<?php
$currentAction = $_GET['action'] ?? '';
?>
<aside class="sidebar">

    <!-- Menu -->
    <div class="sidebar-menu">

        <small class="menu-title">GENERAL</small>

        <a href="?action=staff_dashboard" class="<?= ($currentAction === 'staff_dashboard' || $currentAction === '') ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i>
            Dashboard
        </a>

        <small class="menu-title">MOVIE</small>

        <a href="?action=staff_movie_list" class="<?= (strpos($currentAction, 'staff_movie') === 0) ? 'active' : '' ?>">
            <i class="bi bi-film"></i>
            Movies
        </a>

        <a href="?action=staff_showtimes" class="<?= (strpos($currentAction, 'staff_showtimes') === 0) ? 'active' : '' ?>">
            <i class="bi bi-calendar-event"></i>
            Showtimes
        </a>

        <small class="menu-title">CINEMA</small>

        <a href="?action=staff_rooms" class="<?= (strpos($currentAction, 'staff_rooms') === 0) ? 'active' : '' ?>">
            <i class="bi bi-door-open"></i>
            Rooms
        </a>

        <a href="?action=staff_room-types" class="<?= (strpos($currentAction, 'staff_room-types') === 0) ? 'active' : '' ?>">
            <i class="bi bi-building"></i>
            Room Types
        </a>

        <a href="?action=staff_seat-types" class="<?= (strpos($currentAction, 'staff_seat-types') === 0) ? 'active' : '' ?>">
            <i class="bi bi-grid-3x3-gap"></i>
            Seat Types
        </a>

        <small class="menu-title">BOOKING</small>

        <a href="?action=staff_booking_list" class="<?= (strpos($currentAction, 'staff_booking') === 0) ? 'active' : '' ?>">
            <i class="bi bi-ticket-perforated"></i>
            Bookings
        </a>


        <small class="menu-title">PAYMENT</small>

        <a href="?action=staff_payment_list" class="<?= (strpos($currentAction, 'staff_payment') === 0) ? 'active' : '' ?>">
            <i class="bi bi-credit-card"></i>
            Payments
        </a>

        <small class="menu-title">SERVICE</small>

        <a href="?action=staff_food_list" class="<?= (strpos($currentAction, 'staff_food') === 0) ? 'active' : '' ?>">
            <i class="bi bi-cup-hot"></i>
            Foods
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