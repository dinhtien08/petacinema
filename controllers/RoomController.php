<?php
class RoomController
{
    public $roomModel;

    public function __construct()
    {
        $this->roomModel = new RoomModel();
    }

    public function list()
    {
        $listRoom = $this->roomModel->getAll();
        $view = 'admin/rooms/list';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    public function add()
    {
        $listRoomType = $this->roomModel->getRoomTypes();
        $view = 'admin/rooms/add';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    public function addProcess()
    {
        $room_type_id = $_POST['room_type_id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $total_seats = (int)($_POST['total_seats'] ?? 0);

        if ($room_type_id === '' || $name === '' || $total_seats <= 0) {
            header('Location:' . BASE_URL . '?action=roomAdd&error=invalid_room');
            exit;
        }

        $this->roomModel->add($room_type_id, $name, $total_seats);
        header('Location:' . BASE_URL . '?action=rooms&success=room_added');
        exit;
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location:' . BASE_URL . '?action=rooms&error=invalid_room');
            exit;
        }

        $room = $this->roomModel->editById($id);
        $listRoomType = $this->roomModel->getRoomTypes();

        if (!$room) {
            header('Location:' . BASE_URL . '?action=rooms&error=room_not_found');
            exit;
        }
        $view = 'admin/rooms/edit';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    public function editProcess()
    {
        $id = (int)($_GET['id'] ?? 0);
        $room_type_id = $_POST['room_type_id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $total_seats = (int)($_POST['total_seats'] ?? 0);

        if ($id <= 0 || $room_type_id === '' || $name === '' || $total_seats <= 0) {
            header('Location:' . BASE_URL . '?action=rooms&error=invalid_room');
            exit;
        }

        $this->roomModel->edit($id, $room_type_id, $name, $total_seats);
        header('Location:' . BASE_URL . '?action=rooms&success=room_updated');
        exit;
    }

    public function delete()
    {
        $id = (int)($_GET['id'] ?? 0);
        $totalSeats = $this->roomModel->countSeats($id);
        $totalShowtimes = $this->roomModel->countShowtimes($id);

        if ($totalSeats > 0 || $totalShowtimes > 0) {
            header('Location:' . BASE_URL . '?action=rooms&error=room_using');
            exit;
        }

        $this->roomModel->delete($id);
        header('Location:' . BASE_URL . '?action=rooms&success=room_deleted');
        exit;
    }
}
