<?php
class StaffSeatTypeController
{
    public $seatTypeModel;

    public function __construct()
    {
        $this->seatTypeModel = new SeatTypeModel();
    }

    public function list()
    {
        $listSeatType = $this->seatTypeModel->getAll();
        $view = 'staff/seats-type';
        require_once PATH_VIEW . 'staff/layout/layout.php';
    }
}
