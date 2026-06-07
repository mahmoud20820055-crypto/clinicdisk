<?php
class DoctorController {
    
    public function indexAction() {
        Auth::requireRole('admin');
        
        $page = $_GET['p'] ?? 1;
        $search = $_GET['search'] ?? '';
        
        $doctorModel = new DoctorModel();
        $doctors = $doctorModel->getAllPaginated($page, 10, $search);
        $total = $doctorModel->countAll($search);
        
        $paginator = new Paginator($total, 10, $page);
        
        require_once APP_ROOT . '/views/doctors/index.php';
    }
    
    public function createAction() {
        Auth::requireRole('admin');
        
        if (!isset($_SESSION['temp_doctor_user_id'])) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Please create user account first'];
            header('Location: index.php?page=users&action=create');
            exit();
        }
        
        $specModel = new SpecializationModel();
        $specializations = $specModel->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate();
        } else {
            require_once APP_ROOT . '/views/doctors/create.php';
        }
    }
    
    private function handleCreate() {
        // ========== أسطر التصحيح (تمت إضافتها) ==========
        error_log("=== handleCreate started ===");
        var_dump($_SESSION['temp_doctor_user_id'] ?? 'لم يتم تعيينه بعد');
        var_dump($_POST);
        exit;
        // ==============================================
        
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            header('Location: index.php?page=doctors&action=create');
            exit();
        }
        
        $userId = $_SESSION['temp_doctor_user_id'] ?? 0;
        $tempPassword = $_SESSION['temp_password'] ?? '';
        
        if (!$userId) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Missing user ID'];
            header('Location: index.php?page=users&action=create');
            exit();
        }
        
        $availableDays = isset($_POST['available_days']) ? implode(',', $_POST['available_days']) : 'Sun,Mon,Tue,Wed,Thu';
        
        $doctorModel = new DoctorModel();
        
        $photoPath = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $photoPath = $this->uploadPhoto($_FILES['photo']);
        }
        
        $result = $doctorModel->create([
            'user_id' => $userId,
            'specialization_id' => $_POST['specialization_id'],
            'bio' => $_POST['bio'] ?? '',
            'consultation_fee' => $_POST['consultation_fee'],
            'available_days' => $availableDays,
            'photo' => $photoPath
        ]);
        
        unset($_SESSION['temp_doctor_user_id'], $_SESSION['temp_password']);
        
        if ($result) {
            $_SESSION['flash'] = [
                'type' => 'success', 
                'message' => "Doctor created successfully. Temporary password: $tempPassword"
            ];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Failed to create doctor'];
        }
        
        header('Location: index.php?page=doctors');
        exit();
    }
    
    public function editAction() {
        Auth::requireRole('admin');
        
        $id = $_GET['id'] ?? 0;
        $doctorModel = new DoctorModel();
        $doctor = $doctorModel->findById($id);
        
        if (!$doctor) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Doctor not found'];
            header('Location: index.php?page=doctors');
            exit();
        }
        
        $specModel = new SpecializationModel();
        $specializations = $specModel->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEdit($id);
        } else {
            require_once APP_ROOT . '/views/doctors/edit.php';
        }
    }
    
    private function handleEdit($id) {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            header('Location: index.php?page=doctors&action=edit&id=' . $id);
            exit();
        }
        
        $availableDays = isset($_POST['available_days']) ? implode(',', $_POST['available_days']) : 'Sun,Mon,Tue,Wed,Thu';
        
        $doctorModel = new DoctorModel();
        
        $result = $doctorModel->update($id, [
            'specialization_id' => $_POST['specialization_id'],
            'bio' => $_POST['bio'] ?? '',
            'consultation_fee' => $_POST['consultation_fee'],
            'available_days' => $availableDays
        ]);
        
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $photoPath = $this->uploadPhoto($_FILES['photo']);
            if ($photoPath) {
                $doctorModel->updatePhoto($id, $photoPath);
            }
        }
        
        if ($result) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Doctor updated successfully'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Failed to update doctor'];
        }
        
        header('Location: index.php?page=doctors');
        exit();
    }
    
    private function uploadPhoto($file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }
        
        if ($file['size'] > MAX_FILE_SIZE) {
            return null;
        }
        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'doctor_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $destination = UPLOAD_PATH . 'doctor_photos/' . $filename;
        
        // إنشاء المجلد إذا لم يكن موجوداً
        $dir = dirname($destination);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }
        
        return null;
    }
    
    public function deleteAction() {
        Auth::requireRole('admin');
        
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            header('Location: index.php?page=doctors');
            exit();
        }
        
        $id = $_POST['id'] ?? 0;
        $doctorModel = new DoctorModel();
        
        if ($doctorModel->delete($id)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Doctor deleted successfully'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Failed to delete doctor'];
        }
        
        header('Location: index.php?page=doctors');
        exit();
    }
}
?> 