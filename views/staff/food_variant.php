<div class="card p-4">

    <div class="d-flex align-items-center justify-content-between mb-3">

        <div>
            <h4 class="mb-1">
                Danh sách kích cỡ - <?= h($food['name']) ?>
            </h4>
            <p class="text-muted mb-0">
                Theo dõi các kích cỡ và giá bán của món ăn.
            </p>
        </div>

        <a href="?action=staff_food_list" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            Quay lại
        </a>

    </div>

    <?php require PATH_VIEW . 'staff/layout/flash.php'; ?>

    <div class="table-responsive">

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-light">

                <tr>

                    <th width="70">STT</th>

                    <th>Kích cỡ</th>

                    <th class="text-end">Giá bán</th>

                    <th class="text-center">Tồn kho</th>

                    <th class="text-center">Trạng thái</th>

                </tr>

            </thead>

            <tbody>

            <?php if (empty($variants)): ?>

                <tr>

                    <td colspan="5" class="text-center text-muted py-4">

                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                        Món ăn này chưa có kích cỡ nào.

                    </td>

                </tr>

            <?php else: ?>

                <?php $stt = 1; ?>

                <?php foreach ($variants as $variant): ?>

                    <tr>

                        <td><?= $stt++ ?></td>

                        <td>

                            <span class="fw-semibold">

                                <?= h($variant['size']) ?>

                            </span>

                        </td>

                        <td class="text-end">

                            <?= number_format((float)$variant['price']) ?>đ

                        </td>

                        <td class="text-center">

                            <?= (int)$variant['stock'] ?>

                        </td>

                        <td class="text-center">

                            <?php if ($variant['stock'] > 10): ?>

                                <span class="badge bg-success">
                                    Còn hàng
                                </span>

                            <?php elseif ($variant['stock'] > 0): ?>

                                <span class="badge bg-warning text-dark">
                                    Sắp hết
                                </span>

                            <?php else: ?>

                                <span class="badge bg-danger">
                                    Hết hàng
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>