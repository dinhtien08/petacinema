<?php
class RoomTypeModel extends BaseModel
{
    public function getAll()
    {
        $sql = "SELECT room_types.*,
                (SELECT COUNT(*) FROM rooms WHERE rooms.room_type_id = room_types.id) AS total_rooms
                FROM room_types
                ORDER BY room_types.id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function add($name, $price_modifier, $description)
    {
        $sql = "INSERT INTO room_types (name, price_modifier, description)
                VALUES (:name, :price_modifier, :description)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam('name', $name);
        $stmt->bindParam('price_modifier', $price_modifier);
        $stmt->bindParam('description', $description);
        $stmt->execute();
    }

    public function editById($id)
    {
        $sql = "SELECT * FROM room_types WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function edit($id, $name, $price_modifier, $description)
    {
        $sql = "UPDATE room_types
                SET name = :name,
                    price_modifier = :price_modifier,
                    description = :description
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->bindParam('name', $name);
        $stmt->bindParam('price_modifier', $price_modifier);
        $stmt->bindParam('description', $description);
        $stmt->execute();
    }

    public function countRooms($id)
    {
        $sql = "SELECT COUNT(*) FROM rooms WHERE room_type_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM room_types WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->execute();
    }
}
?>
