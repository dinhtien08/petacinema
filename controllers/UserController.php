<?php

class UserController
{
    public $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function users_list()
    {
        $listUser = $this->userModel->getAll();
        require_once PATH_VIEW . 'admin/user/list.php';
    }

    public function users_add()
    {
        require_once PATH_VIEW . 'admin/user/add.php';
    }

    public function users_addUser()
    {
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        $status = $_POST['status'];
        $created_at = date('Y-m-d H:i:s');

        $this->userModel->addUser(
            $fullname,
            $email,
            $password,
            $role,
            $status,
            $created_at
        );

        header("Location:" . BASE_URL . "?action=users_list");
        exit();
    }

    public function users_edit()
    {
        $id = $_GET['id'];
        $edit = $this->userModel->getById($id);
        require_once PATH_VIEW . 'admin/user/edit.php';
    }

    public function users_editUser()
    {
        $id = $_POST['id'];
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        $status = $_POST['status'];

        $this->userModel->editUser(
            $id,
            $fullname,
            $email,
            $password,
            $role,
            $status
        );

        header("Location:" . BASE_URL . "?action=users_list");
        exit();
    }

    public function users_delete()
    {
        $id = $_GET['id'];
        $this->userModel->deleteUser($id);

        header("Location:" . BASE_URL . "?action=users_list");
        exit();
    }
}