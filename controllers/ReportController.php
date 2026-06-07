<?php
class ReportController {
    
    public function indexAction() {
        Auth::requireRole('admin');
        
        $appModel = new AppointmentModel();
        $docModel = new DoctorModel();
        $doctors = $docModel->getAll();
        
        $filters = [
            'start_date' => $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days')),
            'end_date' => $_GET['end_date'] ?? date('Y-m-d'),
            'doctor_id' => $_GET['doctor_id'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];
        
        $appointments = $appModel->getAll(1, 1000, $filters);
        
        // Calculate summary
        $summary = [
            'total' => count($appointments),
            'pending' => 0,
            'confirmed' => 0,
            'completed' => 0,
            'cancelled' => 0
        ];
        
        foreach ($appointments as $app) {
            if (isset($summary[$app['status']])) {
                $summary[$app['status']]++;
            }
        }
        
        // Check for CSV export
        if (isset($_GET['export']) && $_GET['export'] == 'csv') {
            $this->exportCSV($appointments, $filters);
        }
        
        require_once APP_ROOT . '/views/reports/index.php';
    }
    
    private function exportCSV($appointments, $filters) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="appointments_report_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($output, [
            'ID', 'Patient Name', 'Doctor Name', 'Specialization', 
            'Date', 'Time', 'Status', 'Reason', 'Created At'
        ]);
        
        // Add data rows
        foreach ($appointments as $app) {
            fputcsv($output, [
                $app['id'],
                $app['patient_name'],
                $app['doctor_name'],
                $app['specialization_name'],
                $app['appt_date'],
                $app['appt_time'],
                $app['status'],
                $app['reason'],
                $app['created_at']
            ]);
        }
        
        fclose($output);
        exit();
    }
}
?>