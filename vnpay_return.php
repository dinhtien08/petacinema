<?php
// Bridge endpoint để VNPAY có Return URL sạch, không phụ thuộc query ?action=...
$_GET['action'] = 'vnpay_return';
require __DIR__ . '/index.php';
