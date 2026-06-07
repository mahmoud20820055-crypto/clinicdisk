<?php
class ProfileController {
    
    public function indexAction() {
        Auth::requireRole(['admin', 'doctor', 'patient']);
        $userModel = new UserModel();
        $user = $userModel->findById(Auth::id());
        
        if (!$user) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'User not found'];
            header('Location: index.php?page=dashboard');
            exit();
        }
        
        require_once APP_ROOT . '/views/profile/index.php';
    }
    
    public function changePasswordAction() {
        Auth::requireRole(['admin', 'doctor', 'patient']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleChangePassword();
        } else {
            require_once APP_ROOT . '/views/profile/change_password.php';
        }
    }
    
    private function handleChangePassword() {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            header('Location: index.php?page=profile&action=change_password');
            exit();
        }
        
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        
        if (empty($current) || empty($new) || empty($confirm)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'All fields are required'];
            header('Location: index.php?page=profile&action=change_password');
            exit();
        }
        
        if ($new !== $confirm) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'New passwords do not match'];
            header('Location: index.php?page=profile&action=change_password');
            exit();
        }
        
        if (strlen($new) < 6) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Password must be at least 6 characters'];
            header('Location: index.php?page=profile&action=change_password');
            exit();
        }
        
        $userModel = new UserModel();
        $user = $userModel->findById(Auth::id());
        
        if (!password_verify($current, $user['password'])) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Current password is incorrect'];
            header('Location: index.php?page=profile&action=change_password');
            exit();
        }
        
        $newHash = password_hash($new, PASSWORD_BCRYPT);
        if ($userModel->updatePassword(Auth::id(), $newHash)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Password changed successfully'];
            header('Location: index.php?page=profile');
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Failed to change password'];
            header('Location: index.php?page=profile&action=change_password');
        }
        exit();
    }
    
    public function updateAction() {
        Auth::requireRole(['admin', 'doctor', 'patient']);
        
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            header('Location: index.php?page=profile');
            exit();
        }
        
        $name = trim($_POST['name'] ?? '');
        $phone = $_POST['phone'] ?? '';
        
        if (empty($name)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Name is required'];
            header('Location: index.php?page=profile');
            exit();
        }
        
        $userModel = new UserModel();
        if ($userModel->update(Auth::id(), ['name' => $name, 'phone' => $phone])) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Profile updated'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Update failed'];
        }
        header('Location: index.php?page=profile');
        exit();
    }
}
?>