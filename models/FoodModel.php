<?php

class FoodModel extends BaseModel
{
    protected $table = "foods";

    // Danh sách food kèm số biến thể + khoảng giá (JOIN food_variants)
    public function getAll()
    {
        $sql = "SELECT f.*,
                       COUNT(v.id) AS variant_count,
                       MIN(v.price) AS min_price,
                       MAX(v.price) AS max_price
                FROM {$this->table} f
                LEFT JOIN food_variants v ON v.food_id = f.id
                GROUP BY f.id
                ORDER BY f.id ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function addFood($data)
    {
        $sql = "INSERT INTO {$this->table} (name, description, image, status)
                VALUES (:name, :description, :image, :status)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':image', $data['image']);
        $stmt->bindParam(':status', $data['status']);
        return $stmt->execute();
    }

    public function editFood($id, $data)
    {
        $sql = "UPDATE {$this->table}
                SET name = :name, description = :description, image = :image, status = :status
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':image', $data['image']);
        $stmt->bindParam(':status', $data['status']);
        return $stmt->execute();
    }

    public function deleteFood($id)
    {
        // Xóa food_variants liên quan trước khi xóa food
        $sql = "DELETE FROM food_variants WHERE food_id = :food_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':food_id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
