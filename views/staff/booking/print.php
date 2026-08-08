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

        /* Printable ticket at 4:3 aspect ratio (120mm x 90mm) */
        .receipt-container {
            width: 120mm;
            margin: 0 auto;
        }

        .ticket-receipt {
            width: 120mm;
            height: 90mm;
            margin: 0 auto 10mm auto;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            padding: 4mm 5mm;
            box-sizing: border-box;
            border: 1px solid #333;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.4;
            color: #000000;
            page-break-after: always;
            position: relative;
            overflow: hidden;
        }

        /* Avoid page break on the very last ticket */
        .ticket-receipt:last-child {
            page-break-after: avoid;
        }

        /* 2-column layout (70% main ticket body, 26% stub) */
        .ticket-main {
            width: 70%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding-right: 4mm;
            border-right: 1.5px dashed #000000;
        }

        .ticket-stub {
            width: 27%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding-left: 2mm;
            text-align: center;
        }

        .ticket-header {
            text-align: center;
            margin-bottom: 2mm;
        }

        .ticket-header .cinema-name {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .ticket-header .ticket-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.8mm;
        }

        .info-label {
            font-weight: bold;
        }

        .info-value {
            text-align: right;
            max-width: 65%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .movie-title-val {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .seat-number-val {
            font-size: 13px;
            font-weight: bold;
            border: 1px solid #000;
            padding: 0 1.5mm;
            display: inline-block;
        }

        .ticket-code-display {
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 1px;
            border: 1.5px dashed #000;
            padding: 1.5mm 1mm;
            display: inline-block;
            width: 100%;
            word-break: break-all;
            text-align: center;
        }

        .ticket-footer {
            text-align: center;
            font-size: 8px;
            border-top: 1px dashed #ccc;
            padding-top: 1mm;
            margin-top: 1mm;
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
                width: 120mm;
                margin: 0;
            }

            .ticket-receipt {
                width: 120mm;
                height: 90mm;
                margin: 0;
                box-shadow: none;
                border: 1px solid #000;
                page-break-after: always;
            }

            @page {
                size: 120mm 90mm;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Toolbar displayed on screen only -->
    <div class="no-print-toolbar no-print">
        <a href="<?= !empty($_GET['booking_id']) ? '?action=staff_booking_detail&id=' . (int)$_GET['booking_id'] : '?action=staff_checkin' ?>" class="btn btn-secondary flex-fill">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
        <button onclick="window.print()" class="btn btn-primary flex-fill">
            <i class="bi bi-printer"></i> In lại
        </button>
    </div>

    <!-- Container containing all tickets in 4:3 format -->
    <div class="receipt-container">
        <?php foreach ($tickets as $idx => $t): ?>
            <div class="ticket-receipt">
                <!-- 1. Main Ticket Part (Left) -->
                <div class="ticket-main">
                    <div class="ticket-header">
                        <div class="cinema-name">PETACINEMA</div>
                        <div class="ticket-title">VÉ XEM PHIM</div>
                    </div>

                    <div class="ticket-body">
                        <div class="info-row">
                            <span class="info-label">Mã Đơn:</span>
                            <span class="info-value"><?= htmlspecialchars($t['booking_code'] ?? '-') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Khách:</span>
                            <span class="info-value"><?= htmlspecialchars($t['customer_name'] ?? 'Khách vãng lai') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phim:</span>
                            <span class="info-value movie-title-val" title="<?= htmlspecialchars($t['movie_title'] ?? '') ?>">
                                <?= htmlspecialchars($t['movie_title'] ?? '-') ?>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Suất:</span>
                            <span class="info-value">
                                <?= !empty($t['start_time']) ? date('d/m/Y H:i', strtotime($t['start_time'])) : '-' ?>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phòng:</span>
                            <span class="info-value fw-bold"><?= htmlspecialchars($t['room_name'] ?? '-') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Ghế:</span>
                            <span class="info-value">
                                <span class="seat-number-val"><?= htmlspecialchars($t['seat_number'] ?? '-') ?></span>
                                <small>(<?= htmlspecialchars($t['seat_type_name'] ?? 'Standard') ?>)</small>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Giá:</span>
                            <span class="info-value"><?= number_format((float)($t['ticket_price'] ?? 0), 0, ',', '.') ?> đ</span>
                        </div>
                    </div>

                    <div class="ticket-footer">
                        PetaCinema - Trải nghiệm điện ảnh đỉnh cao
                    </div>
                </div>

                <!-- 2. Detachable Ticket Stub Part (Right) -->
                <div class="ticket-stub">
                    <div class="ticket-header">
                        <div class="cinema-name" style="font-size: 11px;">PETACINEMA</div>
                        <div style="font-size: 9px; font-weight: bold;">STUB</div>
                    </div>

                    <div class="ticket-body-stub" style="width: 100%;">
                        <div style="font-size: 9px; margin-bottom: 2px; text-align: left;">
                            <strong>Phim:</strong><br>
                            <span style="font-weight: bold; font-size: 10px; display: inline-block; max-width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= htmlspecialchars($t['movie_title'] ?? '-') ?>
                            </span>
                        </div>
                        <div class="info-row" style="font-size: 9px;">
                            <span>Phòng:</span>
                            <span class="fw-bold"><?= htmlspecialchars($t['room_name'] ?? '-') ?></span>
                        </div>
                        <div class="info-row" style="font-size: 10px; margin-top: 1mm;">
                            <span>Ghế:</span>
                            <span class="seat-number-val px-1" style="font-size: 11px;"><?= htmlspecialchars($t['seat_number'] ?? '-') ?></span>
                        </div>
                    </div>

                    <div class="ticket-code-wrapper" style="width: 100%; margin-top: 2mm;">
                        <div class="ticket-code-display"><?= htmlspecialchars($t['booking_code'] ?? '-') ?></div>
                    </div>

                    <div style="font-size: 8px; text-align: center; color: #555; width: 100%; border-top: 1px dashed #ccc; padding-top: 1mm;">
                        NV: <?= htmlspecialchars($t['staff_name'] ?? $_SESSION['user']['fullname'] ?? 'Staff') ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Trigger window print after render
            setTimeout(function() {
                window.print();
            }, 300);
        });

        // Redirect back on print completion or cancel
        window.onafterprint = function() {
            <?php if (!empty($_GET['booking_id'])): ?>
                window.location.href = "?action=staff_booking_detail&id=<?= (int)$_GET['booking_id'] ?>";
            <?php else: ?>
                window.location.href = "?action=staff_checkin";
            <?php endif; ?>
        };
    </script>
</body>
</html>