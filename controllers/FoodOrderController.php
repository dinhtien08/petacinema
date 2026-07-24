<?php

class FoodOrderController
{
    // GET ?action=food_order_list
    public function list()
    {
        $orderModel = new FoodOrderModel();
        $orders = $orderModel->getAll();

        $flash = get_flash();

        $view = PATH_VIEW . 'admin/food_order/list.php';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    // GET ?action=food_order_add[&food_id=][&variant_id=]
    public function add()
    {
        $foodId = (int) ($_GET['food_id'] ?? 0);
        $variantId = (int) ($_GET['variant_id'] ?? 0);

        $foodModel = new FoodModel();
        $variantModel = new FoodVariantModel();

        $foods = $foodModel->getAll();

        $stage = 1;
        $food = null;
        $variants = [];
        $variant = null;

        if ($foodId > 0) {
            $food = $foodModel->getById($foodId);
            if ($food) {
                $variants = $variantModel->getByFoodId($foodId);
                $stage = 2;
            }
        }

        if ($stage === 2 && $variantId > 0) {
            $variant = $variantModel->getById($variantId);
            if ($variant && (int) $variant['food_id'] === $foodId) {
                $stage = 3;
            }
        }

        $errors = [];
        $old = [
            'food_id'          => $foodId,
            'food_variant_id'  => $variantId,
            'booking_id'       => '',
            'quantity'         => 1,
            'price_at_booking' => $variant['price'] ?? '',
        ];

        $view = PATH_VIEW . 'admin/food_order/add.php';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    // POST ?action=food_order_addPost
    public function addPost()
    {
        $foodId = (int) ($_POST['food_id'] ?? 0);
        $variantId = (int) ($_POST['food_variant_id'] ?? 0);

        $variantModel = new FoodVariantModel();
        $variant = $variantModel->getById($variantId);

        if (!$variant || (int) $variant['food_id'] !== $foodId) {
            set_flash('error', 'Biến thể món ăn không hợp lệ.');
            header('Location: ?action=food_order_add');
            exit;
        }

        $old = [
            'food_id'          => $foodId,
            'food_variant_id'  => $variantId,
            'booking_id'       => trim($_POST['booking_id'] ?? ''),
            'quantity'         => trim($_POST['quantity'] ?? ''),
            'price_at_booking' => trim($_POST['price_at_booking'] ?? ''),
        ];

        $errors = $this->validate($old);

        if (!empty($errors)) {
            $foodModel = new FoodModel();
            $food = $foodModel->getById($foodId);
            $foods = $foodModel->getAll();
            $variants = $variantModel->getByFoodId($foodId);
            $stage = 3;

            $view = PATH_VIEW . 'admin/food_order/add.php';
            require_once PATH_VIEW . 'admin/layout/layout.php';
            return;
        }

        $orderModel = new FoodOrderModel();

        try {
            $orderModel->addOrder($old);
        } catch (PDOException $e) {
            set_flash('error', 'Booking ID không tồn tại hoặc dữ liệu không hợp lệ.');
            header('Location: ?action=food_order_add&food_id=' . $foodId . '&variant_id=' . $variantId);
            exit;
        }

        set_flash('success', 'Thêm đơn món ăn thành công.');
        header('Location: ?action=food_order_list');
        exit;
    }

    // GET ?action=food_order_edit&id=[&food_id=][&variant_id=]
    public function edit()
    {
        $id = (int) ($_GET['id'] ?? 0);

        $orderModel = new FoodOrderModel();
        $order = $orderModel->getById($id);

        if (!$order) {
            set_flash('error', 'Không tìm thấy đơn món ăn.');
            header('Location: ?action=food_order_list');
            exit;
        }

        $foodModel = new FoodModel();
        $variantModel = new FoodVariantModel();

        $currentVariant = $variantModel->getById($order['food_variant_id']);
        $foodId = (int) ($_GET['food_id'] ?? $currentVariant['food_id']);
        $variantId = (int) ($_GET['variant_id'] ?? $order['food_variant_id']);

        $foods = $foodModel->getAll();
        $food = $foodModel->getById($foodId);
        $variants = $food ? $variantModel->getByFoodId($foodId) : [];
        $variant = $variantModel->getById($variantId);

        if ($food && $variant && (int) $variant['food_id'] === $foodId) {
            $stage = 3;
        } elseif ($food) {
            $stage = 2;
        } else {
            $stage = 1;
        }

        $errors = [];
        $old = [
            'id'               => $id,
            'food_id'          => $foodId,
            'food_variant_id'  => $variantId,
            'booking_id'       => $order['booking_id'],
            'quantity'         => $order['quantity'],
            'price_at_booking' => $order['price_at_booking'],
        ];

        $view = PATH_VIEW . 'admin/food_order/edit.php';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    // POST ?action=food_order_editPost&id=
    public function editPost()
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

        $orderModel = new FoodOrderModel();
        $order = $orderModel->getById($id);

        if (!$order) {
            set_flash('error', 'Không tìm thấy đơn món ăn.');
            header('Location: ?action=food_order_list');
            exit;
        }

        $foodId = (int) ($_POST['food_id'] ?? 0);
        $variantId = (int) ($_POST['food_variant_id'] ?? 0);

        $variantModel = new FoodVariantModel();
        $variant = $variantModel->getById($variantId);

        if (!$variant || (int) $variant['food_id'] !== $foodId) {
            set_flash('error', 'Biến thể món ăn không hợp lệ.');
            header('Location: ?action=food_order_edit&id=' . $id);
            exit;
        }

        $old = [
            'id'               => $id,
            'food_id'          => $foodId,
            'food_variant_id'  => $variantId,
            'booking_id'       => trim($_POST['booking_id'] ?? ''),
            'quantity'         => trim($_POST['quantity'] ?? ''),
            'price_at_booking' => trim($_POST['price_at_booking'] ?? ''),
        ];

        $errors = $this->validate($old);

        if (!empty($errors)) {
            $foodModel = new FoodModel();
            $food = $foodModel->getById($foodId);
            $foods = $foodModel->getAll();
            $variants = $variantModel->getByFoodId($foodId);
            $stage = 3;

            $view = PATH_VIEW . 'admin/food_order/edit.php';
            require_once PATH_VIEW . 'admin/layout/layout.php';
            return;
        }

        try {
            $orderModel->editOrder($id, $old);
        } catch (PDOException $e) {
            set_flash('error', 'Booking ID không tồn tại hoặc dữ liệu không hợp lệ.');
            header('Location: ?action=food_order_edit&id=' . $id);
            exit;
        }

        set_flash('success', 'Cập nhật đơn món ăn thành công.');
        header('Location: ?action=food_order_list');
        exit;
    }

    // POST ?action=food_order_delete&id=
    public function delete()
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

        $orderModel = new FoodOrderModel();

        try {
            $orderModel->deleteOrder($id);
            set_flash('success', 'Xóa đơn món ăn thành công.');
        } catch (PDOException $e) {
            set_flash('error', 'Không thể xóa đơn món ăn này.');
        }

        header('Location: ?action=food_order_list');
        exit;
    }

    private function validate($data)
    {
        $errors = [];

        if ($data['booking_id'] === '' || !ctype_digit((string) $data['booking_id']) || (int) $data['booking_id'] <= 0) {
            $errors['booking_id'] = 'Booking ID phải là số nguyên dương.';
        }

        if ($data['quantity'] === '' || !ctype_digit((string) $data['quantity']) || (int) $data['quantity'] <= 0) {
            $errors['quantity'] = 'Số lượng phải là số nguyên dương.';
        }

        if ($data['price_at_booking'] === '' || !is_numeric($data['price_at_booking']) || (float) $data['price_at_booking'] < 0) {
            $errors['price_at_booking'] = 'Giá phải là số và không được âm.';
        }

        return $errors;
    }
}
