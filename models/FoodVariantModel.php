<?php

class FoodVariantModel extends BaseModel
{
    protected $table = "food_variants";

    // Toàn bộ biến thể, JOIN foods để biết tên món
    public function getAll()
    {
        $sql = "SELECT v.*, f.name AS food_name
                FROM {$this->table} v
                JOIN foods f ON f.id = v.food_id
                ORDER BY v.id ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Biến thể theo food_id (dùng cho trang "Xem biến thể")
    public function getByFoodId($foodId)
    {
        $sql = "SELECT v.*, f.name AS food_name
                FROM {$this->table} v
                JOIN foods f ON f.id = v.food_id
                WHERE v.food_id = :food_id
                ORDER BY v.id ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':food_id', $foodId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT v.*, f.name AS food_name
                FROM {$this->table} v
                JOIN foods f ON f.id = v.food_id
                WHERE v.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function addVariant($data)
    {
        $sql = "INSERT INTO {$this->table} (food_id, size, price, stock)
                VALUES (:food_id, :size, :price, :stock)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':food_id', $data['food_id'], PDO::PARAM_INT);
        $stmt->bindParam(':size', $data['size']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':stock', $data['stock'], PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function editVariant($id, $data)
    {
        $sql = "UPDATE {$this->table}
                SET size = :size, price = :price, stock = :stock
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':size', $data['size']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':stock', $data['stock'], PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteVariant($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
