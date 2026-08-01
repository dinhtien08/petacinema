<?php

class RoomController
{
    public $roomModel;
    public $seatModel;

    public function __construct()
    {
        $this->roomModel = new RoomModel();
        $this->seatModel = new SeatModel();
    }

    private function redirect($action, $parameters = [])
    {
        $query = array_merge(
            ['action' => $action],
            $parameters
        );

        header(
            'Location: '
            . BASE_URL
            . '?'
            . http_build_query($query)
        );

        exit;
    }

    private function isGoldClass($roomTypeName)
    {
        return strcasecmp(
            trim($roomTypeName),
            'Gold Class'
        ) === 0;
    }

    public function list()
    {
        $listRoom = $this->roomModel->getAll();

        $view = PATH_VIEW_ADMIN . 'rooms/list.php';

        require_once PATH_VIEW_ADMIN
            . 'layout/layout.php';
    }

    public function add()
    {
        $listRoomType = $this->roomModel->getRoomTypes();

        $old = $_SESSION['old_input'] ?? [];

        unset($_SESSION['old_input']);

        $view = PATH_VIEW_ADMIN . 'rooms/add.php';

        require_once PATH_VIEW_ADMIN
            . 'layout/layout.php';
    }

    public function addProcess()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Phương thức gửi dữ liệu không hợp lệ.';
            $this->redirect('roomAdd');
        }

        $name = trim($_POST['name'] ?? '');

        $roomTypeId = filter_input(
            INPUT_POST,
            'room_type_id',
            FILTER_VALIDATE_INT
        );

        $totalSeats = filter_input(
            INPUT_POST,
            'total_seats',
            FILTER_VALIDATE_INT
        );

        $_SESSION['old_input'] = [
            'name' => $name,
            'room_type_id' => $roomTypeId,
            'total_seats' => $totalSeats,
        ];

        if ($name === '') {
            $_SESSION['error'] = 'Tên phòng không được để trống.';
            $this->redirect('roomAdd');
        }

        if (mb_strlen($name) > 100) {
            $_SESSION['error'] = 'Tên phòng không được vượt quá 100 ký tự.';
            $this->redirect('roomAdd');
        }

        if ($this->roomModel->isNameExists($name)) {
            $_SESSION['error'] = 'Tên phòng đã tồn tại. Vui lòng chọn tên khác.';
            $this->redirect('roomAdd');
        }

        if (!$roomTypeId || $roomTypeId <= 0) {
            $_SESSION['error'] = 'Vui lòng chọn loại phòng hợp lệ.';
            $this->redirect('roomAdd');
        }

        $roomType = $this->roomModel->getRoomTypeById(
            $roomTypeId
        );

        if (!$roomType) {
            $_SESSION['error'] = 'Loại phòng không tồn tại.';
            $this->redirect('roomAdd');
        }

        if (
            !$totalSeats
            || !in_array(
                $totalSeats,
                RoomModel::allowedCapacities(),
                true
            )
        ) {
            $_SESSION['error'] =
                'Sức chứa không hợp lệ. Hệ thống chỉ hỗ trợ '
                . implode(
                    ', ',
                    RoomModel::allowedCapacities()
                )
                . ' ghế.';

            $this->redirect('roomAdd');
        }

        if (
            $this->isGoldClass($roomType['name'])
            && $totalSeats !== 60
        ) {
            $_SESSION['error'] =
                'Phòng Gold Class bắt buộc có 60 ghế Couple.';

            $this->redirect('roomAdd');
        }

        $pdo = $this->roomModel->getPdo();

        try {
            $pdo->beginTransaction();

            $roomId = $this->roomModel->add(
                $roomTypeId,
                $name,
                $totalSeats,
                $pdo
            );

            $generateResult =
                $this->seatModel->generateForRoom(
                    $roomId,
                    $roomType['name'],
                    $totalSeats,
                    $pdo
                );

            if (!$generateResult['success']) {
                throw new RuntimeException(
                    $generateResult['message']
                );
            }

            $pdo->commit();

            unset($_SESSION['old_input']);

            $_SESSION['success'] =
                'Thêm phòng và sinh '
                . $totalSeats
                . ' ghế thành công.';

            $this->redirect('rooms');
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log(
                'RoomController::addProcess: '
                . $e->getMessage()
            );

            $_SESSION['error'] =
                'Không thể tạo phòng. '
                . $e->getMessage();

            $this->redirect('roomAdd');
        }
    }

    public function edit()
    {
        $id = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$id || $id <= 0) {
            $_SESSION['error'] = 'ID phòng không hợp lệ.';
            $this->redirect('rooms');
        }

        $room = $this->roomModel->editById($id);

        if (!$room) {
            $_SESSION['error'] = 'Không tìm thấy phòng chiếu.';
            $this->redirect('rooms');
        }

        $listRoomType =
            $this->roomModel->getRoomTypes();

        $hasSeats =
            $this->roomModel->countSeats($id) > 0;

        $hasShowtimes =
            $this->roomModel->countShowtimes($id) > 0;

        $hasTickets =
            $this->roomModel->countTickets($id) > 0;

        $old = $_SESSION['old_input'] ?? $room;

        unset($_SESSION['old_input']);

        $view = PATH_VIEW_ADMIN . 'rooms/edit.php';

        require_once PATH_VIEW_ADMIN
            . 'layout/layout.php';
    }

    public function editProcess()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Phương thức gửi dữ liệu không hợp lệ.';
            $this->redirect('rooms');
        }

        $id = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$id || $id <= 0) {
            $_SESSION['error'] = 'ID phòng không hợp lệ.';
            $this->redirect('rooms');
        }

        $room = $this->roomModel->editById($id);

        if (!$room) {
            $_SESSION['error'] = 'Không tìm thấy phòng chiếu.';
            $this->redirect('rooms');
        }

        $name = trim($_POST['name'] ?? '');

        $roomTypeId = filter_input(
            INPUT_POST,
            'room_type_id',
            FILTER_VALIDATE_INT
        );

        $totalSeats = filter_input(
            INPUT_POST,
            'total_seats',
            FILTER_VALIDATE_INT
        );

        $_SESSION['old_input'] = [
            'name' => $name,
            'room_type_id' => $roomTypeId,
            'total_seats' => $totalSeats,
        ];

        if ($name === '') {
            $_SESSION['error'] =
                'Tên phòng không được để trống.';

            $this->redirect(
                'roomEdit',
                ['id' => $id]
            );
        }

        if (mb_strlen($name) > 100) {
            $_SESSION['error'] =
                'Tên phòng không được vượt quá 100 ký tự.';

            $this->redirect(
                'roomEdit',
                ['id' => $id]
            );
        }

        if (
            $this->roomModel->isNameExists(
                $name,
                $id
            )
        ) {
            $_SESSION['error'] =
                'Tên phòng đã bị trùng với phòng khác.';

            $this->redirect(
                'roomEdit',
                ['id' => $id]
            );
        }

        if (!$roomTypeId || $roomTypeId <= 0) {
            $_SESSION['error'] =
                'Loại phòng không hợp lệ.';

            $this->redirect(
                'roomEdit',
                ['id' => $id]
            );
        }

        $roomType =
            $this->roomModel->getRoomTypeById(
                $roomTypeId
            );

        if (!$roomType) {
            $_SESSION['error'] =
                'Loại phòng không tồn tại.';

            $this->redirect(
                'roomEdit',
                ['id' => $id]
            );
        }

        if (
            !$totalSeats
            || !in_array(
                $totalSeats,
                RoomModel::allowedCapacities(),
                true
            )
        ) {
            $_SESSION['error'] =
                'Sức chứa không hợp lệ. Hệ thống chỉ hỗ trợ '
                . implode(
                    ', ',
                    RoomModel::allowedCapacities()
                )
                . ' ghế.';

            $this->redirect(
                'roomEdit',
                ['id' => $id]
            );
        }

        if (
            $this->isGoldClass($roomType['name'])
            && $totalSeats !== 60
        ) {
            $_SESSION['error'] =
                'Phòng Gold Class chỉ hỗ trợ 60 ghế Couple.';

            $this->redirect(
                'roomEdit',
                ['id' => $id]
            );
        }

        $hasShowtimes =
            $this->roomModel->countShowtimes($id) > 0;

        $hasTickets =
            $this->roomModel->countTickets($id) > 0;

        $typeOrCapacityChanged =
            (int) $room['room_type_id']
                !== (int) $roomTypeId
            || (int) $room['total_seats']
                !== (int) $totalSeats;

        if (
            ($hasShowtimes || $hasTickets)
            && $typeOrCapacityChanged
        ) {
            $_SESSION['error'] =
                'Phòng đã có suất chiếu hoặc vé liên quan. '
                . 'Bạn chỉ được đổi tên phòng.';

            $this->redirect(
                'roomEdit',
                ['id' => $id]
            );
        }

        $pdo = $this->roomModel->getPdo();

        try {
            $pdo->beginTransaction();

            $this->roomModel->edit(
                $id,
                $roomTypeId,
                $name,
                $totalSeats,
                $pdo
            );

            if ($typeOrCapacityChanged) {
                $regenResult =
                    $this->seatModel->regenerateForRoom(
                        $id,
                        $roomType['name'],
                        $totalSeats,
                        $pdo
                    );

                if (!$regenResult['success']) {
                    throw new RuntimeException(
                        $regenResult['message']
                    );
                }
            }

            $pdo->commit();

            unset($_SESSION['old_input']);

            $_SESSION['success'] =
                'Cập nhật phòng chiếu thành công.';

            $this->redirect('rooms');
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log(
                'RoomController::editProcess: '
                . $e->getMessage()
            );

            $_SESSION['error'] =
                'Không thể cập nhật phòng. '
                . $e->getMessage();

            $this->redirect(
                'roomEdit',
                ['id' => $id]
            );
        }
    }

    public function delete()
    {
        $id = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$id || $id <= 0) {
            $_SESSION['error'] =
                'ID phòng không hợp lệ.';

            $this->redirect('rooms');
        }

        $room = $this->roomModel->getDetail($id);

        if (!$room) {
            $_SESSION['error'] =
                'Không tìm thấy phòng chiếu.';

            $this->redirect('rooms');
        }

        $totalShowtimes =
            $this->roomModel->countShowtimes($id);

        $totalTickets =
            $this->roomModel->countTickets($id);

        if (
            $totalShowtimes > 0
            || $totalTickets > 0
        ) {
            $_SESSION['error'] =
                'Không thể xóa phòng vì phòng đang có '
                . 'suất chiếu hoặc vé liên quan.';

            $this->redirect('rooms');
        }

        $pdo = $this->roomModel->getPdo();

        try {
            $pdo->beginTransaction();

            $this->seatModel->deleteByRoomId(
                $id,
                $pdo
            );

            $deleted =
                $this->roomModel->delete(
                    $id,
                    $pdo
                );

            if (!$deleted) {
                throw new RuntimeException(
                    'Không tìm thấy phòng để xóa.'
                );
            }

            $pdo->commit();

            $_SESSION['success'] =
                'Xóa phòng và toàn bộ ghế liên quan thành công.';

            $this->redirect('rooms');
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log(
                'RoomController::delete: '
                . $e->getMessage()
            );

            $_SESSION['error'] =
                'Không thể xóa phòng. '
                . $e->getMessage();

            $this->redirect('rooms');
        }
    }

    public function seats()
    {
        $id = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$id || $id <= 0) {
            $_SESSION['error'] =
                'ID phòng không hợp lệ.';

            $this->redirect('rooms');
        }

        $room = $this->roomModel->getDetail($id);

        if (!$room) {
            $_SESSION['error'] =
                'Không tìm thấy phòng chiếu.';

            $this->redirect('rooms');
        }

        $seats =
            $this->seatModel->getByRoomId($id);

        $hasTickets =
            $this->seatModel->hasTicketsForRoom($id);

        $seatCount = count($seats);

        $view = PATH_VIEW_ADMIN . 'rooms/seats.php';

        require_once PATH_VIEW_ADMIN
            . 'layout/layout.php';
    }

    public function generateSeats()
    {
        $id = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$id || $id <= 0) {
            $_SESSION['error'] =
                'ID phòng không hợp lệ.';

            $this->redirect('rooms');
        }

        $room = $this->roomModel->getDetail($id);

        if (!$room) {
            $_SESSION['error'] =
                'Không tìm thấy phòng chiếu.';

            $this->redirect('rooms');
        }

        if (
            $this->seatModel->hasTicketsForRoom($id)
        ) {
            $_SESSION['error'] =
                'Phòng này đã có vé bán, '
                . 'không thể sinh lại ghế.';

            $this->redirect(
                'roomSeats',
                ['id' => $id]
            );
        }

        $seatCount =
            $this->seatModel->countByRoomId($id);

        if ($seatCount > 0) {
            $result =
                $this->seatModel->regenerateForRoom(
                    $id,
                    $room['room_type_name'],
                    (int) $room['total_seats']
                );
        } else {
            $result =
                $this->seatModel->generateForRoom(
                    $id,
                    $room['room_type_name'],
                    (int) $room['total_seats']
                );
        }

        if (!$result['success']) {
            $_SESSION['error'] =
                $result['message'];

            $this->redirect(
                'roomSeats',
                ['id' => $id]
            );
        }

        $_SESSION['success'] =
            $result['message'];

        $this->redirect(
            'roomSeats',
            ['id' => $id]
        );
    }

    public function toggleSeat()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);

            echo json_encode([
                'success' => false,
                'message' => 'Chỉ chấp nhận phương thức POST.',
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        $seatId = filter_input(
            INPUT_POST,
            'seat_id',
            FILTER_VALIDATE_INT
        );

        if (!$seatId || $seatId <= 0) {
            http_response_code(422);

            echo json_encode([
                'success' => false,
                'message' => 'ID ghế không hợp lệ.',
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        try {
            $result = $this->seatModel->toggleStatus($seatId);

            if (empty($result['success'])) {
                http_response_code(400);
            }

            echo json_encode(
                $result,
                JSON_UNESCAPED_UNICODE
            );
        } catch (Throwable $e) {
            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage(),
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }
}