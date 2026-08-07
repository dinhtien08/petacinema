<?php

class AuthMiddleware
{
    public static function handle($action)
    {
        // Danh sách các route công khai không yêu cầu đăng nhập
        $publicActions = ['/', 'login', 'register', 'loginPost', 'registerStore', 'movie_detail', 'booking_date'];
        $isSelectingShowtime = $action === 'booking_date'
            && (int) ($_REQUEST['showtime_id'] ?? 0) > 0;

        if (!in_array($action, $publicActions) || $isSelectingShowtime) {
            if (!isset($_SESSION['user']) || !isset($_SESSION['user_id'])) {
                if ($isSelectingShowtime) {
                    $bookingParams = [
                        'action' => 'booking_date',
                        'movie_id' => (int) ($_REQUEST['movie_id'] ?? 0),
                        'date' => trim((string) ($_REQUEST['date'] ?? '')),
                        'showtime_id' => (int) ($_REQUEST['showtime_id'] ?? 0),
                    ];
                    $_SESSION['booking_return_url'] = BASE_URL . '?' . http_build_query($bookingParams);
                    set_flash('error', 'Vui lòng đăng nhập hoặc đăng ký để tiếp tục đặt vé.');
                }
                header("Location: " . BASE_URL . "?action=login");
                exit;
            }
        }
    }
}
