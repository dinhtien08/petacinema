<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Thanh toán</h4>
        <p class="text-muted mb-0">Quản lý giao dịch thanh toán</p>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">

        <form action="<?= BASE_URL ?>?action=payment_list" method="GET" class="row g-2 align-items-end">

            <input type="hidden" name="action" value="payment_list">

            <div class="col-md-4">
                <label class="form-label">Tìm theo mã giao dịch</label>
                <input
                    type="text"
                    name="keyword"
                    class="form-control"
                    placeholder="Nhập mã giao dịch..."
                    value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                    <option value="completed" <?= ($_GET['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Hoàn tất</option>
                    <option value="failed" <?= ($_GET['status'] ?? '') === 'failed' ? 'selected' : '' ?>>Thất bại</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Phương thức</label>
                <select name="method" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="vnpay" <?= ($_GET['method'] ?? '') === 'vnpay' ? 'selected' : '' ?>>VNPay</option>
                    <option value="momo" <?= ($_GET['method'] ?? '') === 'momo' ? 'selected' : '' ?>>Momo</option>
                    <option value="visa" <?= ($_GET['method'] ?? '') === 'visa' ? 'selected' : '' ?>>Visa</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-danger flex-fill">
                    <i class="bi bi-search me-1"></i>Lọc
                </button>
                <a href="<?= BASE_URL ?>?action=payment_list" class="btn btn-outline-secondary" title="Bỏ lọc">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>

        </form>

    </div>
</div>

<div class="card">
    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="ps-4">STT</th>
                        <th>Transaction Code</th>
                        <th>Payment Method</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Payment Time</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($payments)) : ?>
                        <?php $stt = 1; ?>
                        <?php foreach ($payments as $payment) : ?>

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

                            <tr>
                                <td class="ps-4"><?= $stt++ ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($payment['transaction_code'] ?? '') ?></td>
                                <td><?= htmlspecialchars($payment['payment_method'] ?? '') ?></td>
                                <td><?= number_format((float) $payment['amount'], 0, ',', '.') ?> VNĐ</td>
                                <td>
                                    <span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                                </td>
                                <td>
                                    <?= !empty($payment['payment_time']) ? date('d/m/Y H:i', strtotime($payment['payment_time'])) : '—' ?>
                                </td>
                                <td class="text-end pe-4">
                                    <a
                                        href="<?= BASE_URL ?>?action=payment_detail&id=<?= $payment['id'] ?>"
                                        class="btn btn-sm btn-outline-info"
                                        title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                Không tìm thấy giao dịch nào.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>
</div>
