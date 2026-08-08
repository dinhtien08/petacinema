<?php

class DashboardModel extends BaseModel
{
    private function scalar(string $sql, array $params = [], $default = 0)
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $value = $stmt->fetchColumn();

        return ($value !== false && $value !== null) ? $value : $default;
    }

    private function rows(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countUsers(?string $from = null, ?string $to = null): int
    {
        $sql = "SELECT COUNT(*) FROM users";
        $params = [];

        if ($from !== null && $to !== null) {
            $sql .= " WHERE created_at BETWEEN ? AND ?";
            $params = [$from, $to];
        }

        return (int) $this->scalar($sql, $params);
    }

    public function countMovies(): int
    {
        return (int) $this->scalar("SELECT COUNT(*) FROM movies");
    }

    public function countBookings(?string $from = null, ?string $to = null): int
    {
        $sql = "SELECT COUNT(*) FROM bookings";
        $params = [];

        if ($from !== null && $to !== null) {
            $sql .= " WHERE created_at BETWEEN ? AND ?";
            $params = [$from, $to];
        }

        return (int) $this->scalar($sql, $params);
    }

    public function countRooms(): int
    {
        return (int) $this->scalar("SELECT COUNT(*) FROM rooms");
    }

    public function countSeats(): int
    {
        return (int) $this->scalar("SELECT COUNT(*) FROM seats");
    }

    public function countFoods(): int
    {
        return (int) $this->scalar("SELECT COUNT(*) FROM foods");
    }

    public function countPayments(?string $from = null, ?string $to = null): int
    {
        $sql = "SELECT COUNT(*) FROM payments";
        $params = [];

        if ($from !== null && $to !== null) {
            $sql .= " WHERE payment_time BETWEEN ? AND ?";
            $params = [$from, $to];
        }

        return (int) $this->scalar($sql, $params);
    }

    public function countTickets(?string $from = null, ?string $to = null): int
    {
        $sql = "
            SELECT COUNT(t.id)
            FROM tickets t
            INNER JOIN bookings b ON b.id = t.booking_id
            WHERE b.status = 'paid'
        ";
        $params = [];

        if ($from !== null && $to !== null) {
            $sql .= " AND b.created_at BETWEEN ? AND ?";
            $params = [$from, $to];
        }

        return (int) $this->scalar($sql, $params);
    }

    public function countShowtimes(?string $from = null, ?string $to = null): int
    {
        $sql = "SELECT COUNT(*) FROM showtimes";
        $params = [];

        if ($from !== null && $to !== null) {
            $sql .= " WHERE start_time BETWEEN ? AND ?";
            $params = [$from, $to];
        }

        return (int) $this->scalar($sql, $params);
    }

    public function totalRevenue(?string $from = null, ?string $to = null): float
    {
        $sql = "SELECT COALESCE(SUM(total_amount), 0) FROM bookings WHERE status = 'paid'";
        $params = [];

        if ($from !== null && $to !== null) {
            $sql .= " AND created_at BETWEEN ? AND ?";
            $params = [$from, $to];
        }

        return (float) $this->scalar($sql, $params, 0);
    }

    public function bookingStatus(?string $from = null, ?string $to = null): array
    {
        $sql = "SELECT status, COUNT(*) AS total FROM bookings";
        $params = [];

        if ($from !== null && $to !== null) {
            $sql .= " WHERE created_at BETWEEN ? AND ?";
            $params = [$from, $to];
        }

        $sql .= " GROUP BY status";
        return $this->rows($sql, $params);
    }

    public function recentBookings(int $limit = 5, ?string $from = null, ?string $to = null): array
    {
        $limit = max(1, min(50, $limit));
        $where = '';
        $params = [];

        if ($from !== null && $to !== null) {
            $where = 'WHERE b.created_at BETWEEN ? AND ?';
            $params = [$from, $to];
        }

        $sql = "
            SELECT
                b.id,
                b.booking_code,
                b.total_amount,
                b.status,
                b.checkin_status,
                b.created_at,
                COALESCE(u.fullname, 'Khách hàng') AS customer_name,
                COALESCE(m.title, '—') AS movie_title,
                st.start_time AS showtime_at,
                COALESCE(
                    GROUP_CONCAT(
                        DISTINCT se.seat_number
                        ORDER BY se.row_char, se.col_num
                        SEPARATOR ', '
                    ),
                    '—'
                ) AS seat_labels
            FROM bookings b
            LEFT JOIN users u ON u.id = b.user_id
            LEFT JOIN showtimes st ON st.id = b.showtime_id
            LEFT JOIN movies m ON m.id = st.movie_id
            LEFT JOIN tickets t ON t.booking_id = b.id
            LEFT JOIN seats se ON se.id = t.seat_id
            {$where}
            GROUP BY
                b.id,
                b.booking_code,
                b.total_amount,
                b.status,
                b.checkin_status,
                b.created_at,
                u.fullname,
                m.title,
                st.start_time
            ORDER BY b.created_at DESC
            LIMIT {$limit}
        ";

        return $this->rows($sql, $params);
    }

    public function revenueByMonth(int $months = 7): array
    {
        $months = max(1, min(24, $months));
        $start = date('Y-m-01 00:00:00', strtotime('-' . ($months - 1) . ' months'));

        return $this->rows(
            "SELECT
                DATE_FORMAT(created_at, '%Y-%m') AS period,
                COALESCE(SUM(total_amount), 0) AS revenue
             FROM bookings
             WHERE status = 'paid'
               AND created_at >= ?
             GROUP BY DATE_FORMAT(created_at, '%Y-%m')
             ORDER BY period ASC",
            [$start]
        );
    }

    public function revenueByDay(string $from, string $to): array
    {
        return $this->rows(
            "SELECT
                DATE(created_at) AS period,
                COALESCE(SUM(total_amount), 0) AS revenue
             FROM bookings
             WHERE status = 'paid'
               AND created_at BETWEEN ? AND ?
             GROUP BY DATE(created_at)
             ORDER BY period ASC",
            [$from, $to]
        );
    }

    public function earliestBookingDate(): ?string
    {
        $date = $this->scalar("SELECT DATE(MIN(created_at)) FROM bookings", [], null);
        return $date ? (string) $date : null;
    }

    public function topMovies(int $limit = 5): array
    {
        $limit = max(1, min(20, $limit));

        return $this->rows("\n            SELECT
                m.title,
                COUNT(DISTINCT CASE WHEN b.status = 'paid' THEN b.id END) AS total_bookings,
                COUNT(CASE WHEN b.status = 'paid' THEN t.id END) AS total_tickets
            FROM movies m
            LEFT JOIN showtimes st ON st.movie_id = m.id
            LEFT JOIN bookings b ON b.showtime_id = st.id
            LEFT JOIN tickets t ON t.booking_id = b.id
            GROUP BY m.id, m.title
            ORDER BY total_tickets DESC, total_bookings DESC, m.title ASC
            LIMIT {$limit}
        ");
    }
}
