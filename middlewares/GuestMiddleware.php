<?php

class GuestMiddleware
{
    public static function handle($action)
    {
        $guestActions = ['login', 'register', 'loginPost', 'registerStore'];

        if (in_array($action, $guestActions)) {
            if (isset($_SESSION['user'])) {
                $role = $_SESSION['role'] ?? $_SESSION['user']['role'] ?? 'user';

                if ($role === 'admin') {
                    header("Location: " . BASE_URL . "?action=dashboard");
                    exit;
                } elseif ($role === 'staff') {
                    header("Location: " . BASE_URL . "?action=staff_dashboard");
                    exit;
                } else {
                    header("Location: " . BASE_URL);
                    exit;
                }
            }
        }
    }
}
