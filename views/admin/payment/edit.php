<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Cập nhật thanh toán</h4>
        <p class="text-muted mb-0">Mã giao dịch: <?= htmlspecialchars($payment['transaction_code'] ?? '') ?></p>
    </div>

    <a href="<?= BASE_URL ?>?action=payment_list" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>
        Quay lại
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        <form action="<?= BASE_URL ?>?action=payment_update" method="POST">

            <input type="hidden" name="id" value="<?= $payment['id'] ?>">

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Transaction Code</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($payment['transaction_code'] ?? '') ?>" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Payment Method</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($payment['payment_method'] ?? '') ?>" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Amount</label>
                    <input type="text" class="form-control" value="<?= number_format((float) $payment['amount'], 0, ',', '.') ?> VNĐ" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Payment Time</label>
                    <input
                        type="text"
                        class="form-control"
                        value="<?= !empty($payment['payment_time']) ? date('d/m/Y H:i:s', strtotime($payment['payment_time'])) : 'Chưa thanh toán' ?>"
                        readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select">
                        <option value="pending" <?= $payment['status'] === 'pending' ? 'selected' : '' ?>>Chờ xử lý (pending)</option>
                        <option value="completed" <?= $payment['status'] === 'completed' ? 'selected' : '' ?>>Hoàn tất (completed)</option>
                        <option value="failed" <?= $payment['status'] === 'failed' ? 'selected' : '' ?>>Thất bại (failed)</option>
                    </select>
                </div>

            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-floppy me-1"></i>
                    Cập nhật
                </button>

                <a href="<?= BASE_URL ?>?action=payment_list" class="btn btn-outline-secondary ms-2">
                    Hủy
                </a>
            </div>

        </form>

    </div>
</div>
