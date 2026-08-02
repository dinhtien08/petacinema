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
                ORDER BY s.start_time DESC";

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

        $sql .= " ORDER BY s.start_time DESC";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}