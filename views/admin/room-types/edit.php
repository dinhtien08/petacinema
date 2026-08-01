<div class="mb-4">
    <h4 class="mb-1 fw-bold">Sửa loại phòng</h4>
    <p class="text-muted mb-0">Cập nhật thông tin loại phòng.</p>
</div>

<?php if (($_GET['error'] ?? '') === 'invalid_room_type'): ?>
    <div class="alert alert-danger">Vui lòng nhập đầy đủ và hợp lệ thông tin loại phòng.</div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="?action=roomTypeEditProcess&id=<?= $roomType['id'] ?>">
            <div class="mb-3">
                <label class="form-label fw-semibold">Tên loại phòng</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($roomType['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Phụ thu</label>
                <input type="number" name="price_modifier" class="form-control" min="0" step="0.01" value="<?= htmlspecialchars($roomType['price_modifier']) ?>" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Mô tả</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($roomType['description'] ?? '') ?></textarea>
            </div>
            <a href="?action=room-types" class="btn btn-light">Hủy</a>
            <button type="submit" class="btn btn-danger">Cập nhật</button>
        </form>
    </div>
</div>
