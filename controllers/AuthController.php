<?php
class AuthController {
    
    public function indexAction() {
        $this->loginAction();
    }
    
    public function loginAction() {
        if (Auth::check()) {
            header('Location: index.php?page=dashboard');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
        } else {
            $this->showLoginForm();
        }
    }
    
    private function showLoginForm() {
        $pageTitle = 'Login';
        require_once APP_ROOT . '/views/auth/login.php';
    }
    
    private function handleLogin() {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            header('Location: index.php?page=login');
            exit();
        }
        
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Email and password are required'];
            header('Location: index.php?page=login');
            exit();
        }
        
        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);
        
        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid credentials'];
            header('Location: index.php?page=login');
            exit();
        }
        
        if ($user['is_active'] != 1) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Account suspended. Contact admin.'];
            header('Location: index.php?page=login');
            exit();
        }
        
        Auth::login($user);
        
        if ($user['first_login'] == 1) {
            header('Location: index.php?page=profile&action=change_password');
        } else {
            header('Location: index.php?page=dashboard');
        }
        exit();
    }
    
    public function logoutAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            Auth::logout();
        }
        header('Location: index.php?page=login');
        exit();
    }
}
?>