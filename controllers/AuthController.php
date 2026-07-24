<?php

class AuthController
{
    public $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        require_once PATH_VIEW . 'login.php';
    }

    public function register()
    {
        require_once PATH_VIEW . 'register.php';
    }

    public function loginPost()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = $this->userModel->getByEmail($email);

        if (!$user) {
            echo "Email không tồn tại";
            return;
        }

        if ($user['password'] != $password) {
            echo "Sai mật khẩu";
            return;
        }

        if ($user['status'] != 'active') {
            echo "Tài khoản đã bị khóa";
            return;
        }

        $_SESSION['user'] = $user;

        if ($user['role'] == 'admin') {
            header("Location:" . BASE_URL);
        } else {
            header("Location:" . BASE_URL);
        }
    }

    public function logout()
    {
        unset($_SESSION['user']);
        header("Location:" . BASE_URL);
    }
}