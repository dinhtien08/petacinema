<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-qr-code-scan me-2 text-primary"></i>QR Ticket Check-in
            </h4>
            <p class="text-muted mb-0">
                Quét mã QR trên vé hoặc mã Booking để kiểm tra vào cổng và in vé.
            </p>
        </div>
        <a href="?action=staff_booking_list" class="btn btn-outline-secondary">
            <i class="bi bi-list-ul me-1"></i> Danh sách Booking
        </a>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- Input & Instructions Section -->
        <div class="col-lg-6 col-md-8">
            <div class="card shadow border-0 mb-4">
                <div class="card-body p-4 text-center">
                    <div class="scanner-icon-wrapper mb-3 text-primary" style="font-size: 3rem;">
                        <i class="bi bi-upc-scan animate-pulse"></i>
                    </div>
                    
                    <h5 class="card-title fw-bold mb-3">Sử dụng máy quét Barcode/QR (USB)</h5>
                    <p class="text-muted small mb-4">
                        Đặt con trỏ vào ô nhập liệu dưới đây và quét mã. Hệ thống sẽ tự động xử lý khi hoàn tất.
                    </p>

                    <!-- Alert messages -->
                    <div id="alert-container" class="mb-3 d-none"></div>

                    <!-- USB Scanner Form -->
                    <form id="usb-scanner-form" class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-keyboard text-muted"></i>
                            </span>
                            <input 
                                type="text" 
                                id="scanner-input" 
                                class="form-control form-control-lg border-start-0 fs-5" 
                                placeholder="Nhấp vào đây và quét mã..." 
                                autofocus 
                                autocomplete="off"
                            >
                            <button class="btn btn-primary px-4" type="submit">
                                <i class="bi bi-arrow-right-short fs-4"></i>
                            </button>
                        </div>
                        <div class="form-text text-start text-muted mt-2">
                            <i class="bi bi-info-circle me-1"></i> Thiết bị quét tự động nhấn Enter sau khi nhận diện mã.
                        </div>
                    </form>

                    <div class="hr-text my-4 text-muted"><span>HOẶC</span></div>

                    <!-- Camera Scan Toggle -->
                    <button id="toggle-camera-btn" class="btn btn-success btn-lg w-100 py-3 mb-2 shadow-sm">
                        <i class="bi bi-camera me-2"></i> Bật Camera quét QR
                    </button>
                </div>
            </div>
        </div>

        <!-- Camera Scanner Section -->
        <div class="col-lg-6 col-md-8 d-none" id="camera-scanner-wrapper">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-camera me-2 text-success"></i>Quét bằng Webcam
                    </h5>
                    <button class="btn-close" id="close-camera-btn" aria-label="Close"></button>
                </div>
                <div class="card-body p-4 text-center">
                    <!-- Scanner Reader Element -->
                    <div id="reader" class="bg-light border rounded overflow-hidden shadow-inner mx-auto mb-3" style="width: 100%; max-width: 480px; min-height: 350px;"></div>
                    
                    <div class="form-text text-muted">
                        <i class="bi bi-brightness-high me-1"></i> Đảm bảo mã QR nằm trong vùng camera quét và đủ ánh sáng.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles for Pulse Animation and Divider Text -->
<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: .6; transform: scale(1.05); }
    }
    .animate-pulse {
        animation: pulse 2s infinite ease-in-out;
        display: inline-block;
    }
    .hr-text {
        line-height: 1em;
        position: relative;
        outline: 0;
        border: 0;
        color: #999;
        text-align: center;
        height: 1.5em;
    }
    .hr-text:before {
        content: '';
        background: #e0e0e0;
        position: absolute;
        left: 0;
        top: 50%;
        width: 100%;
        height: 1px;
    }
    .hr-text span {
        position: relative;
        display: inline-block;
        background: #f8f9fa;
        padding: 0 15px;
        font-weight: 600;
        font-size: 0.85rem;
        letter-spacing: 0.1em;
    }
    .shadow-inner {
        box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
    }
</style>

<!-- Load html5-qrcode from CDN -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scannerInput = document.getElementById('scanner-input');
    const usbForm = document.getElementById('usb-scanner-form');
    const alertContainer = document.getElementById('alert-container');
    const toggleCameraBtn = document.getElementById('toggle-camera-btn');
    const cameraWrapper = document.getElementById('camera-scanner-wrapper');
    const closeCameraBtn = document.getElementById('close-camera-btn');
    
    let html5QrcodeScanner = null;

    // Autofocus input
    if (scannerInput) {
        scannerInput.focus();
        // Keep focus on input unless clicking on something else
        document.addEventListener('click', function(e) {
            if (e.target !== toggleCameraBtn && !cameraWrapper.contains(e.target) && e.target !== scannerInput) {
                scannerInput.focus();
            }
        });
    }

    // Show Alert helper
    function showAlert(message, type = 'danger') {
        alertContainer.className = `alert alert-${type} alert-dismissible fade show`;
        alertContainer.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        alertContainer.classList.remove('d-none');
    }

    // Process code AJAX handler
    function processCode(code) {
        if (!code) return;
        
        showAlert('Đang xử lý check-in...', 'info');
        
        const formData = new FormData();
        formData.append('code', code);
        
        fetch('?action=staff_checkin_process', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Check-in thành công! Đang chuyển hướng in vé...', 'success');
                // Redirect to staff_ticket_print
                let printUrl = '?action=staff_ticket_print';
                if (data.type === 'ticket') {
                    printUrl += '&id=' + data.ticket_id;
                } else if (data.type === 'booking') {
                    printUrl += '&ids=' + data.ticket_ids.join(',');
                }
                
                // Stop camera scan if running
                stopCamera().then(() => {
                    window.location.href = printUrl;
                }).catch(() => {
                    window.location.href = printUrl;
                });
            } else {
                showAlert(data.message || 'Có lỗi xảy ra trong quá trình check-in.', 'danger');
                if (scannerInput) {
                    scannerInput.value = '';
                    scannerInput.focus();
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Lỗi kết nối máy chủ.', 'danger');
            if (scannerInput) {
                scannerInput.value = '';
                scannerInput.focus();
            }
        });
    }

    // Handle USB Scanner submit
    usbForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const code = scannerInput.value.trim();
        if (code) {
            processCode(code);
        }
    });

    // Camera Scan implementation
    function onScanSuccess(decodedText, decodedResult) {
        // Stop scanning when code is found
        processCode(decodedText);
    }

    function onScanFailure(error) {
        // Just verbose console logs or ignore it
    }

    function startCamera() {
        cameraWrapper.classList.remove('d-none');
        toggleCameraBtn.disabled = true;
        toggleCameraBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Đang khởi tạo camera...';

        html5QrcodeScanner = new Html5Qrcode("reader");
        
        // Find cameras and start
        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length > 0) {
                // Use back camera if available, otherwise first camera
                let cameraId = devices[0].id;
                for (let device of devices) {
                    if (device.label.toLowerCase().includes('back') || device.label.toLowerCase().includes('rear')) {
                        cameraId = device.id;
                        break;
                    }
                }
                
                html5QrcodeScanner.start(
                    cameraId, 
                    {
                        fps: 15,
                        qrbox: { width: 250, height: 250 }
                    },
                    onScanSuccess,
                    onScanFailure
                )
                .then(() => {
                    toggleCameraBtn.classList.replace('btn-success', 'btn-danger');
                    toggleCameraBtn.innerHTML = '<i class="bi bi-camera-video-off me-2"></i> Tắt Camera';
                    toggleCameraBtn.disabled = false;
                })
                .catch(err => {
                    console.error('Camera start error:', err);
                    showAlert('Không thể mở camera: ' + err, 'danger');
                    stopCamera();
                });
            } else {
                showAlert('Không tìm thấy thiết bị camera nào.', 'danger');
                stopCamera();
            }
        }).catch(err => {
            console.error('Get cameras error:', err);
            showAlert('Lỗi yêu cầu quyền truy cập camera: ' + err, 'danger');
            stopCamera();
        });
    }

    function stopCamera() {
        return new Promise((resolve) => {
            toggleCameraBtn.classList.replace('btn-danger', 'btn-success');
            toggleCameraBtn.innerHTML = '<i class="bi bi-camera me-2"></i> Bật Camera quét QR';
            toggleCameraBtn.disabled = false;
            cameraWrapper.classList.add('d-none');
            
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().then(() => {
                    html5QrcodeScanner = null;
                    if (scannerInput) {
                        scannerInput.focus();
                    }
                    resolve();
                }).catch(err => {
                    console.error('Stop scanner error:', err);
                    html5QrcodeScanner = null;
                    if (scannerInput) {
                        scannerInput.focus();
                    }
                    resolve();
                });
            } else {
                if (scannerInput) {
                    scannerInput.focus();
                }
                resolve();
            }
        });
    }

    toggleCameraBtn.addEventListener('click', function() {
        if (html5QrcodeScanner) {
            stopCamera();
        } else {
            startCamera();
        }
    });

    closeCameraBtn.addEventListener('click', function() {
        stopCamera();
    });
});
</script>
