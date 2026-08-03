<?php
class StaffRoomTypeController
{
    public $roomTypeModel;

    public function __construct()
    {
        $this->roomTypeModel = new RoomTypeModel();
    }

    public function list()
    {
        $listRoomType = $this->roomTypeModel->getAll();
        $view = 'staff/room_type';
        require_once PATH_VIEW . 'staff/layout/layout.php';
    }
}
