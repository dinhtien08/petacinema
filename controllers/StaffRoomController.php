<?php

class StaffRoomController
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

    public function list()
    {
        $listRoom = $this->roomModel->getAll();

        $view = PATH_VIEW_STAFF . 'room_list.php';

        require_once PATH_VIEW_STAFF
            . 'layout/layout.php';
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

            $this->redirect('staff_rooms');
        }

        $room = $this->roomModel->getDetail($id);

        if (!$room) {
            $_SESSION['error'] =
                'Không tìm thấy phòng chiếu.';

            $this->redirect('staff_rooms');
        }

        $seats =
            $this->seatModel->getByRoomId($id);

        $hasTickets =
            $this->seatModel->hasTicketsForRoom($id);

        $seatCount = count($seats);

        $view = PATH_VIEW_STAFF . 'room_seats.php';

        require_once PATH_VIEW_STAFF
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

            $this->redirect('staff_rooms');
        }

        $room = $this->roomModel->getDetail($id);

        if (!$room) {
            $_SESSION['error'] =
                'Không tìm thấy phòng chiếu.';

            $this->redirect('staff_rooms');
        }

        if (
            $this->seatModel->hasTicketsForRoom($id)
        ) {
            $_SESSION['error'] =
                'Phòng này đã có vé bán, '
                . 'không thể sinh lại ghế.';

            $this->redirect(
                'staff_roomSeats',
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
                'staff_roomSeats',
                ['id' => $id]
            );
        }

        $_SESSION['success'] =
            $result['message'];

        $this->redirect(
            'staff_roomSeats',
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