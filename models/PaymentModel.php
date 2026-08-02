<?php
class PaymentModel extends BaseModel
{
    protected $table = "payments";

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }

    public function updateStatus($id, $status)
    {
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':id'     => $id
        ]);
    }

    // Tìm kiếm theo transaction_code + lọc status + lọc payment_method, kết hợp đồng thời
    public function searchFilter($keyword = '', $status = '', $method = '')
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";

        $params = [];

        if ($keyword !== '') {
            $sql .= " AND transaction_code LIKE :keyword";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        if ($status !== '') {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }

        if ($method !== '') {
            $sql .= " AND payment_method = :method";
            $params[':method'] = $method;
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
