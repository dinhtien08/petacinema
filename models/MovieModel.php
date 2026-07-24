<?php
class MovieModel extends BaseModel
{
    protected $table = "movies";
    public function delete($id)
    {
        $sql = "DELETE FROM movies WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);
    }
    public function insert($data)
    {
        $sql = "INSERT INTO movies(
                    title,
                    genres,
                    duration,
                    description,
                    trailer,
                    poster,
                    release_date,
                    language,
                    director,
                    actors,
                    age_rating,
                    status
                )
                VALUES(
                    :title,
                    :genres,
                    :duration,
                    :description,
                    :trailer,
                    :poster,
                    :release_date,
                    :language,
                    :director,
                    :actors,
                    :age_rating,
                    :status
                )";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($data);
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
    public function update($data)
    {
        $sql = "UPDATE movies SET
                    title = :title,
                    genres = :genres,
                    duration = :duration,
                    description = :description,
                    trailer = :trailer,
                    poster = :poster,
                    release_date = :release_date,
                    language = :language,
                    director = :director,
                    actors = :actors,
                    age_rating = :age_rating,
                    status = :status
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($data);
    }
}
