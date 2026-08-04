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