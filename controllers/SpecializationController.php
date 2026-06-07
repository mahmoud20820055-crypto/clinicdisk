<?php
class SpecializationController {
    
    public function indexAction() {
        Auth::requireRole('admin');
        $specModel = new SpecializationModel();
        $specializations = $specModel->getAll();
        require_once APP_ROOT . '/views/specializations/index.php';
    }
    
    public function createAction() {
        Auth::requireRole('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate();
        } else {
            require_once APP_ROOT . '/views/specializations/create.php';
        }
    }
    
    private function handleCreate() {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            header('Location: index.php?page=specializations&action=create');
            exit();
        }
        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Specialization name is required'];
            header('Location: index.php?page=specializations&action=create');
            exit();
        }
        $specModel = new SpecializationModel();
        if ($specModel->create($name)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Specialization added successfully'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Failed to add specialization'];
        }
        header('Location: index.php?page=specializations');
        exit();
    }
    
    public function editAction() {
        Auth::requireRole('admin');
        $id = $_GET['id'] ?? 0;
        $specModel = new SpecializationModel();
        $specialization = $specModel->findById($id);
        if (!$specialization) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Specialization not found'];
            header('Location: index.php?page=specializations');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEdit($id);
        } else {
            require_once APP_ROOT . '/views/specializations/edit.php';
        }
    }
    
    private function handleEdit($id) {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            header('Location: index.php?page=specializations&action=edit&id=' . $id);
            exit();
        }
        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Specialization name is required'];
            header('Location: index.php?page=specializations&action=edit&id=' . $id);
            exit();
        }
        $specModel = new SpecializationModel();
        if ($specModel->update($id, $name)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Specialization updated successfully'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Failed to update specialization'];
        }
        header('Location: index.php?page=specializations');
        exit();
    }
    
    public function deleteAction() {
        Auth::requireRole('admin');
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            header('Location: index.php?page=specializations');
            exit();
        }
        $id = $_POST['id'] ?? 0;
        $specModel = new SpecializationModel();
        if (!$specModel->isSafeToDelete($id)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Cannot delete specialization because it has associated doctors'];
            header('Location: index.php?page=specializations');
            exit();
        }
        if ($specModel->delete($id)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Specialization deleted successfully'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Failed to delete specialization'];
        }
        header('Location: index.php?page=specializations');
        exit();
    }
}
?> 