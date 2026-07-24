<?php
class SeatTypeModel extends BaseModel
{
    public function getAll()
    {
        $sql = "SELECT seat_types.*,
                       (SELECT COUNT(*) FROM seats WHERE seats.seat_type_id = seat_types.id) AS total_seats
                FROM seat_types
                ORDER BY seat_types.id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function add($name, $surcharge, $description)
    {
        $sql = "INSERT INTO seat_types (name, surcharge, description)
                VALUES (:name, :surcharge, :description)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam('name', $name);
        $stmt->bindParam('surcharge', $surcharge);
        $stmt->bindParam('description', $description);
        $stmt->execute();
    }

    public function editById($id)
    {
        $sql = "SELECT * FROM seat_types WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function edit($id, $name, $surcharge, $description)
    {
        $sql = "UPDATE seat_types
                SET name = :name,
                    surcharge = :surcharge,
                    description = :description
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->bindParam('name', $name);
        $stmt->bindParam('surcharge', $surcharge);
        $stmt->bindParam('description', $description);
        $stmt->execute();
    }

    public function countSeats($id)
    {
        $sql = "SELECT COUNT(*) FROM seats WHERE seat_type_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM seat_types WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->execute();
    }
}
?>
