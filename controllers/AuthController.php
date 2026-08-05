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
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $errors   = [];

        // Validate không để trống
        if (empty($email)) {
            $errors['email'] = 'Vui lòng nhập Email hoặc Tên đăng nhập.';
        }

        if (empty($password)) {
            $errors['password'] = 'Vui lòng nhập mật khẩu.';
        }

        if (!empty($errors)) {
            set_errors($errors, $_POST);
            header("Location: " . BASE_URL . "?action=login");
            exit;
        }

        // Kiểm tra tài khoản
        $user = $this->userModel->getByEmail($email);

        if (!$user) {
            $errors['password'] = 'Tài khoản hoặc mật khẩu không đúng.';
            set_errors($errors, $_POST);
            header("Location: " . BASE_URL . "?action=login");
            exit;
        }

        // Xác thực mật khẩu (bằng password_verify hoặc so sánh trực tiếp cho dữ liệu mẫu)
        $isPasswordValid = password_verify($password, $user['password']) || $user['password'] === $password;

        if (!$isPasswordValid) {
            $errors['password'] = 'Tài khoản hoặc mật khẩu không đúng.';
            set_errors($errors, $_POST);
            header("Location: " . BASE_URL . "?action=login");
            exit;
        }

        // Kiểm tra trạng thái tài khoản
        if ($user['status'] !== 'active') {
            $errors['password'] = 'Tài khoản của bạn đã bị khóa hoặc chưa kích hoạt.';
            set_errors($errors, $_POST);
            header("Location: " . BASE_URL . "?action=login");
            exit;
        }

        // Lưu Session sau khi đăng nhập thành công
        $_SESSION['user']     = $user;
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['email'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role']     = $user['role']; // 'user' (client), 'staff', 'admin'

        // Chuyển hướng theo Role
        if ($user['role'] === 'admin') {
            header("Location: " . BASE_URL . "?action=dashboard");
            exit;
        } elseif ($user['role'] === 'staff') {
            header("Location: " . BASE_URL . "?action=staff_dashboard");
            exit;
        } else {
            header("Location: " . BASE_URL);
            exit;
        }
    }

    public function registerStore()
    {
        $fullname        = trim($_POST['fullname'] ?? '');
        $email           = trim($_POST['email'] ?? '');
        $password        = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $errors          = [];

        // Validate Họ tên
        if (empty($fullname)) {
            $errors['fullname'] = 'Họ và tên không được để trống.';
        } elseif (mb_strlen($fullname) < 3) {
            $errors['fullname'] = 'Họ và tên phải có ít nhất 3 ký tự.';
        }

        // Validate Email
        if (empty($email)) {
            $errors['email'] = 'Email không được để trống.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không đúng định dạng.';
        } else {
            $existingUser = $this->userModel->getByEmail($email);
            if ($existingUser) {
                $errors['email'] = 'Email này đã được sử dụng trong hệ thống.';
            }
        }

        // Validate Mật khẩu
        if (empty($password)) {
            $errors['password'] = 'Mật khẩu không được để trống.';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự.';
        }

        // Validate Xác nhận mật khẩu
        if (empty($confirmPassword)) {
            $errors['confirm_password'] = 'Vui lòng xác nhận mật khẩu.';
        } elseif ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Mật khẩu xác nhận không trùng khớp.';
        }

        // Nếu có lỗi thì lưu lỗi + dữ liệu cũ và quay lại trang Đăng ký
        if (!empty($errors)) {
            set_errors($errors, $_POST);
            header("Location: " . BASE_URL . "?action=register");
            exit;
        }

        // Mã hóa mật khẩu và tạo tài khoản
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role           = 'user';
        $status         = 'active';
        $createdAt      = date('Y-m-d H:i:s');

        $result = $this->userModel->addUser($fullname, $email, $hashedPassword, $role, $status, $createdAt);

        if ($result) {
            set_flash('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
            header("Location: " . BASE_URL . "?action=login");
            exit;
        } else {
            $errors['fullname'] = 'Đăng ký không thành công, vui lòng thử lại.';
            set_errors($errors, $_POST);
            header("Location: " . BASE_URL . "?action=register");
            exit;
        }
    }

    public function logout()
    {
        unset(
            $_SESSION['user'],
            $_SESSION['user_id'],
            $_SESSION['username'],
            $_SESSION['fullname'],
            $_SESSION['role']
        );
        session_destroy();
        session_start();
        header("Location: " . BASE_URL . "?action=/");
        exit;
    }

    public function profile()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "?action=login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);

        $title = "Thông tin cá nhân - PETACINEMA";
        $view  = "client/profile";

        require_once PATH_VIEW . 'main.php';
    }

    public function changePassword()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "?action=login");
            exit;
        }

        $title = "Đổi mật khẩu - PETACINEMA";
        $view  = "client/change_password";

        require_once PATH_VIEW . 'main.php';
    }

    public function changePasswordPost()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "?action=login");
            exit;
        }

        $oldPassword        = $_POST['old_password'] ?? '';
        $newPassword        = $_POST['new_password'] ?? '';
        $confirmNewPassword = $_POST['confirm_new_password'] ?? '';
        $errors             = [];

        $userId = $_SESSION['user_id'];
        $user   = $this->userModel->getById($userId);

        if (empty($oldPassword)) {
            $errors['old_password'] = 'Vui lòng nhập mật khẩu hiện tại.';
        } else {
            $isValid = password_verify($oldPassword, $user['password']) || $oldPassword === $user['password'];
            if (!$isValid) {
                $errors['old_password'] = 'Mật khẩu hiện tại không chính xác.';
            }
        }

        if (empty($newPassword)) {
            $errors['new_password'] = 'Vui lòng nhập mật khẩu mới.';
        } elseif (strlen($newPassword) < 8) {
            $errors['new_password'] = 'Mật khẩu mới phải có ít nhất 8 ký tự.';
        }

        if (empty($confirmNewPassword)) {
            $errors['confirm_new_password'] = 'Vui lòng xác nhận mật khẩu mới.';
        } elseif ($newPassword !== $confirmNewPassword) {
            $errors['confirm_new_password'] = 'Mật khẩu xác nhận không trùng khớp.';
        }

        if (!empty($errors)) {
            set_errors($errors);
            header("Location: " . BASE_URL . "?action=change_password");
            exit;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->userModel->updatePassword($userId, $hashedPassword);

        set_flash('success', 'Đổi mật khẩu thành công!');
        header("Location: " . BASE_URL . "?action=profile");
        exit;
    }
}