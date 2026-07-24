<?php

class DashboardModel extends BaseModel
{
    public function countUsers()
    {
        $sql = "SELECT COUNT(*) FROM users";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function countMovies()
    {
        $sql = "SELECT COUNT(*) FROM movies";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function countBookings()
    {
        $sql = "SELECT COUNT(*) FROM bookings";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function countRooms()
    {
        $sql = "SELECT COUNT(*) FROM rooms";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function countFoods()
    {
        $sql = "SELECT COUNT(*) FROM foods";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function totalRevenue()
    {
        $sql = "SELECT SUM(total_amount)
                FROM bookings
                WHERE status='paid'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchColumn() ?? 0;
    }

    public function recentBookings()
{
    $sql = "
        SELECT
            b.booking_code,
            u.fullname AS customer_name,
            m.title AS movie_title,
            CONCAT(
                DATE_FORMAT(s.start_time, '%d/%m/%Y %H:%i'),
                ' - ',
                DATE_FORMAT(s.end_time, '%H:%i')
            ) AS showtime_at,
            GROUP_CONCAT(se.seat_number ORDER BY se.seat_number SEPARATOR ', ') AS seat_labels,
            b.total_amount,
            b.status,
            b.created_at

        FROM bookings b

        LEFT JOIN users u
            ON b.user_id = u.id

        LEFT JOIN showtimes s
            ON b.showtime_id = s.id

        LEFT JOIN movies m
            ON s.movie_id = m.id

        LEFT JOIN tickets t
            ON b.id = t.booking_id

        LEFT JOIN seats se
            ON t.seat_id = se.id

        GROUP BY
            b.id,
            b.booking_code,
            u.fullname,
            m.title,
            s.start_time,
            s.end_time,
            b.total_amount,
            b.status,
            b.created_at

        ORDER BY b.created_at DESC

        LIMIT 5
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function topMovies()
    {
        $sql = "
            SELECT
                movies.title,
                COUNT(bookings.id) AS total
            FROM movies

            LEFT JOIN showtimes
                ON movies.id = showtimes.movie_id

            LEFT JOIN bookings
                ON showtimes.id = bookings.showtime_id

            GROUP BY movies.id

            ORDER BY total DESC

            LIMIT 5
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function bookingStatus()
    {
        $sql = "
            SELECT
                status,
                COUNT(*) total
            FROM bookings
            GROUP BY status
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}