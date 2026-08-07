<?php

define('BASE_URL',          'http://localhost/petacinema/');

define('PATH_ROOT',         __DIR__ . '/../');

define('PATH_VIEW',         PATH_ROOT . 'views/');

define('PATH_VIEW_MAIN',    PATH_ROOT . 'views/main.php');
define('PATH_VIEW_ADMIN',   PATH_ROOT . 'views/admin/');
define('PATH_VIEW_STAFF',   PATH_ROOT . 'views/staff/');

define('BASE_ASSETS', BASE_URL . 'assets/');

define('BASE_ASSETS_UPLOADS',   BASE_URL . 'assets/uploads/');

define('PATH_ASSETS_UPLOADS',   PATH_ROOT . 'assets/uploads/');

define('PATH_CONTROLLER',       PATH_ROOT . 'controllers/');

define('PATH_MODEL',            PATH_ROOT . 'models/');

define('PATH_MIDDLEWARE',       PATH_ROOT . 'middlewares/');


define('DB_HOST',     'localhost');
define('DB_PORT',     '3306');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME',     'movie_booking');
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
define('SHOWTIME_CLEANING_TIME', 20);
date_default_timezone_set('Asia/Ho_Chi_Minh');

// VNPay Sandbox
define('VNPAY_TMN_CODE', 'VKA0CKXS');
define('VNPAY_HASH_SECRET', 'GVDIQPPOGWDJFEUGUQLECZCSEZSYCOIW');
define(
    'VNPAY_PAYMENT_URL',
    'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'
);
define(
    'VNPAY_RETURN_URL',
    BASE_URL . 'vnpay_return.php'
);

define('VNPAY_PAYMENT_TIMEOUT_MINUTES', 5);