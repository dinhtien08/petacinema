<?php
class SeatTypeController
{
    public $seatTypeModel;

    public function __construct()
    {
        $this->seatTypeModel = new SeatTypeModel();
    }

    public function list()
    {
        $listSeatType = $this->seatTypeModel->getAll();
        $view = 'admin/seat-types/list';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    public function add()
    {
        $view = 'admin/seat-types/add';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    public function addProcess()
    {
        $name = trim($_POST['name'] ?? '');
        $surcharge = (float)($_POST['surcharge'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        if ($name === '' || $surcharge < 0) {
            header('Location:' . BASE_URL . '?action=seatTypeAdd&error=invalid_seat_type');
            exit;
        }

        $this->seatTypeModel->add($name, $surcharge, $description);
        header('Location:' . BASE_URL . '?action=seat-types&success=seat_type_added');
        exit;
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location:' . BASE_URL . '?action=seat-types&error=invalid_seat_type');
            exit;
        }

        $seatType = $this->seatTypeModel->editById($id);
        if (!$seatType) {
            header('Location:' . BASE_URL . '?action=seat-types&error=seat_type_not_found');
            exit;
        }
        $view = 'admin/seat-types/edit';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    public function editProcess()
    {
        $id = (int)($_GET['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $surcharge = (float)($_POST['surcharge'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        if ($id <= 0 || $name === '' || $surcharge < 0) {
            header('Location:' . BASE_URL . '?action=seat-types&error=invalid_seat_type');
            exit;
        }

        $this->seatTypeModel->edit($id, $name, $surcharge, $description);
        header('Location:' . BASE_URL . '?action=seat-types&success=seat_type_updated');
        exit;
    }

    public function delete()
    {
        $id = (int)($_GET['id'] ?? 0);
        $totalSeats = $this->seatTypeModel->countSeats($id);

        if ($totalSeats > 0) {
            header('Location:' . BASE_URL . '?action=seat-types&error=seat_type_using');
            exit;
        }

        $this->seatTypeModel->delete($id);
        header('Location:' . BASE_URL . '?action=seat-types&success=seat_type_deleted');
        exit;
    }
}
