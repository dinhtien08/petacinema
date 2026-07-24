<?php

class FoodVariantController
{
    // GET ?action=food_variant_list&food_id=
    public function list()
    {
        $foodId = (int) ($_GET['food_id'] ?? 0);

        $foodModel = new FoodModel();
        $food = $foodModel->getById($foodId);

        if (!$food) {
            set_flash('error', 'Không tìm thấy món ăn.');
            header('Location: ?action=food_list');
            exit;
        }

        $variantModel = new FoodVariantModel();
        $variants = $variantModel->getByFoodId($foodId);

        $flash = get_flash();

        $view = 'admin/food_variant/list';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    // GET ?action=food_variant_add&food_id=
    public function add()
    {
        $foodId = (int) ($_GET['food_id'] ?? 0);

        $foodModel = new FoodModel();
        $food = $foodModel->getById($foodId);

        if (!$food) {
            set_flash('error', 'Không tìm thấy món ăn.');
            header('Location: ?action=food_list');
            exit;
        }

        $errors = [];
        $old = ['food_id' => $foodId];

        $view = 'admin/food_variant/add';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    // POST ?action=food_variant_addPost
    public function addPost()
    {
        $foodId = (int) ($_POST['food_id'] ?? 0);

        $foodModel = new FoodModel();
        $food = $foodModel->getById($foodId);

        if (!$food) {
            set_flash('error', 'Không tìm thấy món ăn.');
            header('Location: ?action=food_list');
            exit;
        }

        $old = [
            'food_id' => $foodId,
            'size'    => trim($_POST['size'] ?? ''),
            'price'   => trim($_POST['price'] ?? ''),
            'stock'   => trim($_POST['stock'] ?? ''),
        ];

        $errors = $this->validate($old);

        if (!empty($errors)) {
            $view = 'admin/food_variant/add';
            require_once PATH_VIEW . 'admin/layout/layout.php';
            return;
        }

        $variantModel = new FoodVariantModel();
        $variantModel->addVariant($old);

        set_flash('success', 'Thêm biến thể thành công.');
        header('Location: ?action=food_variant_list&food_id=' . $foodId);
        exit;
    }

    // GET ?action=food_variant_edit&id=
    public function edit()
    {
        $id = (int) ($_GET['id'] ?? 0);

        $variantModel = new FoodVariantModel();
        $variant = $variantModel->getById($id);

        if (!$variant) {
            set_flash('error', 'Không tìm thấy biến thể.');
            header('Location: ?action=food_list');
            exit;
        }

        $errors = [];
        $old = $variant;

        $view = 'admin/food_variant/edit';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    // POST ?action=food_variant_editPost&id=
    public function editPost()
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

        $variantModel = new FoodVariantModel();
        $variant = $variantModel->getById($id);

        if (!$variant) {
            set_flash('error', 'Không tìm thấy biến thể.');
            header('Location: ?action=food_list');
            exit;
        }

        $old = [
            'id'      => $id,
            'food_id' => $variant['food_id'],
            'size'    => trim($_POST['size'] ?? ''),
            'price'   => trim($_POST['price'] ?? ''),
            'stock'   => trim($_POST['stock'] ?? ''),
        ];

        $errors = $this->validate($old);

        if (!empty($errors)) {
            $old['food_name'] = $variant['food_name'];
            $view = 'admin/food_variant/edit';
            require_once PATH_VIEW . 'admin/layout/layout.php';
            return;
        }

        $variantModel->editVariant($id, $old);

        set_flash('success', 'Cập nhật biến thể thành công.');
        header('Location: ?action=food_variant_list&food_id=' . $variant['food_id']);
        exit;
    }

    // POST ?action=food_variant_delete&id=&food_id=
    public function delete()
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $foodId = (int) ($_GET['food_id'] ?? $_POST['food_id'] ?? 0);

        $variantModel = new FoodVariantModel();

        try {
            $variantModel->deleteVariant($id);
            set_flash('success', 'Xóa biến thể thành công.');
        } catch (PDOException $e) {
            set_flash('error', 'Không thể xóa biến thể vì đang có đơn hàng liên quan.');
        }

        header('Location: ?action=food_variant_list&food_id=' . $foodId);
        exit;
    }

    private function validate($data)
    {
        $errors = [];

        if ($data['food_id'] <= 0) {
            $errors['food_id'] = 'Món ăn không hợp lệ.';
        }

        if ($data['size'] === '') {
            $errors['size'] = 'Vui lòng nhập kích cỡ.';
        } elseif (mb_strlen($data['size']) > 10) {
            $errors['size'] = 'Kích cỡ không được vượt quá 10 ký tự.';
        }

        if ($data['price'] === '' || !is_numeric($data['price']) || (float) $data['price'] < 0) {
            $errors['price'] = 'Giá phải là số và không được âm.';
        }

        if ($data['stock'] === '' || !ctype_digit((string) $data['stock']) || (int) $data['stock'] < 0) {
            $errors['stock'] = 'Tồn kho phải là số nguyên không âm.';
        }

        return $errors;
    }
}
