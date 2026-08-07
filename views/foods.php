<?php
    $foodModel = new FoodModel();
    $variantModel = new FoodVariantModel();
    $foods = array_values(array_filter(
        $foodModel->getAll(),
        fn($food) => ($food['status'] ?? '') === 'active'
    ));
?>

<style>
    .food-card { border: 1px solid var(--peta-card-border); border-radius: 1rem; overflow: hidden; transition: transform .25s ease, box-shadow .25s ease; }
    .food-card:hover { transform: translateY(-5px); box-shadow: 0 14px 30px rgba(15, 23, 42, .13); }
    .food-image { height: 210px; width: 100%; object-fit: cover; background: #f1f5f9; }
    .food-image-placeholder { height: 210px; background: linear-gradient(135deg, #fff1f2, #f8fafc); color: var(--peta-accent); }
    .food-price { color: var(--peta-accent); font-size: 1.15rem; font-weight: 800; }
</style>

<nav aria-label="breadcrumb" class="mb-4 small"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-danger text-decoration-none">Trang chủ</a></li><li class="breadcrumb-item active">Ưu đãi &amp; Đồ ăn</li></ol></nav>

<section class="mb-5">
    <div class="text-center mb-4">
        <span class="badge bg-danger text-uppercase px-3 py-2 mb-2">Petacinema treats</span>
        <h1 class="h2 text-dark text-uppercase mb-2">Ưu đãi &amp; Đồ ăn</h1>
        <p class="text-secondary mb-0">Các combo, snack, nước uống và ưu đãi đang được Admin cập nhật.</p>
    </div>

    <?php if (empty($foods)): ?>
        <div class="alert alert-light border text-center py-5 text-secondary"><i class="bi bi-cup-straw fs-2 text-danger d-block mb-2"></i>Hiện chưa có sản phẩm hoặc ưu đãi đang áp dụng.</div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
            <?php foreach ($foods as $food): ?>
                <?php
                    $variants = $variantModel->getByFoodId((int) $food['id']);
                    $availableVariants = array_values(array_filter($variants, fn($variant) => (int) $variant['stock'] > 0));
                    $image = trim((string) ($food['image'] ?? ''));
                    $priceLabel = !empty($availableVariants) ? number_format(min(array_column($availableVariants, 'price')), 0, ',', '.') . ' VNĐ' : 'Liên hệ';
                ?>
                <div class="col">
                    <article class="card food-card h-100 bg-white">
                        <?php if ($image !== ''): ?>
                            <img class="food-image" src="<?= h(str_starts_with($image, 'http') ? $image : BASE_ASSETS_UPLOADS . $image) ?>" alt="<?= h($food['name']) ?>">
                        <?php else: ?>
                            <div class="food-image-placeholder d-flex align-items-center justify-content-center"><i class="bi bi-cup-straw fs-1"></i></div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between gap-2 align-items-start mb-2">
                                <h2 class="h5 text-dark mb-0"><?= h($food['name']) ?></h2>
                                <span class="badge bg-danger-subtle text-danger text-nowrap">Đang áp dụng</span>
                            </div>
                            <p class="text-secondary small mb-3"><?= h($food['description'] ?: 'Đang cập nhật mô tả.') ?></p>
                            <div class="mt-auto">
                                <div class="food-price mb-2">Từ <?= $priceLabel ?></div>
                                <p class="small text-secondary mb-0"><i class="bi bi-box-seam me-1 text-danger"></i><?= count($availableVariants) ?> lựa chọn đang có</p>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
