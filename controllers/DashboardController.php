<?php
class DashboardController {
    
    public function indexAction() {
        Auth::requireRole(['admin', 'doctor', 'patient']);
        
        $role = Auth::role();
        $data = [];
        
        if ($role == 'admin') {
            $userModel = new UserModel();
            $data['stats'] = $userModel->getStatsByRole();
            $appModel = new AppointmentModel();
            $data['today_appointments'] = $appModel->getTodayCount();
            $data['total_users'] = $userModel->countAll();
            $data['active_users'] = $userModel->getActiveCount();
        } 
        elseif ($role == 'doctor') {
            $docModel = new DoctorModel();
            $doc = $docModel->findByUserId(Auth::id());
            if ($doc) {
                $appModel = new AppointmentModel();
                $data['today_appointments'] = $appModel->getTodayAppointments($doc['id']);
                $data['today_count'] = count($data['today_appointments']);
                $data['stats'] = $appModel->getStatsByStatus($doc['id']);
                $data['upcoming_count'] = $appModel->getTodayCount($doc['id']);
            }
        } 
        elseif ($role == 'patient') {
            $appModel = new AppointmentModel();
            $data['active_appointments'] = $appModel->getUpcomingAppointments(Auth::id(), 5);
            $data['active_count'] = count($data['active_appointments']);
            
            $presModel = new PrescriptionModel();
            $data['prescriptions_count'] = $presModel->getCountByPatient(Auth::id());
        }
        
        require_once APP_ROOT . '/views/dashboard.php';
    }
}
?>