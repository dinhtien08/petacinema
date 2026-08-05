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
    public function getMovieList()
    {
        $sql = "SELECT id, title, duration
                FROM movies
                WHERE status <> 'ended'
                ORDER BY title";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
    public function findById($id)
    {
        $sql = "SELECT *
                FROM movies
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':id', $id);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function searchAndFilter($keyword = null, $status = null, $genre = null, $ageRating = null, $sort = 'status')
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (title LIKE :keyword OR genres LIKE :keyword OR director LIKE :keyword OR actors LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        if (!empty($status)) {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }

        if (!empty($genre)) {
            $sql .= " AND genres LIKE :genre";
            $params[':genre'] = '%' . $genre . '%';
        }

        if (!empty($ageRating)) {
            $sql .= " AND age_rating = :age_rating";
            $params[':age_rating'] = $ageRating;
        }

        switch ($sort) {
            case 'release_newest':
                $sql .= " ORDER BY release_date DESC, title ASC";
                break;

            case 'release_oldest':
                $sql .= " ORDER BY release_date ASC, title ASC";
                break;

            case 'title_asc':
                $sql .= " ORDER BY title ASC";
                break;

            case 'title_desc':
                $sql .= " ORDER BY title DESC";
                break;

            case 'status':
            default:
                $sql .= " ORDER BY
                    CASE status
                        WHEN 'now_showing' THEN 1
                        WHEN 'coming_soon' THEN 2
                        WHEN 'ended' THEN 3
                        ELSE 4
                    END ASC,
                    CASE
                        WHEN status = 'coming_soon'
                        THEN release_date
                    END ASC,
                    CASE
                        WHEN status IN ('now_showing', 'ended')
                        THEN release_date
                    END DESC,
                    title ASC";
                break;
        }

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNowShowingMovies()
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'now_showing' ORDER BY release_date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getComingSoonMovies()
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'coming_soon' ORDER BY release_date ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFeaturedMovies()
    {
        $sql = "SELECT * FROM {$this->table} WHERE status IN ('now_showing', 'coming_soon') ORDER BY id DESC LIMIT 5";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
