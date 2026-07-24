<?php

class FoodController
{
    private $allowedImageExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    // GET ?action=food_list
    public function list()
    {
        $foodModel = new FoodModel();
        $foods = $foodModel->getAll();

        $flash = get_flash();

        $view = 'admin/food/list';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    // GET ?action=food_add
    public function add()
    {
        $errors = [];
        $old = [];

        $view = 'admin/food/add';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    // POST ?action=food_addPost
    public function addPost()
    {
        $old = [
            'name'        => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'status'      => $_POST['status'] ?? 'active',
        ];

        $errors = $this->validate($old, $_FILES['image'] ?? null);

        if (!empty($errors)) {
            $view = 'admin/food/add';
            require_once PATH_VIEW . 'admin/layout/layout.php';
            return;
        }

        $image = null;
        if (!empty($_FILES['image']['name'])) {
            $image = upload_file('foods', $_FILES['image']);
        }

        $foodModel = new FoodModel();
        $foodModel->addFood([
            'name'        => $old['name'],
            'description' => $old['description'],
            'image'       => $image,
            'status'      => $old['status'],
        ]);

        set_flash('success', 'Thêm món ăn thành công.');
        header('Location: ?action=food_list');
        exit;
    }

    // GET ?action=food_edit&id=
    public function edit()
    {
        $id = (int) ($_GET['id'] ?? 0);

        $foodModel = new FoodModel();
        $food = $foodModel->getById($id);

        if (!$food) {
            set_flash('error', 'Không tìm thấy món ăn.');
            header('Location: ?action=food_list');
            exit;
        }

        $errors = [];
        $old = $food;

        $view = 'admin/food/edit';
        require_once PATH_VIEW . 'admin/layout/layout.php';
    }

    // POST ?action=food_editPost&id=
    public function editPost()
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

        $foodModel = new FoodModel();
        $food = $foodModel->getById($id);

        if (!$food) {
            set_flash('error', 'Không tìm thấy món ăn.');
            header('Location: ?action=food_list');
            exit;
        }

        $old = [
            'id'          => $id,
            'name'        => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'status'      => $_POST['status'] ?? 'active',
            'image'       => $food['image'],
        ];

        $errors = $this->validate($old, $_FILES['image'] ?? null);

        if (!empty($errors)) {
            $view = 'admin/food/edit';
            require_once PATH_VIEW . 'admin/layout/layout.php';
            return;
        }

        $image = $food['image'];
        if (!empty($_FILES['image']['name'])) {
            $image = upload_file('foods', $_FILES['image']);
        }

        $foodModel->editFood($id, [
            'name'        => $old['name'],
            'description' => $old['description'],
            'image'       => $image,
            'status'      => $old['status'],
        ]);

        set_flash('success', 'Cập nhật món ăn thành công.');
        header('Location: ?action=food_list');
        exit;
    }

    // POST ?action=food_delete&id=
    public function delete()
    {
        
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $foodModel = new FoodModel();
    
        try {
            $foodModel->deleteFood($id);
            set_flash('success', 'Xóa món ăn thành công.');
        } catch (PDOException $e) {
            set_flash('error', 'Không thể xóa món ăn vì đang có đơn hàng liên kết với biến thể của nó.');
        }

        header('Location: ?action=food_list');
        exit;
    }

    private function validate($data, $imageFile)
    {
        $errors = [];

        if ($data['name'] === '') {
            $errors['name'] = 'Vui lòng nhập tên món ăn.';
        } elseif (mb_strlen($data['name']) > 255) {
            $errors['name'] = 'Tên món ăn không được vượt quá 255 ký tự.';
        }

        if (!in_array($data['status'], ['active', 'inactive'], true)) {
            $errors['status'] = 'Trạng thái không hợp lệ.';
        }

        if (!empty($imageFile['name'])) {
            if ($imageFile['error'] !== UPLOAD_ERR_OK) {
                $errors['image'] = 'Tải ảnh lên thất bại.';
            } else {
                $ext = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $this->allowedImageExt, true)) {
                    $errors['image'] = 'Ảnh chỉ chấp nhận định dạng: ' . implode(', ', $this->allowedImageExt) . '.';
                }
            }
        }

        return $errors;
    }
}
