<?php

class StaffFoodOrderController
{
    // GET ?action=food_order_list
    public function list()
    {
        $orderModel = new FoodOrderModel();
        $orders = $orderModel->getAll();

        $flash = get_flash();

        $view = 'staff/food_order';
        require_once PATH_VIEW . 'staff/layout/layout.php';
    }
}
