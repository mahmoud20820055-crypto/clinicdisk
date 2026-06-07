<?php
class PrescriptionController {
    
    public function indexAction() {
        Auth::requireRole(['admin', 'doctor', 'patient']);
        
        $role = Auth::role();
        
        if ($role == 'patient') {
            $presModel = new PrescriptionModel();
            $prescriptions = $presModel->getByPatient(Auth::id());
        } elseif ($role == 'doctor') {
            $docModel = new DoctorModel();
            $doc = $docModel->findByUserId(Auth::id());
            $presModel = new PrescriptionModel();
            $prescriptions = $presModel->getByDoctor($doc['id']);
        } else {
            // Admin - get all
            $presModel = new PrescriptionModel();
            $prescriptions = $presModel->getAll();
        }
        
        require_once APP_ROOT . '/views/prescriptions/index.php';
    }
    
    public function addAction() {
        Auth::requireRole('doctor');
        
        $appointmentId = $_GET['id'] ?? 0;
        $appModel = new AppointmentModel();
        $appointment = $appModel->findById($appointmentId);
        
        // Verify doctor owns this appointment
        $docModel = new DoctorModel();
        $doctor = $docModel->findByUserId(Auth::id());
        
        if (!$appointment || $appointment['doctor_id'] != $doctor['id']) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid appointment'];
            header('Location: index.php?page=appointments');
            exit();
        }
        
        if ($appointment['status'] != 'completed') {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Cannot add prescription to incomplete appointment'];
            header('Location: index.php?page=appointments');
            exit();
        }
        
        $presModel = new PrescriptionModel();
        if ($presModel->existsForAppointment($appointmentId)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Prescription already exists for this appointment'];
            header('Location: index.php?page=appointments');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleAdd($appointmentId);
        } else {
            require_once APP_ROOT . '/views/prescriptions/add.php';
        }
    }
    
    private function handleAdd($appointmentId) {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            header('Location: index.php?page=prescriptions&action=add&id=' . $appointmentId);
            exit();
        }
        
        $diagnosis = $_POST['diagnosis'] ?? '';
        $medications = $_POST['medications'] ?? '';
        $notes = $_POST['notes'] ?? '';
        
        if (empty($diagnosis) || empty($medications)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Diagnosis and medications are required'];
            header('Location: index.php?page=prescriptions&action=add&id=' . $appointmentId);
            exit();
        }
        
        // Handle file upload
        $filePath = null;
        if (isset($_FILES['prescription_file']) && $_FILES['prescription_file']['error'] == 0) {
            $filePath = $this->uploadFile($_FILES['prescription_file'], $appointmentId);
        }
        
        $presModel = new PrescriptionModel();
        $result = $presModel->create([
            'appointment_id' => $appointmentId,
            'diagnosis' => $diagnosis,
            'medications' => $medications,
            'notes' => $notes,
            'file_path' => $filePath
        ]);
        
        if ($result) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Prescription added successfully'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Failed to add prescription'];
        }
        
        header('Location: index.php?page=appointments');
        exit();
    }
    
    private function uploadFile($file, $appointmentId) {
        if ($file['type'] != 'application/pdf') {
            return null;
        }
        
        if ($file['size'] > MAX_FILE_SIZE) {
            return null;
        }
        
        $filename = 'prescription_' . $appointmentId . '_' . time() . '.pdf';
        $destination = UPLOAD_PATH . 'prescriptions/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }
        
        return null;
    }
    
    public function downloadAction() {
        Auth::requireRole(['admin', 'doctor', 'patient']);
        
        $id = $_GET['id'] ?? 0;
        $presModel = new PrescriptionModel();
        $prescription = $presModel->findById($id);
        
        if (!$prescription) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Prescription not found'];
            header('Location: index.php?page=prescriptions');
            exit();
        }
        
        // Check authorization
        $authorized = false;
        $role = Auth::role();
        
        if ($role == 'admin') {
            $authorized = true;
        } elseif ($role == 'doctor') {
            $docModel = new DoctorModel();
            $doctor = $docModel->findByUserId(Auth::id());
            if ($doctor && $prescription['doctor_id'] == $doctor['id']) {
                $authorized = true;
            }
        } elseif ($role == 'patient') {
            if ($prescription['patient_id'] == Auth::id()) {
                $authorized = true;
            }
        }
        
        if (!$authorized) {
            die("403 - Access Denied");
        }
        
        $filePath = UPLOAD_PATH . 'prescriptions/' . $prescription['file_path'];
        
        if (!file_exists($filePath)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'File not found'];
            header('Location: index.php?page=prescriptions');
            exit();
        }
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="prescription_' . $prescription['id'] . '.pdf"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit();
    }
}
?>