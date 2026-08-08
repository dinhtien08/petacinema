<div class="container-fluid px-4 py-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-qr-code-scan me-2 text-primary"></i> Check-in
            </h4>
            <p class="text-muted mb-0">
                Nhập hoặc quét mã Booking để tiếp tục.
            </p>
        </div>
        <a href="?action=staff_booking_list" class="btn btn-outline-secondary">
            <i class="bi bi-list-ul me-1"></i> Danh sách Booking
        </a>
    </div>

    <!-- Hiển thị thông báo nếu có -->
    <?php if (!empty($flash)): ?>
        <div class="row justify-content-center mb-3">
            <div class="col-lg-5 col-md-7">
                <div class="alert alert-<?= ($flash['type'] ?? 'error') === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show mb-0" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5 text-center">
                    <div class="scanner-icon-wrapper mb-3 text-primary" style="font-size: 3rem;">
                        <i class="bi bi-upc-scan animate-pulse"></i>
                    </div>

                    <h5 class="card-title fw-bold mb-2">Nhập mã Booking</h5>
                    <p class="text-muted small mb-4">
                        Đặt con trỏ vào ô nhập liệu bên dưới và quét mã bằng máy quét Barcode/QR (USB),
                        hoặc gõ trực tiếp mã Booking rồi nhấn Enter.
                    </p>

                    <!-- Scanner / Manual entry form -->
                    <form action="<?= BASE_URL ?>" method="GET" id="usb-scanner-form">
                        <input type="hidden" name="action" value="staff_booking_detail">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-keyboard text-muted"></i>
                            </span>
                            <input
                                type="text"
                                name="code"
                                id="scanner-input"
                                class="form-control border-start-0 border-end-0 fs-5"
                                placeholder="Mã Booking (ví dụ: PET2026...)"
                                autofocus
                                autocomplete="off"
                                required
                            >
                            <button class="btn btn-primary px-4" type="submit">
                                <i class="bi bi-arrow-right-short fs-4"></i>
                            </button>
                        </div>
                        <div class="form-text text-start text-muted mt-2">
                            <i class="bi bi-info-circle me-1"></i> Thiết bị quét tự động nhấn Enter sau khi nhận diện mã.
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles for Pulse Animation -->
<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: .6; transform: scale(1.05); }
    }
    .animate-pulse {
        animation: pulse 2s infinite ease-in-out;
        display: inline-block;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scannerInput = document.getElementById('scanner-input');

    // Autofocus input, keep focus unless clicking elsewhere meaningful
    if (scannerInput) {
        scannerInput.focus();
        document.addEventListener('click', function(e) {
            // Keep focus if clicking general background
            const isClickInsideInput = scannerInput.contains(e.target);
            const isClickInsideButton = e.target.closest('button') || e.target.closest('a');
            if (!isClickInsideInput && !isClickInsideButton) {
                scannerInput.focus();
            }
        });
    }
});
</script>