<?php

class AuthMiddleware
{
    public static function handle($action)
    {
        // Danh sách các route công khai không yêu cầu đăng nhập
        $publicActions = [
            '/', 
            'login', 
            'register', 
            'loginPost', 
            'registerStore',
            'now_showing',
            'upcoming',
            'news',
            'promotions',
            'movie_show'
        ];

        if (!in_array($action, $publicActions)) {
            if (!isset($_SESSION['user']) || !isset($_SESSION['user_id'])) {
                set_flash('danger', 'Vui lòng đăng nhập để sử dụng chức năng này.');
                header("Location: " . BASE_URL . "?action=login");
                exit;
            }
        }
    }
}
