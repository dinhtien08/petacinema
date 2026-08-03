<?php

class StaffFoodController
{
    // GET ?action=food_list
    public function list()
    {
        $foodModel = new FoodModel();
        $foods = $foodModel->getAll();

        $flash = get_flash();

        $view = 'staff/food';
        require_once PATH_VIEW . 'staff/layout/layout.php';
    }
}
