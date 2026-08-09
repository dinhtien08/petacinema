<?php

class AccountController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();

        // Bảo vệ controller - chỉ user đăng nhập mới được truy cập
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "?action=login");
            exit;
        }
    }

    public function profile()
    {
        $userId = (int) $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);

        if (!$user) {
            header("Location: " . BASE_URL . "?action=logout");
            exit;
        }

        $title = "Thông tin tài khoản | Petacinema";
        $view = 'account_layout';
        $accountView = 'account/profile';

        require_once PATH_VIEW . 'main.php';
    }

    public function profilePost()
    {
        $userId = (int) $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);

        if (!$user) {
            header("Location: " . BASE_URL . "?action=logout");
            exit;
        }

        $fullname = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $errors = [];

        // Validate
        if (empty($fullname)) {
            $errors['fullname'] = 'Họ và tên không được để trống.';
        } elseif (mb_strlen($fullname) < 3) {
            $errors['fullname'] = 'Họ và tên phải có ít nhất 3 ký tự.';
        }

        if (empty($email)) {
            $errors['email'] = 'Email không được để trống.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không đúng định dạng.';
        } else {
            $existingUser = $this->userModel->getByEmail($email);
            if ($existingUser && $existingUser['id'] != $userId) {
                $errors['email'] = 'Email này đã được sử dụng bởi tài khoản khác.';
            }
        }

        if (!empty($errors)) {
            set_errors($errors, $_POST);
            header("Location: " . BASE_URL . "?action=account");
            exit;
        }

        // Thực hiện update
        $result = $this->userModel->editUser(
            $userId, 
            $fullname, 
            $email, 
            $user['password'], // Giữ nguyên password hiện tại
            $user['role'], 
            $user['status']
        );

        if ($result) {
            // Cập nhật lại session
            $_SESSION['user']['fullname'] = $fullname;
            $_SESSION['user']['email'] = $email;
            $_SESSION['fullname'] = $fullname;
            $_SESSION['username'] = $email;

            set_flash('success', 'Cập nhật thông tin thành công.');
        } else {
            set_flash('error', 'Có lỗi xảy ra, vui lòng thử lại sau.');
        }

        header("Location: " . BASE_URL . "?action=account");
        exit;
    }

    public function changePassword()
    {
        $userId = (int) $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);

        if (!$user) {
            header("Location: " . BASE_URL . "?action=logout");
            exit;
        }

        $title = "Đổi mật khẩu | Petacinema";
        $view = 'account_layout';
        $accountView = 'account/password';

        require_once PATH_VIEW . 'main.php';
    }

    public function changePasswordPost()
    {
        $userId = (int) $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);

        if (!$user) {
            header("Location: " . BASE_URL . "?action=logout");
            exit;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $errors = [];

        if (empty($currentPassword)) {
            $errors['current_password'] = 'Vui lòng nhập mật khẩu hiện tại.';
        } else {
            $isPasswordValid = password_verify($currentPassword, $user['password']) || $user['password'] === $currentPassword;
            if (!$isPasswordValid) {
                $errors['current_password'] = 'Mật khẩu hiện tại không chính xác.';
            }
        }

        if (empty($newPassword)) {
            $errors['new_password'] = 'Vui lòng nhập mật khẩu mới.';
        } elseif (strlen($newPassword) < 6) {
            $errors['new_password'] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
        } elseif ($newPassword === $currentPassword) {
            $errors['new_password'] = 'Mật khẩu mới không được trùng với mật khẩu hiện tại.';
        }

        if (empty($confirmPassword)) {
            $errors['confirm_password'] = 'Vui lòng xác nhận mật khẩu mới.';
        } elseif ($newPassword !== $confirmPassword) {
            $errors['confirm_password'] = 'Mật khẩu xác nhận không trùng khớp.';
        }

        if (!empty($errors)) {
            set_errors($errors, $_POST);
            header("Location: " . BASE_URL . "?action=change_password");
            exit;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $result = $this->userModel->editUser(
            $userId, 
            $user['fullname'], 
            $user['email'], 
            $hashedPassword, 
            $user['role'], 
            $user['status']
        );

        if ($result) {
            // Yêu cầu đăng nhập lại sau khi đổi mật khẩu
            unset(
                $_SESSION['user'],
                $_SESSION['user_id'],
                $_SESSION['username'],
                $_SESSION['fullname'],
                $_SESSION['role']
            );
            session_destroy();
            session_start();
            
            set_flash('success', 'Đổi mật khẩu thành công. Vui lòng đăng nhập lại.');
            header("Location: " . BASE_URL . "?action=login");
            exit;
        } else {
            set_flash('error', 'Có lỗi xảy ra, vui lòng thử lại sau.');
            header("Location: " . BASE_URL . "?action=change_password");
            exit;
        }
    }
}
