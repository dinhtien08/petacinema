<?php

class RoleMiddleware
{
    public static function handle($action)
    {
        if (!isset($_SESSION['user'])) {
            return;
        }

        $role = $_SESSION['role'] ?? $_SESSION['user']['role'] ?? 'user';

        // 1. Phân quyền cho Staff (các route bắt đầu bằng staff_)
        if (strpos($action, 'staff_') === 0) {
            if ($role === 'user' || $role === 'client') {
                header("Location: " . BASE_URL);
                exit;
            }
        }

        // 2. Các route quản trị Admin
        $adminActions = [
            'dashboard', 
            'rooms', 'roomAdd', 'roomAddProcess', 'roomEdit', 'roomEditProcess', 'roomDelete', 'roomSeats', 'roomGenerateSeats', 'roomToggleSeat',
            'room-types', 'roomTypeAdd', 'roomTypeAddProcess', 'roomTypeEdit', 'roomTypeEditProcess', 'roomTypeDelete',
            'seat-types', 'seatTypeAdd', 'seatTypeAddProcess', 'seatTypeEdit', 'seatTypeEditProcess', 'seatTypeDelete',
            'movies', 'movie_list', 'movie_delete', 'movie_create', 'movie_store', 'movie_edit', 'movie_update', 'movie_show',
            'users', 'users_list', 'users_add', 'users_addUser', 'users_edit', 'users_editUser', 'users_delete',
            'food_list', 'food_add', 'food_addPost', 'food_edit', 'food_editPost', 'food_delete',
            'food_variant_list', 'food_variant_add', 'food_variant_addPost', 'food_variant_edit', 'food_variant_editPost', 'food_variant_delete',
            'food_order_list', 'food_order_add', 'food_order_addPost', 'food_order_edit', 'food_order_editPost', 'food_order_delete',
            'bookings', 'booking_list', 'booking_show', 'booking_detail', 'booking_add', 'booking_addPost', 'booking_edit', 'booking_editPost', 'booking_delete',
            'payment_list', 'payment_detail',
            'showtimes', 'showtime_show', 'showtime_create', 'showtime_store', 'showtime_edit', 'showtime_update', 'showtime_delete', 'showtimeSeats'
        ];

        if (in_array($action, $adminActions)) {
            if ($role === 'user' || $role === 'client') {
                header("Location: " . BASE_URL);
                exit;
            } elseif ($role === 'staff') {
                header("Location: " . BASE_URL . "?action=staff_dashboard");
                exit;
            }
        }
    }
}
