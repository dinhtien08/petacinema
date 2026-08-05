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
                       t.ticket_code,
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
                $ticketTotal += (float) $showtime['base_price'] + (float) $seat['surcharge'];
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
                            (booking_id, seat_id, ticket_code, price)
                          VALUES
                            (:booking_id, :seat_id, :ticket_code, :price)";
            $ticketStmt = $this->pdo->prepare($ticketSql);
            foreach ($seats as $index => $seat) {
                $ticketPrice = (float) $showtime['base_price'] + (float) $seat['surcharge'];
                $ticketCode = $data['booking_code'] . '-T' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
                $ticketStmt->execute([
                    ':booking_id' => $bookingId,
                    ':seat_id' => (int) $seat['id'],
                    ':ticket_code' => $ticketCode,
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

    private function getShowtimeForBooking(int $showtimeId)
    {
        $sql = "SELECT id, room_id, base_price
                FROM showtimes
                WHERE id = :id
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
                        AND b.status IN ('pending', 'paid')
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

    public function getByUserId($userId)
    {
        $sql = "SELECT b.*,
                       m.title AS movie_title,
                       m.poster AS movie_poster,
                       m.duration AS movie_duration,
                       st.start_time,
                       st.end_time,
                       r.name AS room_name,
                       GROUP_CONCAT(
                           DISTINCT se.seat_number
                           ORDER BY se.row_char, se.col_num
                           SEPARATOR ', '
                       ) AS seat_numbers
                FROM {$this->table} b
                JOIN showtimes st ON st.id = b.showtime_id
                JOIN movies m ON m.id = st.movie_id
                LEFT JOIN rooms r ON r.id = st.room_id
                LEFT JOIN tickets t ON t.booking_id = b.id
                LEFT JOIN seats se ON se.id = t.seat_id
                WHERE b.user_id = :user_id
                GROUP BY b.id, m.title, m.poster, m.duration, st.start_time, st.end_time, r.name
                ORDER BY b.id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
