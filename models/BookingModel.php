<?php

class BookingModel extends BaseModel
{
    protected $table = "bookings";

    // JOIN users + showtimes + movies để hiển thị đầy đủ thông tin booking
    public function getAll()
    {
        $sql = "SELECT b.*,
                       u.fullname AS customer_name,
                       u.email AS customer_email,
                       m.title AS movie_title,
                       s.start_time,
                       s.end_time
                FROM {$this->table} b
                JOIN users u ON u.id = b.user_id
                JOIN showtimes s ON s.id = b.showtime_id
                JOIN movies m ON m.id = s.movie_id
                ORDER BY b.id ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT b.*,
                u.fullname AS customer_name,
                u.email AS customer_email,
                m.title AS movie_title,
                s.start_time,
                s.end_time
                FROM {$this->table} b
                JOIN users u ON u.id = b.user_id
                JOIN showtimes s ON s.id = b.showtime_id
                JOIN movies m ON m.id = s.movie_id
                WHERE b.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
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
