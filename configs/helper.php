<?php

if (!function_exists('debug')) {
    function debug($data)
    {
        echo '<pre>';
        print_r($data);
        die;
    }
}

if (!function_exists('upload_file')) {
    function upload_file($folder, $file)
    {
        $targetFile = $folder . '/' . time() . '-' . $file["name"];

        if (move_uploaded_file($file["tmp_name"], PATH_ASSETS_UPLOADS . $targetFile)) {
            return $targetFile;
        }

        throw new Exception('Upload file không thành công!');
    }
}

if (!function_exists('set_flash')) {
    function set_flash($type, $message)
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
}

if (!function_exists('get_flash')) {
    function get_flash()
    {
        if (!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}

if (!function_exists('h')) {
    function h($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('old_value')) {
    function old_value($old, $key, $default = '')
    {
        return h($old[$key] ?? $default);
    }
}

if (!function_exists('set_errors')) {
    function set_errors($errors, $old = [])
    {
        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = $old;
    }
}

if (!function_exists('get_errors')) {
    function get_errors()
    {
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);
        return $errors;
    }
}

if (!function_exists('get_old')) {
    function get_old()
    {
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['old']);
        return $old;
    }
}

if (!function_exists('field_error')) {
    function field_error($errors, $key)
    {
        if (!empty($errors[$key])) {
            return '<div class="text-white mt-1 small" style="color: #ffffff !important;"><i class="bi bi-exclamation-circle-fill me-1"></i>' . h($errors[$key]) . '</div>';
        }
        return '';
    }
}
if (!function_exists('booking_checkin_deadline')) {
    /**
     * Thời điểm cuối được check-in: giờ bắt đầu suất chiếu + số phút gia hạn.
     */
    function booking_checkin_deadline(array $booking): ?int
    {
        $startTime = $booking['start_time'] ?? $booking['showtime_at'] ?? null;
        if (empty($startTime)) {
            return null;
        }

        $timestamp = strtotime((string) $startTime);
        if ($timestamp === false) {
            return null;
        }

        $graceMinutes = defined('CHECKIN_GRACE_MINUTES')
            ? max(0, (int) CHECKIN_GRACE_MINUTES)
            : 30;

        return $timestamp + ($graceMinutes * 60);
    }
}

if (!function_exists('booking_checkin_state')) {
    /**
     * Trạng thái hiển thị của check-in.
     * Không ghi "expired" xuống DB vì đây là trạng thái phụ thuộc thời gian.
     */
    function booking_checkin_state(array $booking): string
    {
        if (($booking['checkin_status'] ?? 'pending') === 'checked_in') {
            return 'checked_in';
        }

        $deadline = booking_checkin_deadline($booking);
        if ($deadline !== null && time() > $deadline) {
            return 'expired';
        }

        return 'pending';
    }
}

if (!function_exists('booking_food_delivery_expired')) {
    /**
     * Đồ ăn được phép giao đến hết giờ kết thúc suất chiếu.
     */
    function booking_food_delivery_expired(array $booking): bool
    {
        if (empty($booking['end_time'])) {
            return false;
        }

        $endTimestamp = strtotime((string) $booking['end_time']);
        return $endTimestamp !== false && time() > $endTimestamp;
    }
}
