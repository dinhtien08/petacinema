<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Phim</h4>
        <p class="text-muted mb-0">Quản lý danh sách phim</p>
    </div>

    <a href="<?= BASE_URL ?>?action=movie-create" class="btn btn-danger">
        <i class="bi bi-plus-lg me-1"></i>
        Thêm phim
    </a>
</div>

<div class="card">
    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Poster</th>
                        <th>Tên phim</th>
                        <th>Thể loại</th>
                        <th>Thời lượng</th>
                        <th>Ngày khởi chiếu</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($movies)) : ?>

                        <?php foreach ($movies as $movie) : ?>

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
                            ?>

                            <tr>

                                <td class="ps-4">
                                    <?= $movie['id'] ?>
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
                                    <span class="badge <?= $statusClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>

                                <td class="text-end pe-4">

                                    <a
                                        href="<?= BASE_URL ?>?action=movie-show&id=<?= $movie['id'] ?>"
                                        class="btn btn-sm btn-outline-info me-1"
                                        title="Chi tiết">

                                        <i class="bi bi-eye"></i>

                                    </a>

                                    <a
                                        href="<?= BASE_URL ?>?action=movie-edit&id=<?= $movie['id'] ?>"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        title="Sửa">

                                        <i class="bi bi-pencil"></i>

                                    </a>

                                    <a
                                        href="<?= BASE_URL ?>?action=movie-delete&id=<?= $movie['id'] ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Bạn có chắc muốn xóa phim này?')"
                                        title="Xóa">

                                        <i class="bi bi-trash"></i>

                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                Chưa có phim nào.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>
</div>