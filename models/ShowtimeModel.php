<?php

class ShowtimeModel extends BaseModel
{
    protected $table = 'showtimes';

    /**
     * Danh sách suất chiếu
     */
    public function getAllShowtimes()
    {
        $sql = "SELECT
                    s.id,
                    m.title AS movie_title,
                    r.name AS room_name,
                    s.start_time,
                    s.end_time,
                    s.base_price
                FROM showtimes s
                INNER JOIN movies m
                    ON s.movie_id = m.id
                INNER JOIN rooms r
                    ON s.room_id = r.id
                ORDER BY
                    CASE
                        WHEN s.end_time >= NOW() THEN 1
                        ELSE 2
                    END ASC,
                    CASE
                        WHEN s.end_time >= NOW() THEN s.start_time
                    END ASC,
                    CASE
                        WHEN s.end_time < NOW() THEN s.start_time
                    END DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Chi tiết suất chiếu
     */
    public function findById($id)
    {
        $sql = "SELECT *
                FROM showtimes
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm suất chiếu
     */
    public function insert($data)
    {
        $sql = "INSERT INTO showtimes
                (movie_id, room_id, start_time, end_time, base_price)
                VALUES
                (:movie_id, :room_id, :start_time, :end_time, :base_price)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':movie_id', $data['movie_id'], PDO::PARAM_INT);
        $stmt->bindParam(':room_id', $data['room_id'], PDO::PARAM_INT);
        $stmt->bindParam(':start_time', $data['start_time']);
        $stmt->bindParam(':end_time', $data['end_time']);
        $stmt->bindParam(':base_price', $data['base_price']);

        return $stmt->execute();
    }

    /**
     * Cập nhật suất chiếu
     */
    public function update($id, $data)
    {
        $sql = "UPDATE showtimes
                SET
                    movie_id = :movie_id,
                    room_id = :room_id,
                    start_time = :start_time,
                    end_time = :end_time,
                    base_price = :base_price
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':movie_id', $data['movie_id'], PDO::PARAM_INT);
        $stmt->bindParam(':room_id', $data['room_id'], PDO::PARAM_INT);
        $stmt->bindParam(':start_time', $data['start_time']);
        $stmt->bindParam(':end_time', $data['end_time']);
        $stmt->bindParam(':base_price', $data['base_price']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Xóa suất chiếu
     */
    /**
     * Kiểm tra trùng lịch phòng
     */
    public function checkConflict($roomId, $startTime, $endTime)
    {
        $sql = "SELECT COUNT(*)
                FROM showtimes
                WHERE room_id = :room_id
                AND start_time < :end_time
                AND end_time > :start_time";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':room_id', $roomId, PDO::PARAM_INT);
        $stmt->bindParam(':start_time', $startTime);
        $stmt->bindParam(':end_time', $endTime);

        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Kiểm tra trùng lịch khi cập nhật
     */
    public function checkConflictExcept($id, $roomId, $startTime, $endTime)
    {
        $sql = "SELECT COUNT(*)
                FROM showtimes
                WHERE room_id = :room_id
                AND id <> :id
                AND start_time < :end_time
                AND end_time > :start_time";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':room_id', $roomId, PDO::PARAM_INT);
        $stmt->bindParam(':start_time', $startTime);
        $stmt->bindParam(':end_time', $endTime);

        $stmt->execute();

        return $stmt->fetchColumn();
    }

   
    public function getDetail($id)
    {
        $sql = "SELECT
                    s.*,
                    m.title AS movie_title,
                    m.poster,
                    m.duration,
                    m.language,
                    m.age_rating,
                    m.genres,
                    m.director,
                    m.release_date,
                    r.name AS room_name,
                    rt.name AS room_type
                FROM showtimes s
                INNER JOIN movies m
                    ON s.movie_id = m.id
                INNER JOIN rooms r
                    ON s.room_id = r.id
                INNER JOIN room_types rt
                    ON r.room_type_id = rt.id
                WHERE s.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->rowCount() > 0;
    }
     /**
     * Kiểm tra đã có booking chưa
     */
    public function hasBooking($showtimeId)
    {
        $sql = "
            SELECT COUNT(*)
            FROM bookings
            WHERE showtime_id = :showtime_id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(
            ':showtime_id',
            (int) $showtimeId,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    public function searchAndFilter($keyword = null, $movieId = null, $roomId = null, $status = null, $date = null)
    {
        $sql = "SELECT
                    s.id,
                    s.movie_id,
                    s.room_id,
                    m.title AS movie_title,
                    r.name AS room_name,
                    r.total_seats,
                    s.start_time,
                    s.end_time,
                    s.base_price,
                    (
                        SELECT COUNT(DISTINCT t.seat_id)
                        FROM tickets t
                        JOIN bookings b ON t.booking_id = b.id
                        WHERE b.showtime_id = s.id
                          AND b.status IN ('pending', 'paid')
                    ) AS booked_seats
                FROM showtimes s
                INNER JOIN movies m
                    ON s.movie_id = m.id
                INNER JOIN rooms r
                    ON s.room_id = r.id
                WHERE 1=1";

        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND m.title LIKE :keyword";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        if (!empty($movieId)) {
            $sql .= " AND s.movie_id = :movie_id";
            $params[':movie_id'] = (int)$movieId;
        }

        if (!empty($roomId)) {
            $sql .= " AND s.room_id = :room_id";
            $params[':room_id'] = (int)$roomId;
        }

        if (!empty($status)) {
            if ($status === 'upcoming') {
                $sql .= " AND s.start_time > NOW()";
            } elseif ($status === 'showing') {
                $sql .= " AND s.start_time <= NOW() AND s.end_time >= NOW()";
            } elseif ($status === 'ended') {
                $sql .= " AND s.end_time < NOW()";
            }
        }

        if (!empty($date)) {
            $sql .= " AND DATE(s.start_time) = :date";
            $params[':date'] = $date;
        }

        $sql .= " ORDER BY
            CASE
                WHEN s.end_time >= NOW() THEN 1
                ELSE 2
            END ASC,
            CASE
                WHEN s.end_time >= NOW() THEN s.start_time
            END ASC,
            CASE
                WHEN s.end_time < NOW() THEN s.start_time
            END DESC";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách booking theo suất chiếu kèm số ghế đã đặt
     */
    public function getBookingsByShowtime($showtimeId)
    {
        $sql = "SELECT
                    b.id,
                    b.booking_code,
                    b.user_id,
                    b.total_amount,
                    b.status,
                    b.created_at,
                    u.fullname AS customer_name,
                    u.email AS customer_email,
                    GROUP_CONCAT(se.seat_number ORDER BY se.seat_number SEPARATOR ', ') AS seat_labels,
                    COUNT(t.id) AS ticket_count
                FROM bookings b
                LEFT JOIN users u
                    ON b.user_id = u.id
                LEFT JOIN tickets t
                    ON b.id = t.booking_id
                LEFT JOIN seats se
                    ON t.seat_id = se.id
                WHERE b.showtime_id = :showtime_id
                GROUP BY
                    b.id,
                    b.booking_code,
                    b.user_id,
                    b.total_amount,
                    b.status,
                    b.created_at,
                    u.fullname,
                    u.email
                ORDER BY b.id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':showtime_id', (int) $showtimeId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy các ngày chiếu còn hiệu lực của một phim
     */
    public function getAvailableDatesByMovie($movieId)
    {
        $sql = "SELECT DISTINCT DATE(start_time) AS show_date
                FROM showtimes
                WHERE movie_id = :movie_id
                  AND end_time >= NOW()
                ORDER BY show_date ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':movie_id', (int) $movieId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Lấy các suất chiếu còn hiệu lực của một phim theo ngày đã chọn
     */
    public function getValidShowtimesByMovieAndDate($movieId, $date)
    {
        $sql = "SELECT 
                    s.id,
                    s.movie_id,
                    s.room_id,
                    s.start_time,
                    s.end_time,
                    s.base_price,
                    r.name AS room_name,
                    rt.name AS room_type_name
                FROM showtimes s
                INNER JOIN rooms r ON s.room_id = r.id
                INNER JOIN room_types rt ON r.room_type_id = rt.id
                WHERE s.movie_id = :movie_id
                  AND DATE(s.start_time) = :date
                  AND s.end_time >= NOW()
                ORDER BY s.start_time ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':movie_id', (int) $movieId, PDO::PARAM_INT);
        $stmt->bindValue(':date', $date);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
