<?php

class RoomModel extends BaseModel
{
    public function getAll()
    {
        $sql = "SELECT
                    rooms.*,
                    room_types.name AS room_type_name,
                    COUNT(seats.id) AS generated_seats,

                    COALESCE(
                        SUM(
                            CASE
                                WHEN seats.status = 'available' THEN 1
                                ELSE 0
                            END
                        ),
                        0
                    ) AS available_seats,

                    COALESCE(
                        SUM(
                            CASE
                                WHEN seats.status = 'maintenance' THEN 1
                                ELSE 0
                            END
                        ),
                        0
                    ) AS maintenance_seats

                FROM rooms

                INNER JOIN room_types
                    ON rooms.room_type_id = room_types.id

                LEFT JOIN seats
                    ON rooms.id = seats.room_id

                GROUP BY
                    rooms.id,
                    rooms.room_type_id,
                    rooms.name,
                    rooms.total_seats,
                    room_types.name

                ORDER BY rooms.id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getRoomTypes()
    {
        $sql = "SELECT *
                FROM room_types
                ORDER BY id ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getRoomTypeById($id)
    {
        $sql = "SELECT *
                FROM room_types
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function getRoomTypeByName($name)
    {
        $sql = "SELECT *
                FROM room_types
                WHERE LOWER(name) = LOWER(:name)
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':name', trim($name), PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function isNameExists($name, $excludeId = null)
    {
        $name = trim($name);

        if ($excludeId !== null) {
            $sql = "SELECT COUNT(*)
                    FROM rooms
                    WHERE LOWER(name) = LOWER(:name)
                      AND id != :exclude_id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(
                ':exclude_id',
                (int) $excludeId,
                PDO::PARAM_INT
            );
        } else {
            $sql = "SELECT COUNT(*)
                    FROM rooms
                    WHERE LOWER(name) = LOWER(:name)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        }

        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    public function add(
        $roomTypeId,
        $name,
        $totalSeats,
        $pdo = null
    ) {
        $db = $pdo ?? $this->pdo;

        $sql = "INSERT INTO rooms (
                    room_type_id,
                    name,
                    total_seats
                )
                VALUES (
                    :room_type_id,
                    :name,
                    :total_seats
                )";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(
            ':room_type_id',
            (int) $roomTypeId,
            PDO::PARAM_INT
        );
        $stmt->bindValue(
            ':name',
            trim($name),
            PDO::PARAM_STR
        );
        $stmt->bindValue(
            ':total_seats',
            (int) $totalSeats,
            PDO::PARAM_INT
        );
        $stmt->execute();

        return (int) $db->lastInsertId();
    }

    public function editById($id)
    {
        $sql = "SELECT
                    rooms.*,
                    room_types.name AS room_type_name
                FROM rooms
                INNER JOIN room_types
                    ON rooms.room_type_id = room_types.id
                WHERE rooms.id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function edit(
        $id,
        $roomTypeId,
        $name,
        $totalSeats,
        $pdo = null
    ) {
        $db = $pdo ?? $this->pdo;

        $sql = "UPDATE rooms
                SET room_type_id = :room_type_id,
                    name = :name,
                    total_seats = :total_seats
                WHERE id = :id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->bindValue(
            ':room_type_id',
            (int) $roomTypeId,
            PDO::PARAM_INT
        );
        $stmt->bindValue(
            ':name',
            trim($name),
            PDO::PARAM_STR
        );
        $stmt->bindValue(
            ':total_seats',
            (int) $totalSeats,
            PDO::PARAM_INT
        );
        $stmt->execute();

        return $stmt->rowCount() >= 0;
    }

    public function getDetail($id)
    {
        return $this->editById($id);
    }

    public static function allowedCapacities()
    {
        return [60, 80, 100, 120, 200];
    }

    public function countSeats($id, $pdo = null)
    {
        $db = $pdo ?? $this->pdo;

        $sql = "SELECT COUNT(*)
                FROM seats
                WHERE room_id = :room_id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(
            ':room_id',
            (int) $id,
            PDO::PARAM_INT
        );
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function countShowtimes($id, $pdo = null)
    {
        $db = $pdo ?? $this->pdo;

        $sql = "SELECT COUNT(*)
                FROM showtimes
                WHERE room_id = :room_id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(
            ':room_id',
            (int) $id,
            PDO::PARAM_INT
        );
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function countTickets($id, $pdo = null)
    {
        $db = $pdo ?? $this->pdo;

        $sql = "SELECT COUNT(*)
                FROM tickets AS t
                INNER JOIN seats AS s
                    ON t.seat_id = s.id
                WHERE s.room_id = :room_id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(
            ':room_id',
            (int) $id,
            PDO::PARAM_INT
        );
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function delete($id, $pdo = null)
    {
        $db = $pdo ?? $this->pdo;

        $sql = "DELETE FROM rooms
                WHERE id = :id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getRoomList()
    {
        $sql = "SELECT id, name
                FROM rooms
                ORDER BY name ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getPdo()
    {
        return $this->pdo;
    }
}