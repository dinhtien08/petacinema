<?php

class AuthMiddleware
{
    public static function handle($action)
    {
        // Danh sách các route công khai không yêu cầu đăng nhập
        $publicActions = ['/', 'login', 'register', 'loginPost', 'registerStore'];

        if (!in_array($action, $publicActions)) {
            if (!isset($_SESSION['user']) || !isset($_SESSION['user_id'])) {
                header("Location: " . BASE_URL . "?action=login");
                exit;
            }
        }
    }
}
