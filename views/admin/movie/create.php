<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Thêm phim</h4>
        <p class="text-muted mb-0">Nhập thông tin phim mới</p>
    </div>

    <a href="<?= BASE_URL ?>?action=movie_list" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>
        Quay lại
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        <form action="<?= BASE_URL ?>?action=movie_store"
              method="POST"
              enctype="multipart/form-data">

            <?php require '_form.php'?>

            <div class="mt-4">
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-floppy me-1"></i>
                    Lưu phim
                </button>

                <a href="<?= BASE_URL ?>?action=movie_list"
                   class="btn btn-outline-secondary ms-2">
                    Hủy
                </a>
            </div>

        </form>

    </div>
</div>
<script>
    document.querySelector('input[name="poster"]').addEventListener('change', function () {
    const file = this.files[0];

    if (file) {
        document.getElementById('posterPreview').src = URL.createObjectURL(file);
    }
});
</script>