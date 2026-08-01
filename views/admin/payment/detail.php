<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Chi tiết thanh toán</h4>
        <p class="text-muted mb-0">Mã giao dịch: <?= htmlspecialchars($payment['transaction_code'] ?? '') ?></p>
    </div>

    <a href="<?= BASE_URL ?>?action=payment_list" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>
        Quay lại
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        <?php
        $statusClass = match ($payment['status']) {
            'completed' => 'bg-success',
            'pending'   => 'bg-warning text-dark',
            'failed'    => 'bg-danger',
            default     => 'bg-secondary',
        };

        $statusLabel = match ($payment['status']) {
            'completed' => 'Hoàn tất',
            'pending'   => 'Chờ xử lý',
            'failed'    => 'Thất bại',
            default     => 'Không xác định',
        };
        ?>

        <table class="table table-borderless mb-0">
            <tbody>
                <tr>
                    <th style="width: 220px;">ID</th>
                    <td><?= $payment['id'] ?></td>
                </tr>
                <tr>
                    <th>Transaction Code</th>
                    <td class="fw-semibold"><?= htmlspecialchars($payment['transaction_code'] ?? '') ?></td>
                </tr>
                <tr>
                    <th>Payment Method</th>
                    <td><?= htmlspecialchars($payment['payment_method'] ?? '') ?></td>
                </tr>
                <tr>
                    <th>Amount</th>
                    <td><?= number_format((float) $payment['amount'], 0, ',', '.') ?> VNĐ</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                    </td>
                </tr>
                <tr>
                    <th>Payment Time</th>
                    <td>
                        <?= !empty($payment['payment_time']) ? date('d/m/Y H:i:s', strtotime($payment['payment_time'])) : 'Chưa thanh toán' ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

