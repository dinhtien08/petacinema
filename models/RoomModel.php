<?php
class RoomModel extends BaseModel
{
    public function getAll()
    {
        $sql = "SELECT rooms.*, room_types.name AS room_type_name
                FROM rooms
                INNER JOIN room_types ON rooms.room_type_id = room_types.id
                ORDER BY rooms.id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRoomTypes()
    {
        $sql = "SELECT * FROM room_types ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function add($room_type_id, $name, $total_seats)
    {
        $sql = "INSERT INTO rooms (room_type_id, name, total_seats)
                VALUES (:room_type_id, :name, :total_seats)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam('room_type_id', $room_type_id);
        $stmt->bindParam('name', $name);
        $stmt->bindParam('total_seats', $total_seats);
        $stmt->execute();
    }

    public function editById($id)
    {
        $sql = "SELECT * FROM rooms WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function edit($id, $room_type_id, $name, $total_seats)
    {
        $sql = "UPDATE rooms
                SET room_type_id = :room_type_id,
                    name = :name,
                    total_seats = :total_seats
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->bindParam('room_type_id', $room_type_id);
        $stmt->bindParam('name', $name);
        $stmt->bindParam('total_seats', $total_seats);
        $stmt->execute();
    }

    public function countSeats($id)
    {
        $sql = "SELECT COUNT(*) FROM seats WHERE room_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function countShowtimes($id)
    {
        $sql = "SELECT COUNT(*) FROM showtimes WHERE room_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM rooms WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->execute();
    }
}
?>
