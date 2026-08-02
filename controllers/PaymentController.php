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
}


