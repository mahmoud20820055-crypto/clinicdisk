<?php
class UserController {
    
    public function indexAction() {
        Auth::requireRole('admin');
        
        $page = $_GET['p'] ?? 1;
        $roleFilter = $_GET['role'] ?? '';
        $search = $_GET['search'] ?? '';
        
        $userModel = new UserModel();
        $users = $userModel->getAllPaginated($page, 10, $roleFilter, $search);
        $total = $userModel->countAll($roleFilter, $search);
        
        $paginator = new Paginator($total, 10, $page);
        
        require_once APP_ROOT . '/views/users/index.php';
    }
    
    public function createAction() {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate();
        } else {
            $this->showCreateForm();
        }
    }
    
    private function showCreateForm() {
        require_once APP_ROOT . '/views/users/create.php';
    }
    
    private function handleCreate() {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            header('Location: index.php?page=users&action=create');
            exit();
        }
        
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'patient';
        $phone = $_POST['phone'] ?? '';
        $tempPassword = bin2hex(random_bytes(4)); // 8 characters
        $hashedPassword = password_hash($tempPassword, PASSWORD_BCRYPT);
        
        $userModel = new UserModel();
        
        // Check if email exists
        if ($userModel->findByEmail($email)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Email already exists'];
            header('Location: index.php?page=users&action=create');
            exit();
        }
        
        $userId = $userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role,
            'phone' => $phone
        ]);
        
        if ($userId) {
            // If role is doctor, create doctor record
            if ($role == 'doctor') {
                $specModel = new SpecializationModel();
                $specializations = $specModel->getAll();
                
                // Store in session for next step
                $_SESSION['temp_doctor_user_id'] = $userId;
                $_SESSION['temp_password'] = $tempPassword;
                header('Location: index.php?page=doctors&action=create');
                exit();
            }
            
            $_SESSION['flash'] = [
                'type' => 'success', 
                'message' => "User created successfully. Temporary password: $tempPassword"
            ];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Failed to create user'];
        }
        
        header('Location: index.php?page=users');
    }
    
    public function editAction() {
        Auth::requireRole('admin');
        
        $id = $_GET['id'] ?? 0;
        $userModel = new UserModel();
        $user = $userModel->findById($id);
        
        if (!$user) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'User not found'];
            header('Location: index.php?page=users');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEdit($id);
        } else {
            require_once APP_ROOT . '/views/users/edit.php';
        }
    }
    
    private function handleEdit($id) {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            header('Location: index.php?page=users&action=edit&id=' . $id);
            exit();
        }
        
        $name = trim($_POST['name'] ?? '');
        $phone = $_POST['phone'] ?? '';
        
        $userModel = new UserModel();
        
        if ($userModel->update($id, ['name' => $name, 'phone' => $phone])) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'User updated successfully'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Failed to update user'];
        }
        
        header('Location: index.php?page=users');
    }
    
    public function toggleActiveAction() {
        Auth::requireRole('admin');
        
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            header('Location: index.php?page=users');
            exit();
        }
        
        $id = $_POST['id'] ?? 0;
        
        // Prevent self deactivation
        if ($id == Auth::id()) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'You cannot deactivate your own account'];
            header('Location: index.php?page=users');
            exit();
        }
        
        $userModel = new UserModel();
        
        if ($userModel->toggleActive($id)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'User status updated'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Failed to update user status'];
        }
        
        header('Location: index.php?page=users');
    }
    
    public function deleteAction() {
        Auth::requireRole('admin');
        
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            header('Location: index.php?page=users');
            exit();
        }
        
        $id = $_POST['id'] ?? 0;
        
        if ($id == Auth::id()) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'You cannot delete your own account'];
            header('Location: index.php?page=users');
            exit();
        }
        
        $userModel = new UserModel();
        
        if ($userModel->delete($id)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'User deleted successfully'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Failed to delete user'];
        }
        
        header('Location: index.php?page=users');
    }
}
?>