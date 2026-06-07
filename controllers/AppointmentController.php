<?php
class AppointmentController {
    
    public function indexAction() {
        Auth::requireRole(['admin', 'doctor', 'patient']);
        
        $role = Auth::role();
        $page = $_GET['p'] ?? 1;
        $status = $_GET['status'] ?? '';
        $appModel = new AppointmentModel();
        
        if ($role == 'patient') {
            $appointments = $appModel->getByPatient(Auth::id(), $page, 10, ['status' => $status]);
            $total = count($appModel->getByPatient(Auth::id(), 1, 1000, ['status' => $status]));
        } elseif ($role == 'doctor') {
            $docModel = new DoctorModel();
            $doc = $docModel->findByUserId(Auth::id());
            $appointments = $appModel->getByDoctor($doc['id'], $page, 10, ['status' => $status]);
            $total = count($appModel->getByDoctor($doc['id'], 1, 1000, ['status' => $status]));
        } else {
            $appointments = $appModel->getAll($page, 10, ['status' => $status]);
            $total = count($appModel->getAll(1, 1000, ['status' => $status]));
        }
        
        $paginator = new Paginator($total, 10, $page);
        
        require_once APP_ROOT . '/views/appointments/index.php';
    }
    
    public function bookAction() {
        Auth::requireRole('patient');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleBook();
        } else {
            $this->showBookForm();
        }
    }
    
    private function showBookForm() {
        $docModel = new DoctorModel();
        $doctors = $docModel->getAll();
        require_once APP_ROOT . '/views/appointments/book.php';
    }
    
    private function handleBook() {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            header('Location: index.php?page=appointments&action=book');
            exit();
        }
        
        $doctorId = $_POST['doctor_id'] ?? 0;
        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';
        $reason = $_POST['reason'] ?? '';
        
        // Validate date not in past
        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Cannot book appointment in the past'];
            header('Location: index.php?page=appointments&action=book');
            exit();
        }
        
        $appModel = new AppointmentModel();
        
        if ($appModel->hasConflict($doctorId, $date, $time)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'This time slot is already booked'];
            header('Location: index.php?page=appointments&action=book');
            exit();
        }
        
        $result = $appModel->book([
            'patient_id' => Auth::id(),
            'doctor_id' => $doctorId,
            'date' => $date,
            'time' => $time,
            'reason' => $reason
        ]);
        
        if ($result) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Appointment booked successfully'];
            header('Location: index.php?page=appointments');
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Failed to book appointment'];
            header('Location: index.php?page=appointments&action=book');
        }
        exit();
    }
    
    public function updateStatusAction() {
        Auth::requireRole(['admin', 'doctor']);
        
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=appointments'));
            exit();
        }
        
        $id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? '';
        $notes = $_POST['notes'] ?? null;
        
        $appModel = new AppointmentModel();
        
        if ($appModel->updateStatus($id, $status, $notes)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Status updated successfully'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Failed to update status'];
        }
        
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=appointments'));
        exit();
    }
    
    public function cancelAction() {
        Auth::requireRole(['admin', 'patient']);
        
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            header('Location: index.php?page=appointments');
            exit();
        }
        
        $id = $_POST['id'] ?? 0;
        $reason = $_POST['cancellation_reason'] ?? 'Cancelled by user';
        
        $appModel = new AppointmentModel();
        $appointment = $appModel->findById($id);
        
        // Check ownership for patients
        if (Auth::role() == 'patient' && $appointment['patient_id'] != Auth::id()) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'You cannot cancel this appointment'];
            header('Location: index.php?page=appointments');
            exit();
        }
        
        if ($appModel->updateStatus($id, 'cancelled', null, $reason)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Appointment cancelled'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Failed to cancel appointment'];
        }
        
        header('Location: index.php?page=appointments');
        exit();
    }
}
?>