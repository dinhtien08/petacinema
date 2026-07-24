<?php

class FoodOrderModel extends BaseModel
{
    protected $table = "food_orders";

    // JOIN food_variants + foods + bookings để hiển thị đầy đủ thông tin đơn
    public function getAll()
    {
        $sql = "SELECT o.*,
                       b.booking_code,
                       f.name AS food_name,
                       v.size AS variant_size
                FROM {$this->table} o
                JOIN food_variants v ON v.id = o.food_variant_id
                JOIN foods f ON f.id = v.food_id
                JOIN bookings b ON b.id = o.booking_id
                ORDER BY o.id ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT o.*,
                    b.booking_code,
                    f.name AS food_name,
                    v.size AS variant_size
                FROM {$this->table} o
                JOIN food_variants v ON v.id = o.food_variant_id
                JOIN foods f ON f.id = v.food_id
                JOIN bookings b ON b.id = o.booking_id
                WHERE o.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function addOrder($data)
    {
        $sql = "INSERT INTO {$this->table} (booking_id, food_variant_id, quantity, price_at_booking)
                VALUES (:booking_id, :food_variant_id, :quantity, :price_at_booking)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':booking_id', $data['booking_id'], PDO::PARAM_INT);
        $stmt->bindParam(':food_variant_id', $data['food_variant_id'], PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $data['quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':price_at_booking', $data['price_at_booking']);
        return $stmt->execute();
    }

    public function editOrder($id, $data)
    {
        $sql = "UPDATE {$this->table}
                SET booking_id = :booking_id, food_variant_id = :food_variant_id,
                    quantity = :quantity, price_at_booking = :price_at_booking
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':booking_id', $data['booking_id'], PDO::PARAM_INT);
        $stmt->bindParam(':food_variant_id', $data['food_variant_id'], PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $data['quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':price_at_booking', $data['price_at_booking']);
        return $stmt->execute();
    }

    public function deleteOrder($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
