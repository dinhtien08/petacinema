<?php
// Khi deploy public HTTPS, cấu hình IPN URL của VNPAY trỏ vào file này.
$_GET['action'] = 'vnpay_ipn';
require __DIR__ . '/index.php';
