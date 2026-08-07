<?php

class VnpayService
{
    public static function isConfigured(): bool
    {
        return defined('VNPAY_TMN_CODE')
            && defined('VNPAY_HASH_SECRET')
            && VNPAY_TMN_CODE !== ''
            && VNPAY_HASH_SECRET !== ''
            && VNPAY_TMN_CODE !== 'YOUR_TMN_CODE'
            && VNPAY_HASH_SECRET !== 'YOUR_HASH_SECRET';
    }

    public static function createPaymentUrl(array $booking): string
    {
        if (!self::isConfigured()) {
            throw new RuntimeException('VNPAY Sandbox chưa được cấu hình TmnCode/HashSecret trong configs/env.php.');
        }

        $createdAt = new DateTimeImmutable('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
        $expireAt = $createdAt->modify('+' . (int) VNPAY_PAYMENT_TIMEOUT_MINUTES . ' minutes');

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => VNPAY_TMN_CODE,
            'vnp_Amount' => (string) ((int) round((float) $booking['total_amount'] * 100)),
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => $createdAt->format('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'vnp_Locale' => 'vn',
            'vnp_OrderInfo' => 'Thanh toan booking ' . $booking['booking_code'],
            'vnp_OrderType' => 'other',
            'vnp_ReturnUrl' => VNPAY_RETURN_URL,
            'vnp_TxnRef' => $booking['booking_code'],
            'vnp_ExpireDate' => $expireAt->format('YmdHis'),
        ];

        ksort($inputData);
        $hashData = self::buildHashData($inputData);
        $secureHash = hash_hmac('sha512', $hashData, VNPAY_HASH_SECRET);

        return VNPAY_PAYMENT_URL
            . '?'
            . http_build_query($inputData, '', '&', PHP_QUERY_RFC1738)
            . '&vnp_SecureHash=' . $secureHash;
    }

    public static function verifyReturn(array $data): bool
    {
        if (!self::isConfigured()) {
            return false;
        }

        $receivedHash = strtolower((string) ($data['vnp_SecureHash'] ?? ''));
        if ($receivedHash === '') {
            return false;
        }

        $inputData = [];
        foreach ($data as $key => $value) {
            if (!str_starts_with((string) $key, 'vnp_')) {
                continue;
            }
            if (in_array($key, ['vnp_SecureHash', 'vnp_SecureHashType'], true)) {
                continue;
            }
            $inputData[$key] = (string) $value;
        }

        ksort($inputData);
        $expectedHash = strtolower(hash_hmac('sha512', self::buildHashData($inputData), VNPAY_HASH_SECRET));

        return hash_equals($expectedHash, $receivedHash);
    }

    private static function buildHashData(array $data): string
    {
        $pairs = [];
        foreach ($data as $key => $value) {
            $pairs[] = urlencode((string) $key) . '=' . urlencode((string) $value);
        }
        return implode('&', $pairs);
    }
}
