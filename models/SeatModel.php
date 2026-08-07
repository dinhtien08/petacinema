<?php

class SeatModel extends BaseModel
{
    private $seatTypeCache = [];

    public function getSeatTypeIdByName(
        $name,
        $pdo = null
    ) {
        $db = $pdo ?? $this->pdo;
        $cacheKey = strtolower(trim($name));

        if (!array_key_exists($cacheKey, $this->seatTypeCache)) {
            $sql = "SELECT id
                    FROM seat_types
                    WHERE LOWER(name) = LOWER(:name)
                    LIMIT 1";

            $stmt = $db->prepare($sql);
            $stmt->bindValue(
                ':name',
                trim($name),
                PDO::PARAM_STR
            );
            $stmt->execute();

            $seatTypeId = $stmt->fetchColumn();

            $this->seatTypeCache[$cacheKey] = $seatTypeId
                ? (int) $seatTypeId
                : null;
        }

        return $this->seatTypeCache[$cacheKey];
    }

    public function getByRoomId(
        $roomId,
        $pdo = null
    ) {
        $db = $pdo ?? $this->pdo;

        $sql = "SELECT
                    seats.*,
                    seat_types.name AS seat_type_name
                FROM seats
                INNER JOIN seat_types
                    ON seats.seat_type_id = seat_types.id
                WHERE seats.room_id = :room_id
                ORDER BY
                    seats.row_char ASC,
                    seats.col_num ASC";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(
            ':room_id',
            (int) $roomId,
            PDO::PARAM_INT
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countByRoomId(
        $roomId,
        $pdo = null
    ) {
        $db = $pdo ?? $this->pdo;

        $sql = "SELECT COUNT(*)
                FROM seats
                WHERE room_id = :room_id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(
            ':room_id',
            (int) $roomId,
            PDO::PARAM_INT
        );
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function findById(
        $id,
        $pdo = null
    ) {
        $db = $pdo ?? $this->pdo;

        $sql = "SELECT
                    seats.*,
                    seat_types.name AS seat_type_name
                FROM seats
                INNER JOIN seat_types
                    ON seats.seat_type_id = seat_types.id
                WHERE seats.id = :id
                LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function hasTicketsForRoom(
        $roomId,
        $pdo = null
    ) {
        $db = $pdo ?? $this->pdo;

        $sql = "SELECT COUNT(*)
                FROM tickets AS t
                INNER JOIN seats AS s
                    ON t.seat_id = s.id
                WHERE s.room_id = :room_id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(
            ':room_id',
            (int) $roomId,
            PDO::PARAM_INT
        );
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    public function hasTicketsForSeat(
        $seatId,
        $pdo = null
    ) {
        $db = $pdo ?? $this->pdo;

        $sql = "SELECT COUNT(*)
                FROM tickets
                WHERE seat_id = :seat_id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(
            ':seat_id',
            (int) $seatId,
            PDO::PARAM_INT
        );
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    public function getLayoutConfig(
        $roomTypeName,
        $capacity
    ) {
        $roomTypeName = trim($roomTypeName);
        $capacity = (int) $capacity;

        if (strcasecmp($roomTypeName, 'Gold Class') === 0) {
            if ($capacity !== 60) {
                return null;
            }

            return [
                'rows' => 6,
                'cols' => 10,
            ];
        }

        $layouts = [
            60 => [
                'rows' => 6,
                'cols' => 10,
            ],
            80 => [
                'rows' => 8,
                'cols' => 10,
            ],
            100 => [
                'rows' => 10,
                'cols' => 10,
            ],
            120 => [
                'rows' => 10,
                'cols' => 12,
            ],
            200 => [
                'rows' => 10,
                'cols' => 20,
            ],
        ];

        return $layouts[$capacity] ?? null;
    }

    public function generateForRoom(
        $roomId,
        $roomTypeName,
        $capacity,
        $pdo = null
    ) {
        $db = $pdo ?? $this->pdo;
        $roomId = (int) $roomId;
        $capacity = (int) $capacity;

        if ($roomId <= 0) {
            return [
                'success' => false,
                'message' => 'ID phòng không hợp lệ.',
            ];
        }

        if ($this->countByRoomId($roomId, $db) > 0) {
            return [
                'success' => false,
                'message' => 'Phòng đã có ghế.',
            ];
        }

        if ($this->hasTicketsForRoom($roomId, $db)) {
            return [
                'success' => false,
                'message' => 'Phòng đã có vé bán, không thể sinh ghế.',
            ];
        }

        $layout = $this->getLayoutConfig(
            $roomTypeName,
            $capacity
        );

        if (!$layout) {
            return [
                'success' => false,
                'message' => 'Cấu hình layout không hợp lệ cho sức chứa '
                    . $capacity
                    . ' ghế.',
            ];
        }

        $standardId = $this->getSeatTypeIdByName(
            'Standard',
            $db
        );

        $vipId = $this->getSeatTypeIdByName(
            'VIP',
            $db
        );

        $coupleId = $this->getSeatTypeIdByName(
            'Couple',
            $db
        );

        if (!$standardId || !$vipId || !$coupleId) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy đủ loại ghế Standard, VIP và Couple trong cơ sở dữ liệu.',
            ];
        }

        $rows = (int) $layout['rows'];
        $cols = (int) $layout['cols'];

        $isGoldClass = strcasecmp(
            trim($roomTypeName),
            'Gold Class'
        ) === 0;

        $isOwnTransaction = false;

        try {
            if (!$db->inTransaction()) {
                $db->beginTransaction();
                $isOwnTransaction = true;
            }

            $sql = "INSERT INTO seats (
                        room_id,
                        seat_type_id,
                        seat_number,
                        row_char,
                        col_num,
                        couple_group,
                        status
                    )
                    VALUES (
                        :room_id,
                        :seat_type_id,
                        :seat_number,
                        :row_char,
                        :col_num,
                        :couple_group,
                        'available'
                    )";

            $stmt = $db->prepare($sql);

            for ($rowIndex = 0; $rowIndex < $rows; $rowIndex++) {
                $rowChar = chr(65 + $rowIndex);

                if ($isGoldClass) {
                    $seatTypeId = $coupleId;
                    $isCoupleRow = true;
                } elseif ($rowIndex < 3) {
                    $seatTypeId = $standardId;
                    $isCoupleRow = false;
                } elseif ($rowIndex === $rows - 1) {
                    $seatTypeId = $coupleId;
                    $isCoupleRow = true;
                } else {
                    $seatTypeId = $vipId;
                    $isCoupleRow = false;
                }

                for ($column = 1; $column <= $cols; $column++) {
                    $seatNumber = $rowChar . $column;
                    $coupleGroup = null;

                    if ($isCoupleRow) {
                        $pairIndex = (int) ceil($column / 2);

                        $coupleGroup = sprintf(
                            'ROOM_%d_%s_PAIR_%d',
                            $roomId,
                            $rowChar,
                            $pairIndex
                        );
                    }

                    $stmt->bindValue(
                        ':room_id',
                        $roomId,
                        PDO::PARAM_INT
                    );

                    $stmt->bindValue(
                        ':seat_type_id',
                        (int) $seatTypeId,
                        PDO::PARAM_INT
                    );

                    $stmt->bindValue(
                        ':seat_number',
                        $seatNumber,
                        PDO::PARAM_STR
                    );

                    $stmt->bindValue(
                        ':row_char',
                        $rowChar,
                        PDO::PARAM_STR
                    );

                    $stmt->bindValue(
                        ':col_num',
                        $column,
                        PDO::PARAM_INT
                    );

                    if ($coupleGroup !== null) {
                        $stmt->bindValue(
                            ':couple_group',
                            $coupleGroup,
                            PDO::PARAM_STR
                        );
                    } else {
                        $stmt->bindValue(
                            ':couple_group',
                            null,
                            PDO::PARAM_NULL
                        );
                    }

                    $stmt->execute();
                }
            }

            $actualSeats = $this->countByRoomId(
                $roomId,
                $db
            );

            if ($actualSeats !== $capacity) {
                throw new RuntimeException(
                    'Số ghế được sinh không khớp sức chứa. '
                    . 'Đã sinh '
                    . $actualSeats
                    . '/'
                    . $capacity
                    . ' ghế.'
                );
            }

            if ($isOwnTransaction) {
                $db->commit();
            }

            return [
                'success' => true,
                'message' => 'Sinh ' . $actualSeats . ' ghế thành công.',
            ];
        } catch (Throwable $e) {
            if ($isOwnTransaction && $db->inTransaction()) {
                $db->rollBack();
            }

            return [
                'success' => false,
                'message' => 'Sinh ghế thất bại: ' . $e->getMessage(),
            ];
        }
    }

    public function deleteByRoomId(
        $roomId,
        $pdo = null
    ) {
        $db = $pdo ?? $this->pdo;

        $sql = "DELETE FROM seats
                WHERE room_id = :room_id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(
            ':room_id',
            (int) $roomId,
            PDO::PARAM_INT
        );
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function regenerateForRoom(
        $roomId,
        $roomTypeName,
        $capacity,
        $pdo = null
    ) {
        $db = $pdo ?? $this->pdo;
        $isOwnTransaction = false;

        if ($this->hasTicketsForRoom($roomId, $db)) {
            return [
                'success' => false,
                'message' => 'Phòng đã có vé bán, không thể sinh lại ghế.',
            ];
        }

        try {
            if (!$db->inTransaction()) {
                $db->beginTransaction();
                $isOwnTransaction = true;
            }

            $this->deleteByRoomId($roomId, $db);

            $result = $this->generateForRoom(
                $roomId,
                $roomTypeName,
                $capacity,
                $db
            );

            if (!$result['success']) {
                throw new RuntimeException($result['message']);
            }

            if ($isOwnTransaction) {
                $db->commit();
            }

            return [
                'success' => true,
                'message' => 'Sinh lại ghế thành công.',
            ];
        } catch (Throwable $e) {
            if ($isOwnTransaction && $db->inTransaction()) {
                $db->rollBack();
            }

            return [
                'success' => false,
                'message' => 'Sinh lại ghế thất bại: '
                    . $e->getMessage(),
            ];
        }
    }

    public function toggleStatus($seatId)
    {
        $seatId = (int) $seatId;

        if ($seatId <= 0) {
            return [
                'success' => false,
                'message' => 'ID ghế không hợp lệ.',
            ];
        }

        $seat = $this->findById($seatId);

        if (!$seat) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy ghế trong cơ sở dữ liệu.',
            ];
        }

        $newStatus = $seat['status'] === 'available'
            ? 'maintenance'
            : 'available';

        try {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            $this->pdo->beginTransaction();

            if (!empty($seat['couple_group'])) {
                $sql = "UPDATE seats
                        SET status = :status
                        WHERE room_id = :room_id
                        AND couple_group = :couple_group";

                $stmt = $this->pdo->prepare($sql);

                $stmt->bindValue(
                    ':status',
                    $newStatus,
                    PDO::PARAM_STR
                );

                $stmt->bindValue(
                    ':room_id',
                    (int) $seat['room_id'],
                    PDO::PARAM_INT
                );

                $stmt->bindValue(
                    ':couple_group',
                    $seat['couple_group'],
                    PDO::PARAM_STR
                );

                $stmt->execute();

                $affectedRows = $stmt->rowCount();

                if ($affectedRows === 0) {
                    throw new RuntimeException(
                        'Không có ghế Couple nào được cập nhật.'
                    );
                }

                $fetchSql = "SELECT
                                id,
                                seat_number,
                                status,
                                couple_group
                            FROM seats
                            WHERE room_id = :room_id
                            AND couple_group = :couple_group
                            ORDER BY col_num ASC";

                $fetchStmt = $this->pdo->prepare($fetchSql);

                $fetchStmt->bindValue(
                    ':room_id',
                    (int) $seat['room_id'],
                    PDO::PARAM_INT
                );

                $fetchStmt->bindValue(
                    ':couple_group',
                    $seat['couple_group'],
                    PDO::PARAM_STR
                );

                $fetchStmt->execute();

                $updatedSeats = $fetchStmt->fetchAll();
            } else {
                $sql = "UPDATE seats
                        SET status = :status
                        WHERE id = :id";

                $stmt = $this->pdo->prepare($sql);

                $stmt->bindValue(
                    ':status',
                    $newStatus,
                    PDO::PARAM_STR
                );

                $stmt->bindValue(
                    ':id',
                    $seatId,
                    PDO::PARAM_INT
                );

                $stmt->execute();

                $affectedRows = $stmt->rowCount();

                if ($affectedRows === 0) {
                    throw new RuntimeException(
                        'Không có ghế nào được cập nhật.'
                    );
                }

                $fetchSql = "SELECT
                                id,
                                seat_number,
                                status,
                                couple_group
                            FROM seats
                            WHERE id = :id
                            LIMIT 1";

                $fetchStmt = $this->pdo->prepare($fetchSql);

                $fetchStmt->bindValue(
                    ':id',
                    $seatId,
                    PDO::PARAM_INT
                );

                $fetchStmt->execute();

                $updatedSeat = $fetchStmt->fetch();

                if (!$updatedSeat) {
                    throw new RuntimeException(
                        'Không thể đọc lại dữ liệu ghế sau khi cập nhật.'
                    );
                }

                $updatedSeats = [$updatedSeat];
            }

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Đã cập nhật '
                    . count($updatedSeats)
                    . ' ghế sang trạng thái '
                    . $newStatus
                    . '.',
                'seats' => $updatedSeats,
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return [
                'success' => false,
                'message' => 'Cập nhật trạng thái thất bại: '
                    . $e->getMessage(),
            ];
        }
    }

    public function updateStatusByCoupleGroup(
        $roomId,
        $coupleGroup,
        $status,
        $pdo = null
    ) {
        $allowedStatuses = [
            'available',
            'maintenance',
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            throw new InvalidArgumentException(
                'Trạng thái ghế không hợp lệ.'
            );
        }

        $db = $pdo ?? $this->pdo;

        $sql = "UPDATE seats
                SET status = :status
                WHERE room_id = :room_id
                  AND couple_group = :couple_group";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(
            ':status',
            $status,
            PDO::PARAM_STR
        );
        $stmt->bindValue(
            ':room_id',
            (int) $roomId,
            PDO::PARAM_INT
        );
        $stmt->bindValue(
            ':couple_group',
            trim($coupleGroup),
            PDO::PARAM_STR
        );
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function getSeatsForShowtime(
        $showtimeId,
        $roomId,
        $pdo = null
    ) {
        $db = $pdo ?? $this->pdo;

        $sql = "SELECT
                    s.id,
                    s.room_id,
                    s.seat_type_id,
                    s.seat_number,
                    s.row_char,
                    s.col_num,
                    s.couple_group,
                    s.status AS physical_status,
                    st.name AS seat_type_name,
                    st.name AS seat_type,

                    CASE
                        WHEN s.status = 'maintenance'
                        THEN 'maintenance'

                        WHEN booked.seat_id IS NOT NULL
                        THEN 'booked'

                        ELSE 'available'
                    END AS display_status

                FROM seats AS s

                INNER JOIN seat_types AS st
                    ON s.seat_type_id = st.id

                LEFT JOIN (
                    SELECT DISTINCT t.seat_id
                    FROM tickets AS t

                    INNER JOIN bookings AS b
                        ON t.booking_id = b.id

                    WHERE b.showtime_id = :showtime_id
                      AND (
                          b.status = 'paid'
                          OR (
                              b.status = 'pending'
                              AND b.created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
                          )
                      )
                ) AS booked
                    ON booked.seat_id = s.id

                WHERE s.room_id = :room_id

                ORDER BY
                    s.row_char ASC,
                    s.col_num ASC";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':showtime_id', (int) $showtimeId, PDO::PARAM_INT);
        $stmt->bindValue(':room_id', (int) $roomId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}