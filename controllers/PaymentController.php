<?php
class PaymentController
{
    public function payment_list()
    {
        $paymentModel = new PaymentModel();

        $keyword = trim($_GET['keyword'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $method = trim($_GET['method'] ?? '');

        $payments = $paymentModel->searchFilter($keyword, $status, $method);

        $view = 'admin/payment/list';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    public function payment_detail()
    {
        $id = (int) ($_GET['id'] ?? 0);

        $paymentModel = new PaymentModel();
        $payment = $paymentModel->getById($id);

        if (!$payment) {
            header('Location: ' . BASE_URL . '?action=payment_list');
            exit;
        }

        $view = 'admin/payment/detail';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    /**
     * URL browser quay về sau khi khách hoàn tất/hủy thanh toán tại VNPAY.
     */
    public function vnpayReturn()
    {
        $bookingCode = trim((string) ($_GET['vnp_TxnRef'] ?? ''));
        $isValidSignature = VnpayService::verifyReturn($_GET);
        $bookingModel = new BookingModel();

        $success = false;
        $message = 'Không thể xác minh kết quả thanh toán.';

        if ($bookingCode === '') {
            $message = 'Thiếu mã booking từ VNPAY.';
        } elseif (!$isValidSignature) {
            $message = 'Chữ ký VNPAY không hợp lệ. Dữ liệu thanh toán không được cập nhật.';
        } else {
            $responseCode = (string) ($_GET['vnp_ResponseCode'] ?? '');
            $transactionStatus = (string) ($_GET['vnp_TransactionStatus'] ?? '');
            $returnedAmount = ((float) ($_GET['vnp_Amount'] ?? 0)) / 100;
            $transactionCode = trim((string) ($_GET['vnp_TransactionNo'] ?? $_GET['vnp_BankTranNo'] ?? ''));
            $payDate = trim((string) ($_GET['vnp_PayDate'] ?? '')) ?: null;

            if ($responseCode === '00' && $transactionStatus === '00') {
                $result = $bookingModel->completeVnpayPayment(
                    $bookingCode,
                    $returnedAmount,
                    $transactionCode,
                    $payDate
                );
                $success = (bool) ($result['success'] ?? false);
                $message = (string) ($result['message'] ?? 'Không thể cập nhật thanh toán.');

                if (!$success) {
                    // Nếu giao dịch thành công ở VNPAY nhưng booking đã quá hạn thì không giữ pending mãi.
                    $bookingModel->cancelPendingCheckout($bookingCode, $transactionCode, $payDate);
                }
            } else {
                $bookingModel->cancelPendingCheckout($bookingCode, $transactionCode, $payDate);
                $message = 'Thanh toán chưa thành công hoặc đã bị hủy. Ghế đã được giải phóng.';
            }
        }

        $booking = $bookingCode !== ''
            ? $bookingModel->getCheckoutByBookingCode($bookingCode)
            : null;

        $retryUrl = BASE_URL;
        if ($booking) {
            $retryUrl = BASE_URL . '?' . http_build_query([
                'action' => 'booking_date',
                'movie_id' => (int) $booking['movie_id'],
                'date' => date('Y-m-d', strtotime((string) $booking['start_time'])),
                'showtime_id' => (int) $booking['showtime_id'],
                'selected_seats' => str_replace(' ', '', (string) ($booking['seat_numbers'] ?? '')),
                'payment_retry' => 1,
            ]);
        }

        if ($success) {
            unset($_SESSION['active_payment_booking_code']);
        }

        $title = $success ? 'Thanh toán thành công | Petacinema' : 'Kết quả thanh toán | Petacinema';
        $view = 'payment_result';
        require_once PATH_VIEW . 'main.php';
    }

    /**
     * IPN dùng khi deploy URL public HTTPS. localhost thường không nhận được callback server-to-server.
     */
    public function vnpayIpn()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!VnpayService::verifyReturn($_GET)) {
            echo json_encode(['RspCode' => '97', 'Message' => 'Invalid Checksum'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $bookingCode = trim((string) ($_GET['vnp_TxnRef'] ?? ''));
        $bookingModel = new BookingModel();
        $booking = $bookingModel->getCheckoutByBookingCode($bookingCode);

        if (!$booking) {
            echo json_encode(['RspCode' => '01', 'Message' => 'Order not found'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $returnedAmount = ((float) ($_GET['vnp_Amount'] ?? 0)) / 100;
        if (abs((float) $booking['total_amount'] - $returnedAmount) > 0.01) {
            echo json_encode(['RspCode' => '04', 'Message' => 'Invalid amount'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $responseCode = (string) ($_GET['vnp_ResponseCode'] ?? '');
        $transactionStatus = (string) ($_GET['vnp_TransactionStatus'] ?? '');
        $transactionCode = trim((string) ($_GET['vnp_TransactionNo'] ?? $_GET['vnp_BankTranNo'] ?? ''));
        $payDate = trim((string) ($_GET['vnp_PayDate'] ?? '')) ?: null;

        if ($responseCode === '00' && $transactionStatus === '00') {
            $result = $bookingModel->completeVnpayPayment(
                $bookingCode,
                $returnedAmount,
                $transactionCode,
                $payDate
            );

            echo json_encode(
                $result['success']
                    ? ['RspCode' => '00', 'Message' => 'Confirm Success']
                    : ['RspCode' => '99', 'Message' => $result['message']],
                JSON_UNESCAPED_UNICODE
            );
            return;
        }

        $bookingModel->cancelPendingCheckout($bookingCode, $transactionCode, $payDate);
        echo json_encode(['RspCode' => '00', 'Message' => 'Confirm Success'], JSON_UNESCAPED_UNICODE);
    }
}
