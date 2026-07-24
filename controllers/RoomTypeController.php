<?php
class RoomTypeController
{
    public $roomTypeModel;

    public function __construct()
    {
        $this->roomTypeModel = new RoomTypeModel();
    }

    public function list()
    {
        $listRoomType = $this->roomTypeModel->getAll();
        $view = 'admin/room-types/list';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    public function add()
    {
        $view = 'admin/room-types/add';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    public function addProcess()
    {
        $name = trim($_POST['name'] ?? '');
        $price_modifier = (float)($_POST['price_modifier'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        if ($name === '' || $price_modifier < 0) {
            header('Location:' . BASE_URL . '?action=roomTypeAdd&error=invalid_room_type');
            exit;
        }

        $this->roomTypeModel->add($name, $price_modifier, $description);
        header('Location:' . BASE_URL . '?action=room-types&success=room_type_added');
        exit;
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location:' . BASE_URL . '?action=room-types&error=invalid_room_type');
            exit;
        }

        $roomType = $this->roomTypeModel->editById($id);
        if (!$roomType) {
            header('Location:' . BASE_URL . '?action=room-types&error=room_type_not_found');
            exit;
        }
        $view = 'admin/room-types/edit';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    public function editProcess()
    {
        $id = (int)($_GET['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $price_modifier = (float)($_POST['price_modifier'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        if ($id <= 0 || $name === '' || $price_modifier < 0) {
            header('Location:' . BASE_URL . '?action=room-types&error=invalid_room_type');
            exit;
        }

        $this->roomTypeModel->edit($id, $name, $price_modifier, $description);
        header('Location:' . BASE_URL . '?action=room-types&success=room_type_updated');
        exit;
    }

    public function delete()
    {
        $id = (int)($_GET['id'] ?? 0);
        $totalRooms = $this->roomTypeModel->countRooms($id);

        if ($totalRooms > 0) {
            header('Location:' . BASE_URL . '?action=room-types&error=room_type_using');
            exit;
        }

        $this->roomTypeModel->delete($id);
        header('Location:' . BASE_URL . '?action=room-types&success=room_type_deleted');
        exit;
    }
}
