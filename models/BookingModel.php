<?php

class BookingModel extends BaseModel
{
    protected $table = "bookings";

    // JOIN users + showtimes + movies để hiển thị đầy đủ thông tin booking
    public function getAll()
    {
        return $this->searchAndFilter();
    }

    public function searchAndFilter($keyword = null)
    {
        $sql = "SELECT b.*,
                       u.fullname AS customer_name,
                       u.email AS customer_email,
                       m.title AS movie_title,
                       st.start_time,
                       st.end_time,
                       r.name AS room_name,
                       GROUP_CONCAT(
                           DISTINCT se.seat_number
                           ORDER BY se.row_char, se.col_num
                           SEPARATOR ', '
                       ) AS seat_numbers
                FROM {$this->table} b
                JOIN users u ON u.id = b.user_id
                JOIN showtimes st ON st.id = b.showtime_id
                JOIN movies m ON m.id = st.movie_id
                LEFT JOIN rooms r ON r.id = st.room_id
                LEFT JOIN tickets t ON t.booking_id = b.id
                LEFT JOIN seats se ON se.id = t.seat_id
                WHERE 1=1";

        if (!empty($keyword)) {
            $sql .= " AND (b.booking_code LIKE :keyword OR u.fullname LIKE :keyword OR u.email LIKE :keyword OR m.title LIKE :keyword OR se.seat_number LIKE :keyword)";
        }

        $sql .= " GROUP BY b.id, u.fullname, u.email, m.title, st.start_time, st.end_time, r.name";
        $sql .= " ORDER BY b.id DESC";

        $stmt = $this->pdo->prepare($sql);
        if (!empty($keyword)) {
            $stmt->bindValue(':keyword', '%' . $keyword . '%');
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT b.*,
                u.fullname AS customer_name,
                u.email AS customer_email,
                m.title AS movie_title,
                m.poster AS movie_poster,
                st.start_time,
                st.end_time,
                st.base_price,
                r.name AS room_name,
                rt.name AS room_type_name,
                p.payment_method,
                p.transaction_code,
                p.status AS payment_status,
                p.payment_time,
                GROUP_CONCAT(
                    DISTINCT se.seat_number
                    ORDER BY se.row_char, se.col_num
                    SEPARATOR ', '
                ) AS seat_numbers
                FROM {$this->table} b
                JOIN users u ON u.id = b.user_id
                JOIN showtimes st ON st.id = b.showtime_id
                JOIN movies m ON m.id = st.movie_id
                LEFT JOIN rooms r ON r.id = st.room_id
                LEFT JOIN room_types rt ON rt.id = r.room_type_id
                LEFT JOIN payments p ON p.id = b.payment_id
                LEFT JOIN tickets t ON t.booking_id = b.id
                LEFT JOIN seats se ON se.id = t.seat_id
                WHERE b.id = :id
                GROUP BY b.id, u.fullname, u.email, m.title, m.poster, st.start_time, st.end_time, st.base_price, r.name, rt.name, p.payment_method, p.transaction_code, p.status, p.payment_time";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getBookingTickets($bookingId)
    {
        $sql = "SELECT t.id AS ticket_id,
                       t.price AS ticket_price,
                       s.seat_number,
                       s.row_char,
                       s.col_num,
                       st.name AS seat_type_name
                FROM tickets t
                JOIN seats s ON s.id = t.seat_id
                JOIN seat_types st ON st.id = s.seat_type_id
                WHERE t.booking_id = :booking_id
                ORDER BY s.row_char ASC, s.col_num ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBookingFoodOrders($bookingId)
    {
        $sql = "SELECT fo.*,
                       f.name AS food_name,
                       fv.size AS variant_size
                FROM food_orders fo
                JOIN food_variants fv ON fv.id = fo.food_variant_id
                JOIN foods f ON f.id = fv.food_id
                WHERE fo.booking_id = :booking_id
                ORDER BY fo.id ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    /**
     * Danh sách vé đã thanh toán của một khách hàng.
     * Không trả room_name vì phía khách chỉ cần thấy phòng sau khi check-in/in vé tại quầy.
     */
    public function getPaidBookingsByUser(int $userId): array
    {
        $sql = "SELECT b.id,
                       b.booking_code,
                       b.total_amount,
                       b.status,
                       b.created_at,
                       b.checkin_status,
                       b.checked_in_at,
                       b.checked_in_by,
                       m.title AS movie_title,
                       m.poster AS movie_poster,
                       st.start_time,
                       st.end_time,
                       rt.name AS room_type_name,
                       p.payment_method,
                       p.transaction_code,
                       p.status AS payment_status,
                       p.payment_time,
                       GROUP_CONCAT(
                           DISTINCT se.seat_number
                           ORDER BY se.row_char, se.col_num
                           SEPARATOR ', '
                       ) AS seat_numbers,
                       COUNT(DISTINCT t.id) AS ticket_count,
                       COALESCE(SUM(t.price), 0) AS ticket_total
                FROM bookings b
                INNER JOIN showtimes st ON st.id = b.showtime_id
                INNER JOIN movies m ON m.id = st.movie_id
                INNER JOIN rooms r ON r.id = st.room_id
                INNER JOIN room_types rt ON rt.id = r.room_type_id
                LEFT JOIN payments p ON p.id = b.payment_id
                LEFT JOIN tickets t ON t.booking_id = b.id
                LEFT JOIN seats se ON se.id = t.seat_id
                WHERE b.user_id = :user_id
                  AND b.status = 'paid'
                GROUP BY b.id, b.booking_code, b.total_amount, b.status, b.created_at,
                         b.checkin_status, b.checked_in_at, b.checked_in_by,
                         m.title, m.poster, st.start_time, st.end_time, rt.name,
                         p.payment_method, p.transaction_code, p.status, p.payment_time
                ORDER BY b.created_at DESC, b.id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Danh sách suất chiếu kèm tên phim + phòng, dùng cho dropdown chọn suất chiếu
    public function getShowtimeOptions()
    {
        $sql = "SELECT s.id, s.start_time, s.end_time, s.base_price,
                m.title AS movie_title, r.name AS room_name
                FROM showtimes s
                JOIN movies m ON m.id = s.movie_id
                JOIN rooms r ON r.id = s.room_id
                ORDER BY s.start_time ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    public function getFoodVariantOptions(): array
    {
        $sql = "SELECT fv.id, fv.size, fv.price, fv.stock,
                       f.name AS food_name
                FROM food_variants fv
                JOIN foods f ON f.id = fv.food_id
                WHERE f.status = 'active'
                ORDER BY f.name ASC, fv.price ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createBookingWithTicketsAndFoods(array $data): int
    {
        $showtimeId = (int) ($data['showtime_id'] ?? 0);
        $seatNumbers = $data['seat_numbers'] ?? [];
        $foodQuantities = $data['food_quantities'] ?? [];

        if ($showtimeId <= 0 || empty($seatNumbers)) {
            throw new InvalidArgumentException('Suất chiếu hoặc danh sách ghế không hợp lệ.');
        }

        try {
            $this->pdo->beginTransaction();

            $showtime = $this->getShowtimeForBooking($showtimeId);
            if (!$showtime) {
                throw new InvalidArgumentException('Suất chiếu không tồn tại.');
            }

            $seats = $this->getSeatsForBooking($showtimeId, (int) $showtime['room_id'], $seatNumbers);
            if (count($seats) !== count($seatNumbers)) {
                throw new InvalidArgumentException('Có ghế không tồn tại, không thuộc phòng hoặc đã được đặt.');
            }

            $this->validateCoupleSeats($seats);
            $foods = $this->getFoodsForBooking($foodQuantities);

            $ticketTotal = 0;
            foreach ($seats as $seat) {
                $ticketTotal += (float) $showtime['base_price']
                    + (float) $showtime['room_price_modifier']
                    + (float) $seat['surcharge'];
            }

            $foodTotal = 0;
            foreach ($foods as $food) {
                $foodTotal += (float) $food['price'] * (int) $food['quantity'];
            }
            $totalAmount = $ticketTotal + $foodTotal;

            $sql = "INSERT INTO bookings
                        (booking_code, user_id, showtime_id, total_amount, status)
                    VALUES
                        (:booking_code, :user_id, :showtime_id, :total_amount, :status)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':booking_code' => $data['booking_code'],
                ':user_id' => (int) $data['user_id'],
                ':showtime_id' => $showtimeId,
                ':total_amount' => $totalAmount,
                ':status' => $data['status'],
            ]);

            $bookingId = (int) $this->pdo->lastInsertId();

            $ticketSql = "INSERT INTO tickets
                            (booking_id, seat_id, price)
                          VALUES
                            (:booking_id, :seat_id, :price)";
            $ticketStmt = $this->pdo->prepare($ticketSql);
            foreach ($seats as $seat) {
                $ticketPrice = (float) $showtime['base_price']
                    + (float) $showtime['room_price_modifier']
                    + (float) $seat['surcharge'];
                $ticketStmt->execute([
                    ':booking_id' => $bookingId,
                    ':seat_id' => (int) $seat['id'],
                    ':price' => $ticketPrice,
                ]);
            }

            if (!empty($foods)) {
                $foodSql = "INSERT INTO food_orders
                                (booking_id, food_variant_id, quantity, price_at_booking)
                            VALUES
                                (:booking_id, :food_variant_id, :quantity, :price_at_booking)";
                $foodStmt = $this->pdo->prepare($foodSql);
                foreach ($foods as $food) {
                    $foodStmt->execute([
                        ':booking_id' => $bookingId,
                        ':food_variant_id' => (int) $food['id'],
                        ':quantity' => (int) $food['quantity'],
                        ':price_at_booking' => (float) $food['price'],
                    ]);
                }

                // Booking cancelled không chiếm tồn kho. Booking pending/paid thì giữ/trừ kho.
                // Nếu transaction phía dưới lỗi thì toàn bộ thay đổi sẽ rollback.
                if (($data['status'] ?? 'pending') !== 'cancelled') {
                    $this->decreaseFoodStock($foods);
                }
            }

            $this->pdo->commit();
            return $bookingId;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    private function getFoodsForBooking(array $foodQuantities): array
    {
        if (empty($foodQuantities)) {
            return [];
        }

        $variantIds = array_map('intval', array_keys($foodQuantities));
        $placeholders = implode(',', array_fill(0, count($variantIds), '?'));
        $sql = "SELECT fv.id, fv.price, fv.stock, f.name AS food_name, fv.size
                FROM food_variants fv
                JOIN foods f ON f.id = fv.food_id
                WHERE fv.id IN ($placeholders)
                  AND f.status = 'active'
                FOR UPDATE";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($variantIds);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) !== count($variantIds)) {
            throw new InvalidArgumentException('Có món ăn không tồn tại hoặc đã ngừng bán.');
        }

        foreach ($rows as &$row) {
            $quantity = (int) ($foodQuantities[(int) $row['id']] ?? 0);
            if ($quantity <= 0) {
                throw new InvalidArgumentException('Số lượng đồ ăn không hợp lệ.');
            }
            if ((int) $row['stock'] < $quantity) {
                throw new InvalidArgumentException('Món ' . $row['food_name'] . ' size ' . ($row['size'] ?: '-') . ' không đủ tồn kho.');
            }
            $row['quantity'] = $quantity;
        }
        unset($row);

        return $rows;
    }

    /**
     * Trừ/giữ tồn kho đồ ăn trong transaction hiện tại.
     * getFoodsForBooking() đã SELECT ... FOR UPDATE và kiểm tra đủ tồn kho trước đó.
     */
    private function decreaseFoodStock(array $foods): void
    {
        if (empty($foods)) {
            return;
        }

        $stmt = $this->pdo->prepare(
            "UPDATE food_variants
             SET stock = stock - :quantity
             WHERE id = :id
               AND stock >= :quantity_check"
        );

        foreach ($foods as $food) {
            $quantity = (int) ($food['quantity'] ?? 0);
            $variantId = (int) ($food['id'] ?? 0);

            if ($variantId <= 0 || $quantity <= 0) {
                throw new InvalidArgumentException('Thông tin tồn kho đồ ăn không hợp lệ.');
            }

            $stmt->execute([
                ':quantity' => $quantity,
                ':id' => $variantId,
                ':quantity_check' => $quantity,
            ]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('Tồn kho đồ ăn vừa thay đổi. Vui lòng chọn lại món.');
            }
        }
    }

    /**
     * Hoàn lại tồn kho của các booking bị huỷ/hết hạn.
     * Chỉ được gọi khi các booking đó vẫn đang ở trạng thái pending và đã được lock.
     */
    private function restoreFoodStockForBookings(array $bookingIds): void
    {
        $bookingIds = array_values(array_unique(array_filter(array_map('intval', $bookingIds))));
        if (empty($bookingIds)) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($bookingIds), '?'));

        $stmt = $this->pdo->prepare(
            "UPDATE food_variants fv
             INNER JOIN (
                 SELECT fo.food_variant_id, SUM(fo.quantity) AS restore_quantity
                 FROM food_orders fo
                 WHERE fo.booking_id IN ($placeholders)
                 GROUP BY fo.food_variant_id
             ) x ON x.food_variant_id = fv.id
             SET fv.stock = fv.stock + x.restore_quantity"
        );
        $stmt->execute($bookingIds);
    }

    private function getShowtimeForBooking(int $showtimeId)
    {
        $sql = "SELECT s.id, s.room_id, s.base_price, s.start_time,
                       rt.price_modifier AS room_price_modifier
                FROM showtimes s
                INNER JOIN rooms r ON r.id = s.room_id
                INNER JOIN room_types rt ON rt.id = r.room_type_id
                WHERE s.id = :id
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $showtimeId]);
        return $stmt->fetch();
    }

    private function getSeatsForBooking(int $showtimeId, int $roomId, array $seatNumbers): array
    {
        $placeholders = implode(',', array_fill(0, count($seatNumbers), '?'));

        $sql = "SELECT s.id, s.seat_number, s.couple_group, s.status,
                       st.name AS seat_type_name, st.surcharge
                FROM seats s
                JOIN seat_types st ON st.id = s.seat_type_id
                WHERE s.room_id = ?
                  AND s.status = 'available'
                  AND s.seat_number IN ($placeholders)
                  AND NOT EXISTS (
                      SELECT 1
                      FROM tickets t
                      JOIN bookings b ON b.id = t.booking_id
                      WHERE t.seat_id = s.id
                        AND b.showtime_id = ?
                        AND (
                            b.status = 'paid'
                            OR (
                                b.status = 'pending'
                                AND b.created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
                            )
                        )
                  )
                FOR UPDATE";

        $params = array_merge([$roomId], $seatNumbers, [$showtimeId]);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function validateCoupleSeats(array $selectedSeats): void
    {
        $selectedByGroup = [];

        foreach ($selectedSeats as $seat) {
            if (($seat['seat_type_name'] ?? '') !== 'Couple') {
                continue;
            }

            $group = trim((string) ($seat['couple_group'] ?? ''));
            if ($group === '') {
                throw new InvalidArgumentException('Ghế Couple chưa có couple_group trong cơ sở dữ liệu.');
            }

            $selectedByGroup[$group] = ($selectedByGroup[$group] ?? 0) + 1;
        }

        foreach ($selectedByGroup as $group => $selectedCount) {
            $sql = "SELECT COUNT(*)
                    FROM seats
                    WHERE couple_group = :couple_group";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':couple_group' => $group]);
            $totalInGroup = (int) $stmt->fetchColumn();

            if ($selectedCount !== $totalInGroup) {
                throw new InvalidArgumentException('Ghế Couple phải chọn đủ cả cặp.');
            }
        }
    }


    /**
     * Tạo booking pending khi khách bấm "Thanh toán".
     * Việc insert tickets ngay trong transaction chính là cơ chế giữ ghế.
     */
    public function createPendingCheckout(array $data): array
    {
        $showtimeId = (int) ($data['showtime_id'] ?? 0);
        $userId = (int) ($data['user_id'] ?? 0);
        $seatNumbers = array_values(array_unique(array_filter(array_map(
            static fn($seat) => strtoupper(trim((string) $seat)),
            $data['seat_numbers'] ?? []
        ))));
        $foodQuantities = $data['food_quantities'] ?? [];

        if ($userId <= 0 || $showtimeId <= 0 || empty($seatNumbers)) {
            throw new InvalidArgumentException('Thông tin đặt vé không hợp lệ.');
        }

        try {
            $this->pdo->beginTransaction();

            $this->expirePendingBookingsInCurrentTransaction();

            $showtime = $this->getShowtimeForBooking($showtimeId);
            if (!$showtime) {
                throw new InvalidArgumentException('Suất chiếu không tồn tại.');
            }

            if (strtotime((string) $showtime['start_time']) <= time()) {
                throw new InvalidArgumentException('Suất chiếu đã bắt đầu, không thể đặt vé.');
            }

            $seats = $this->getSeatsForBooking(
                $showtimeId,
                (int) $showtime['room_id'],
                $seatNumbers
            );

            if (count($seats) !== count($seatNumbers)) {
                throw new InvalidArgumentException('Một hoặc nhiều ghế vừa được khách khác giữ/đặt. Vui lòng chọn lại.');
            }

            $this->validateCoupleSeats($seats);
            $foods = $this->getFoodsForBooking($foodQuantities);

            $ticketTotal = 0.0;
            foreach ($seats as $seat) {
                $ticketTotal += (float) $showtime['base_price']
                    + (float) $showtime['room_price_modifier']
                    + (float) $seat['surcharge'];
            }

            $foodTotal = 0.0;
            foreach ($foods as $food) {
                $foodTotal += (float) $food['price'] * (int) $food['quantity'];
            }

            $totalAmount = $ticketTotal + $foodTotal;

            $bookingStmt = $this->pdo->prepare(
                "INSERT INTO bookings
                    (booking_code, user_id, showtime_id, payment_id, total_amount, status)
                 VALUES
                    (NULL, :user_id, :showtime_id, NULL, :total_amount, 'pending')"
            );
            $bookingStmt->execute([
                ':user_id' => $userId,
                ':showtime_id' => $showtimeId,
                ':total_amount' => $totalAmount,
            ]);

            $bookingId = (int) $this->pdo->lastInsertId();
            $bookingCode = 'PET' . date('Ymd') . str_pad((string) $bookingId, 4, '0', STR_PAD_LEFT);

            $codeStmt = $this->pdo->prepare(
                "UPDATE bookings SET booking_code = :booking_code WHERE id = :id"
            );
            $codeStmt->execute([
                ':booking_code' => $bookingCode,
                ':id' => $bookingId,
            ]);

            $ticketStmt = $this->pdo->prepare(
                "INSERT INTO tickets (booking_id, seat_id, price)
                 VALUES (:booking_id, :seat_id, :price)"
            );

            foreach ($seats as $seat) {
                $ticketPrice = (float) $showtime['base_price']
                    + (float) $showtime['room_price_modifier']
                    + (float) $seat['surcharge'];

                $ticketStmt->execute([
                    ':booking_id' => $bookingId,
                    ':seat_id' => (int) $seat['id'],
                    ':price' => $ticketPrice,
                ]);
            }

            if (!empty($foods)) {
                $foodStmt = $this->pdo->prepare(
                    "INSERT INTO food_orders
                        (booking_id, food_variant_id, quantity, price_at_booking)
                     VALUES
                        (:booking_id, :food_variant_id, :quantity, :price_at_booking)"
                );

                foreach ($foods as $food) {
                    $foodStmt->execute([
                        ':booking_id' => $bookingId,
                        ':food_variant_id' => (int) $food['id'],
                        ':quantity' => (int) $food['quantity'],
                        ':price_at_booking' => (float) $food['price'],
                    ]);
                }

                // Booking pending đã giữ ghế thì đồng thời giữ luôn tồn kho đồ ăn.
                $this->decreaseFoodStock($foods);
            }

            $paymentStmt = $this->pdo->prepare(
                "INSERT INTO payments
                    (payment_method, transaction_code, amount, status, payment_time)
                 VALUES
                    ('vnpay', NULL, :amount, 'pending', NULL)"
            );
            $paymentStmt->execute([':amount' => $totalAmount]);
            $paymentId = (int) $this->pdo->lastInsertId();

            $linkPaymentStmt = $this->pdo->prepare(
                "UPDATE bookings SET payment_id = :payment_id WHERE id = :id"
            );
            $linkPaymentStmt->execute([
                ':payment_id' => $paymentId,
                ':id' => $bookingId,
            ]);

            $createdStmt = $this->pdo->prepare(
                "SELECT id, booking_code, total_amount, created_at
                 FROM bookings
                 WHERE id = :id
                 LIMIT 1"
            );
            $createdStmt->execute([':id' => $bookingId]);
            $booking = $createdStmt->fetch(PDO::FETCH_ASSOC);

            $this->pdo->commit();

            return $booking ?: [
                'id' => $bookingId,
                'booking_code' => $bookingCode,
                'total_amount' => $totalAmount,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function expirePendingBookings(): int
    {
        try {
            $this->pdo->beginTransaction();
            $count = $this->expirePendingBookingsInCurrentTransaction();
            $this->pdo->commit();
            return $count;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    private function expirePendingBookingsInCurrentTransaction(): int
    {
        // Lock chính xác các booking pending đã quá 5 phút.
        // Nhờ lock này, payment success và timeout không thể xử lý cùng một booking đồng thời.
        $expiredStmt = $this->pdo->prepare(
            "SELECT id
             FROM bookings
             WHERE status = 'pending'
               AND created_at <= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
             FOR UPDATE"
        );
        $expiredStmt->execute();
        $bookingIds = array_map('intval', $expiredStmt->fetchAll(PDO::FETCH_COLUMN));

        if (empty($bookingIds)) {
            return 0;
        }

        // Booking pending đã trừ stock khi bắt đầu checkout.
        // Khi hết hạn phải hoàn lại đúng một lần trước khi chuyển sang cancelled.
        $this->restoreFoodStockForBookings($bookingIds);

        $placeholders = implode(',', array_fill(0, count($bookingIds), '?'));

        $paymentStmt = $this->pdo->prepare(
            "UPDATE payments p
             INNER JOIN bookings b ON b.payment_id = p.id
             SET p.status = 'failed',
                 p.payment_time = COALESCE(p.payment_time, NOW())
             WHERE b.id IN ($placeholders)
               AND p.status = 'pending'"
        );
        $paymentStmt->execute($bookingIds);

        $bookingStmt = $this->pdo->prepare(
            "UPDATE bookings
             SET status = 'cancelled'
             WHERE id IN ($placeholders)
               AND status = 'pending'"
        );
        $bookingStmt->execute($bookingIds);

        return $bookingStmt->rowCount();
    }

    public function getCheckoutByBookingCode(string $bookingCode): ?array
    {
        $sql = "SELECT b.id,
                       b.booking_code,
                       b.user_id,
                       b.showtime_id,
                       b.payment_id,
                       b.total_amount,
                       b.status,
                       b.created_at,
                       p.status AS payment_status,
                       p.transaction_code,
                       p.payment_time,
                       s.movie_id,
                       s.start_time,
                       m.title AS movie_title,
                       rt.name AS room_type_name,
                       GROUP_CONCAT(
                           se.seat_number
                           ORDER BY se.row_char, se.col_num
                           SEPARATOR ', '
                       ) AS seat_numbers
                FROM bookings b
                INNER JOIN showtimes s ON s.id = b.showtime_id
                INNER JOIN movies m ON m.id = s.movie_id
                INNER JOIN rooms r ON r.id = s.room_id
                INNER JOIN room_types rt ON rt.id = r.room_type_id
                LEFT JOIN payments p ON p.id = b.payment_id
                LEFT JOIN tickets t ON t.booking_id = b.id
                LEFT JOIN seats se ON se.id = t.seat_id
                WHERE b.booking_code = :booking_code
                GROUP BY b.id, b.booking_code, b.user_id, b.showtime_id, b.payment_id,
                         b.total_amount, b.status, b.created_at, p.status, p.transaction_code,
                         p.payment_time, s.movie_id, s.start_time, m.title, rt.name
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':booking_code' => $bookingCode]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function completeVnpayPayment(
        string $bookingCode,
        float $returnedAmount,
        string $transactionCode,
        ?string $payDate
    ): array {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                "SELECT b.*, p.status AS payment_status
                 FROM bookings b
                 LEFT JOIN payments p ON p.id = b.payment_id
                 WHERE b.booking_code = :booking_code
                 LIMIT 1
                 FOR UPDATE"
            );
            $stmt->execute([':booking_code' => $bookingCode]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$booking) {
                throw new RuntimeException('Không tìm thấy booking.');
            }

            if (abs((float) $booking['total_amount'] - $returnedAmount) > 0.01) {
                throw new RuntimeException('Số tiền VNPAY trả về không khớp booking.');
            }

            if ($booking['status'] === 'paid') {
                $this->pdo->commit();
                return ['success' => true, 'message' => 'Booking đã được thanh toán trước đó.'];
            }

            if ($booking['status'] !== 'pending') {
                throw new RuntimeException('Booking đã hết hạn hoặc đã bị hủy.');
            }

            $createdAt = new DateTimeImmutable((string) $booking['created_at'], new DateTimeZone('Asia/Ho_Chi_Minh'));
            $deadline = $createdAt->modify('+5 minutes');

            $paidAt = $payDate
                ? DateTimeImmutable::createFromFormat('YmdHis', $payDate, new DateTimeZone('Asia/Ho_Chi_Minh'))
                : new DateTimeImmutable('now', new DateTimeZone('Asia/Ho_Chi_Minh'));

            if (!$paidAt || $paidAt > $deadline) {
                throw new RuntimeException('Giao dịch đã vượt quá thời gian thanh toán 5 phút.');
            }

            $paymentStmt = $this->pdo->prepare(
                "UPDATE payments
                 SET transaction_code = :transaction_code,
                     status = 'completed',
                     payment_time = :payment_time
                 WHERE id = :payment_id"
            );
            $paymentStmt->execute([
                ':transaction_code' => $transactionCode !== '' ? $transactionCode : null,
                ':payment_time' => $paidAt->format('Y-m-d H:i:s'),
                ':payment_id' => (int) $booking['payment_id'],
            ]);

            $bookingStmt = $this->pdo->prepare(
                "UPDATE bookings SET status = 'paid' WHERE id = :id"
            );
            $bookingStmt->execute([':id' => (int) $booking['id']]);

            $this->pdo->commit();
            return ['success' => true, 'message' => 'Thanh toán thành công.'];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function cancelPendingCheckout(
        string $bookingCode,
        string $transactionCode = '',
        ?string $payDate = null
    ): bool {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                "SELECT b.id, b.payment_id, b.status
                 FROM bookings b
                 WHERE b.booking_code = :booking_code
                 LIMIT 1
                 FOR UPDATE"
            );
            $stmt->execute([':booking_code' => $bookingCode]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$booking) {
                $this->pdo->rollBack();
                return false;
            }

            if ($booking['status'] === 'paid') {
                $this->pdo->commit();
                return false;
            }

            if ($booking['status'] === 'pending') {
                // Thanh toán thất bại/bị huỷ: nhả phần tồn kho đã giữ.
                // Chỉ chạy khi status còn pending nên gọi lại lần 2 sẽ không cộng stock thêm.
                $this->restoreFoodStockForBookings([(int) $booking['id']]);

                $bookingStmt = $this->pdo->prepare(
                    "UPDATE bookings SET status = 'cancelled' WHERE id = :id AND status = 'pending'"
                );
                $bookingStmt->execute([':id' => (int) $booking['id']]);
            }

            if (!empty($booking['payment_id'])) {
                $paymentTime = null;
                if ($payDate) {
                    $parsed = DateTimeImmutable::createFromFormat(
                        'YmdHis',
                        $payDate,
                        new DateTimeZone('Asia/Ho_Chi_Minh')
                    );
                    if ($parsed) {
                        $paymentTime = $parsed->format('Y-m-d H:i:s');
                    }
                }

                $paymentStmt = $this->pdo->prepare(
                    "UPDATE payments
                     SET transaction_code = CASE
                            WHEN :transaction_code_value = '' THEN transaction_code
                            ELSE :transaction_code_set
                         END,
                         status = CASE WHEN status = 'completed' THEN status ELSE 'failed' END,
                         payment_time = COALESCE(:payment_time, payment_time, NOW())
                     WHERE id = :payment_id"
                );
                $paymentStmt->execute([
                    ':transaction_code_value' => $transactionCode,
                    ':transaction_code_set' => $transactionCode !== '' ? $transactionCode : null,
                    ':payment_time' => $paymentTime,
                    ':payment_id' => (int) $booking['payment_id'],
                ]);
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return false;
        }
    }

    public function addBooking($data)
    {
        $sql = "INSERT INTO {$this->table} (booking_code, user_id, showtime_id, total_amount, status)
                VALUES (:booking_code, :user_id, :showtime_id, :total_amount, :status)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':booking_code', $data['booking_code']);
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':showtime_id', $data['showtime_id'], PDO::PARAM_INT);
        $stmt->bindParam(':total_amount', $data['total_amount']);
        $stmt->bindParam(':status', $data['status']);
        return $stmt->execute();
    }

    public function editBooking($id, $data)
    {
        $sql = "UPDATE {$this->table}
                SET user_id = :user_id, showtime_id = :showtime_id,
                    total_amount = :total_amount, status = :status
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':showtime_id', $data['showtime_id'], PDO::PARAM_INT);
        $stmt->bindParam(':total_amount', $data['total_amount']);
        $stmt->bindParam(':status', $data['status']);
        return $stmt->execute();
    }

    public function deleteBooking($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Sinh booking_code dạng PET + ngày + số thứ tự trong ngày
    public function generateBookingCode()
    {
        $prefix = 'PET' . date('Ymd');

        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE booking_code LIKE :prefix";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':prefix', $prefix . '%');
        $stmt->execute();

        $sequence = (int) $stmt->fetchColumn() + 1;
        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}