<?php
class UserModel extends BaseModel
{
    protected $table = "users";

    public function getById($id)
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function addUser($fullname, $email, $password, $role, $status, $created_at)
    {
        $sql = "INSERT INTO users (fullname, email, password, role, status, created_at)
                VALUES (:fullname, :email, :password, :role, :status, :created_at)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":fullname", $fullname);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":created_at", $created_at);

        return $stmt->execute();
    }

    public function editUser($id, $fullname, $email, $password, $role, $status)
    {
        $sql = "UPDATE users
                SET fullname = :fullname,
                    email = :email,
                    password = :password,
                    role = :role,
                    status = :status
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":fullname", $fullname);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":status", $status);

        return $stmt->execute();
    }

    public function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function count()
    {
        $sql = "SELECT COUNT(*) FROM users";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function searchAndFilter($keyword = null)
    {
        $sql = "SELECT * FROM users WHERE 1=1";
        if (!empty($keyword)) {
            $sql .= " AND (fullname LIKE :keyword OR email LIKE :keyword)";
        }
        $sql .= " ORDER BY id DESC";

        $stmt = $this->pdo->prepare($sql);
        if (!empty($keyword)) {
            $stmt->bindValue(':keyword', '%' . $keyword . '%');
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
