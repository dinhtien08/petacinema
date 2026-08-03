<?php

class StaffFoodVariantController
{
    // GET ?action=food_variant_list&food_id=
    public function list()
    {
        $foodId = (int) ($_GET['food_id'] ?? 0);
        $foodModel = new FoodModel();
        $food = $foodModel->getById($foodId);
        if (!$food) {
            set_flash('error', 'Không tìm thấy món ăn.');
            header('Location: ?action=staff_food_list');
            exit;
        }

        $variantModel = new FoodVariantModel();
        $variants = $variantModel->getByFoodId($foodId);
        $flash = get_flash();
        $view = 'staff/food_variant';
        require_once PATH_VIEW . 'staff/layout/layout.php';
    }
}
