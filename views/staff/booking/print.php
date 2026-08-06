<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In Vé - PETACINEMA</title>
    <!-- Bootstrap 5 CSS for fallback UI on screen -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        /* General styling for screen preview */
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 20px 0;
        }

        .no-print-toolbar {
            max-width: 320px;
            margin: 0 auto 20px auto;
            display: flex;
            gap: 10px;
        }

        /* Printable Receipt Styles (80mm width) */
        .receipt-container {
            width: 80mm;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            padding: 6mm;
            box-sizing: border-box;
            border: 1px solid #e0e0e0;
        }

        .ticket-receipt {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.4;
            color: #000000;
            word-wrap: break-word;
            page-break-after: always;
        }

        /* Avoid page break on the very last ticket */
        .ticket-receipt:last-child {
            page-break-after: avoid;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 4mm;
        }

        .receipt-header .cinema-name {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 1mm;
        }

        .receipt-header .receipt-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 1mm 0;
            margin-top: 2mm;
        }

        .receipt-divider {
            border-top: 1px dashed #000000;
            margin: 3mm 0;
        }

        .receipt-double-divider {
            border-top: 2px double #000000;
            margin: 3mm 0;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1mm;
        }

        .receipt-row .label {
            font-weight: bold;
        }

        .receipt-row .value {
            text-align: right;
            max-width: 60%;
        }

        .movie-title-val {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .seat-number-val {
            font-size: 14px;
            font-weight: bold;
            border: 1px solid #000;
            padding: 0 1.5mm;
            display: inline-block;
        }

        .qr-wrapper {
            text-align: center;
            margin: 4mm 0;
        }

        .qr-wrapper canvas {
            display: block;
            margin: 0 auto;
        }

        .ticket-code-display {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-top: 1mm;
        }

        .receipt-footer {
            text-align: center;
            margin-top: 5mm;
            font-size: 9px;
        }

        /* Print Media Queries */
        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
                margin: 0;
            }

            .no-print-toolbar, .no-print {
                display: none !important;
            }

            .receipt-container {
                width: 80mm;
                margin: 0;
                padding: 4mm;
                box-shadow: none;
                border: none;
            }

            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Toolbar displayed on screen only -->
    <div class="no-print-toolbar no-print">
        <a href="?action=staff_checkin" class="btn btn-secondary flex-fill">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
        <button onclick="window.print()" class="btn btn-primary flex-fill">
            <i class="bi bi-printer"></i> In lại
        </button>
    </div>

    <!-- Container containing all tickets -->
    <div class="receipt-container">
        <?php foreach ($tickets as $idx => $t): ?>
            <div class="ticket-receipt">
                <div class="receipt-header">
                    <div class="cinema-name">PETACINEMA</div>
                    <div class="receipt-title">VÉ XEM PHIM</div>
                </div>

                <div class="receipt-row">
                    <span class="label">Mã Booking:</span>
                    <span class="value"><?= htmlspecialchars($t['booking_code'] ?? '-') ?></span>
                </div>
                <div class="receipt-row">
                    <span class="label">Khách hàng:</span>
                    <span class="value"><?= htmlspecialchars($t['customer_name'] ?? 'Khách vãng lai') ?></span>
                </div>
                
                <div class="receipt-divider"></div>

                <div class="receipt-row">
                    <span class="label">Phim:</span>
                    <span class="value movie-title-val"><?= htmlspecialchars($t['movie_title'] ?? '-') ?></span>
                </div>
                <div class="receipt-row">
                    <span class="label">Suất chiếu:</span>
                    <span class="value">
                        <?= !empty($t['start_time']) ? date('d/m/Y H:i', strtotime($t['start_time'])) : '-' ?>
                    </span>
                </div>
                <div class="receipt-row">
                    <span class="label">Phòng chiếu:</span>
                    <span class="value fw-bold"><?= htmlspecialchars($t['room_name'] ?? '-') ?></span>
                </div>
                <div class="receipt-row">
                    <span class="label">Ghế đặt:</span>
                    <span class="value"><span class="seat-number-val"><?= htmlspecialchars($t['seat_number'] ?? '-') ?></span></span>
                </div>
                <div class="receipt-row">
                    <span class="label">Loại ghế:</span>
                    <span class="value"><?= htmlspecialchars($t['seat_type_name'] ?? 'Standard') ?></span>
                </div>
                <div class="receipt-row">
                    <span class="label">Giá vé:</span>
                    <span class="value"><?= number_format((float)($t['ticket_price'] ?? 0), 0, ',', '.') ?> đ</span>
                </div>

                <div class="receipt-divider"></div>

                <!-- QR Code generator target -->
                <div class="qr-wrapper">
                    <canvas id="qr-<?= $t['ticket_id'] ?>"></canvas>
                    <div class="ticket-code-display"><?= htmlspecialchars($t['ticket_code'] ?? '-') ?></div>
                </div>

                <div class="receipt-double-divider"></div>

                <div class="receipt-row">
                    <span class="label">Nhân viên:</span>
                    <span class="value"><?= htmlspecialchars($t['staff_name'] ?? $_SESSION['user']['fullname'] ?? 'Staff') ?></span>
                </div>
                <div class="receipt-row">
                    <span class="label">Thời gian in:</span>
                    <span class="value"><?= date('d/m/Y H:i:s') ?></span>
                </div>

                <div class="receipt-footer">
                    Chúc quý khách xem phim vui vẻ!<br>
                    PetaCinema - Trải nghiệm điện ảnh đỉnh cao
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Load QRious Library via CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Generate QR Code for each ticket
            <?php foreach ($tickets as $t): ?>
                new QRious({
                    element: document.getElementById('qr-<?= $t['ticket_id'] ?>'),
                    value: '<?= esc_attr_js($t['ticket_code'] ?? '') ?>',
                    size: 130,
                    level: 'H'
                });
            <?php endforeach; ?>

            // Trigger window print
            setTimeout(function() {
                window.print();
            }, 300);
        });

        // Redirect back on print completion or cancel
        window.onafterprint = function() {
            window.location.href = "?action=staff_checkin";
        };
    </script>
</body>
</html>
<?php
// Helper to escape JS string attribute
function esc_attr_js($str) {
    return str_replace(["'", "\n", "\r"], ["\\'", '', ''], (string) $str);
}
?>
