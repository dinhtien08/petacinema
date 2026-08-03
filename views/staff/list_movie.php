<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Phim</h4>
        <p class="text-muted mb-0">Quản lý danh sách phim</p>
    </div>
</div><?php
$keyword = $_GET['keyword'] ?? '';
$status = $_GET['status'] ?? '';
$genre = $_GET['genre'] ?? '';
$ageRating = $_GET['age_rating'] ?? '';
$sort = $_GET['sort'] ?? 'status';
$currentAction = $_GET['action'] ?? 'movie_list';
$hasFilter = !empty($keyword) || !empty($status) || !empty($genre) || !empty($ageRating) || (!empty($sort) && $sort !== 'status');

$currentPage = max(1, (int)($_GET['page'] ?? 1));
$perPage = isset($perPage) && (int)$perPage > 0 ? (int)$perPage : 0;
$sttCounter = $perPage > 0 ? (($currentPage - 1) * $perPage) + 1 : 1;
?>

<!-- Filter Panel -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3 align-items-end">
            <input type="hidden" name="action" value="<?= htmlspecialchars($currentAction) ?>">
            <?php if (!empty($keyword)) : ?>
                <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
            <?php endif; ?>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Trạng thái phim</label>
                <select name="status" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="now_showing" <?= $status === 'now_showing' ? 'selected' : '' ?>>Đang chiếu</option>
                    <option value="coming_soon" <?= $status === 'coming_soon' ? 'selected' : '' ?>>Sắp chiếu</option>
                    <option value="ended" <?= $status === 'ended' ? 'selected' : '' ?>>Ngừng chiếu</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Thể loại</label>
                <input type="text" name="genre" class="form-control" placeholder="Nhập thể loại..." value="<?= htmlspecialchars($genre) ?>">
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Độ tuổi</label>
                <select name="age_rating" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <?php
                    $ratings = ['P', 'K', 'T13', 'T16', 'T18', 'C'];
                    foreach ($ratings as $r) {
                        $selected = $ageRating === $r ? 'selected' : '';
                        echo "<option value=\"$r\" $selected>$r</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Sắp xếp theo</label>
                <select name="sort" class="form-select">
                    <option value="status" <?= $sort === 'status' ? 'selected' : '' ?>>Trạng thái ưu tiên</option>
                    <option value="release_newest" <?= $sort === 'release_newest' ? 'selected' : '' ?>>Ngày khởi chiếu mới nhất</option>
                    <option value="release_oldest" <?= $sort === 'release_oldest' ? 'selected' : '' ?>>Ngày khởi chiếu cũ nhất</option>
                    <option value="title_asc" <?= $sort === 'title_asc' ? 'selected' : '' ?>>Tên phim A–Z</option>
                    <option value="title_desc" <?= $sort === 'title_desc' ? 'selected' : '' ?>>Tên phim Z–A</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Lọc
                </button>
                <a href="<?= BASE_URL ?>?action=<?= htmlspecialchars($currentAction) ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Xóa bộ lọc
                </a>
            </div>
        </form>
    </div>
</div>

<?php if ($hasFilter) : ?>
    <div class="alert alert-info py-2 mb-3">
        <i class="bi bi-info-circle me-1"></i>
        Hiển thị <?= count($movies) ?> kết quả
        <?php if (!empty($keyword)) : ?>
            cho từ khóa "<strong><?= htmlspecialchars($keyword) ?></strong>"
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="ps-4">STT</th>
                        <th>Poster</th>
                        <th>Tên phim</th>
                        <th>Thể loại</th>
                        <th>Thời lượng</th>
                        <th>Ngày khởi chiếu</th>
                        <th>Độ tuổi</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($movies)) : ?>

                        <?php foreach ($movies as $index => $movie) : ?>

                            <?php
                            $statusClass = match ($movie['status']) {
                                'coming_soon' => 'bg-warning text-dark',
                                'now_showing' => 'bg-success',
                                'ended' => 'bg-secondary',
                                default => 'bg-dark',
                            };

                            $statusLabel = match ($movie['status']) {
                                'coming_soon' => 'Sắp chiếu',
                                'now_showing' => 'Đang chiếu',
                                'ended' => 'Ngừng chiếu',
                                default => 'Không xác định',
                            };

                            $rowStt = $perPage > 0 ? $sttCounter++ : $index + 1;
                            ?>

                            <tr>

                                <td class="ps-4">
                                    <?= $rowStt ?>
                                </td>

                                <td>
                                    <img
                                        src="<?= BASE_ASSETS_UPLOADS . $movie['poster'] ?>"
                                        alt="<?= htmlspecialchars($movie['title']) ?>"
                                        width="55"
                                        height="80"
                                        class="rounded shadow-sm"
                                        style="object-fit:cover;">
                                </td>

                                <td class="fw-semibold">
                                    <?= htmlspecialchars($movie['title']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($movie['genres']) ?>
                                </td>

                                <td>
                                    <?= $movie['duration'] ?> phút
                                </td>

                                <td>
                                    <?= date('d/m/Y', strtotime($movie['release_date'])) ?>
                                </td>
                                <td>
                                    <?php
                                    $ageClass = match ($movie['age_rating']) {
                                        'P'   => 'bg-success',
                                        'K'   => 'bg-info text-dark',
                                        'T13' => 'bg-warning text-dark',
                                        'T16' => 'bg-primary',
                                        'T18' => 'bg-danger',
                                        'C'   => 'bg-dark',
                                        default => 'bg-secondary',
                                    };
                                    ?>

                                    <span class="badge <?= $ageClass ?>">
                                        <?= $movie['age_rating'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $statusClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>


                                <td class="text-end pe-4 text-nowrap" style="width:60px;">
                                    <div class="d-inline-flex gap-1">
                                        <a href="<?= BASE_URL ?>?action=staff_movie_show&id=<?= $movie['id'] ?>" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                Không tìm thấy phim phù hợp.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>
</div>