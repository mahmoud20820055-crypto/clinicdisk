<?php
class AppointmentModel extends BaseModel {
    protected $table = 'appointments';
    
    public function book($data) {
        $sql = "INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, reason) 
                VALUES (?, ?, ?, ?, ?)";
        return $this->execute($sql, "iisss", [
            $data['patient_id'], 
            $data['doctor_id'], 
            $data['date'], 
            $data['time'], 
            $data['reason']
        ]);
    }
    
    public function hasConflict($doctorId, $date, $time, $excludeId = null) {
        $sql = "SELECT COUNT(*) as total FROM appointments 
                WHERE doctor_id = ? AND appt_date = ? AND appt_time = ? 
                AND status != 'cancelled'";
        $params = [$doctorId, $date, $time];
        $types = "iss";
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
            $types .= "i";
        }
        
        $result = $this->execute($sql, $types, $params);
        $row = $result->fetch_assoc();
        return $row['total'] > 0;
    }
    
    public function getByPatient($patientId, $page, $perPage = 10, $filters = []) {
        $offset = ($page - 1) * $perPage;
        list($where, $params, $types) = $this->buildFilters($filters);
        
        $sql = "SELECT a.*, u.name as doctor_name, s.name as specialization_name,
                       p.name as patient_name
                FROM appointments a
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users u ON d.user_id = u.id
                JOIN specializations s ON d.specialization_id = s.id
                JOIN users p ON a.patient_id = p.id
                WHERE a.patient_id = ? $where
                ORDER BY a.appt_date DESC, a.appt_time DESC
                LIMIT ? OFFSET ?";
        
        array_unshift($params, $patientId);
        array_unshift($types, "i");
        $params[] = $perPage;
        $params[] = $offset;
        $types .= "ii";
        
        $result = $this->execute($sql, $types, $params);
        $appointments = [];
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
        return $appointments;
    }
    
    public function getByDoctor($doctorId, $page, $perPage = 10, $filters = []) {
        $offset = ($page - 1) * $perPage;
        list($where, $params, $types) = $this->buildFilters($filters);
        
        $sql = "SELECT a.*, p.name as patient_name, p.email as patient_email, p.phone as patient_phone
                FROM appointments a
                JOIN users p ON a.patient_id = p.id
                WHERE a.doctor_id = ? $where
                ORDER BY a.appt_date ASC, a.appt_time ASC
                LIMIT ? OFFSET ?";
        
        array_unshift($params, $doctorId);
        array_unshift($types, "i");
        $params[] = $perPage;
        $params[] = $offset;
        $types .= "ii";
        
        $result = $this->execute($sql, $types, $params);
        $appointments = [];
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
        return $appointments;
    }
    
    public function getAll($page, $perPage = 10, $filters = []) {
        $offset = ($page - 1) * $perPage;
        list($where, $params, $types) = $this->buildFilters($filters);
        
        $sql = "SELECT a.*, pat.name as patient_name, doc.name as doctor_name, s.name as specialization_name
                FROM appointments a
                JOIN users pat ON a.patient_id = pat.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users doc ON d.user_id = doc.id
                JOIN specializations s ON d.specialization_id = s.id
                WHERE 1=1 $where
                ORDER BY a.appt_date DESC, a.appt_time DESC
                LIMIT ? OFFSET ?";
        
        $params[] = $perPage;
        $params[] = $offset;
        $types .= "ii";
        
        $result = $this->execute($sql, $types, $params);
        $appointments = [];
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
        return $appointments;
    }
    
    private function buildFilters($filters) {
        $where = "";
        $params = [];
        $types = "";
        
        if (!empty($filters['status'])) {
            $where .= " AND a.status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }
        
        if (!empty($filters['doctor_id'])) {
            $where .= " AND a.doctor_id = ?";
            $params[] = $filters['doctor_id'];
            $types .= "i";
        }
        
        if (!empty($filters['patient_name'])) {
            $where .= " AND pat.name LIKE ?";
            $params[] = "%{$filters['patient_name']}%";
            $types .= "s";
        }
        
        if (!empty($filters['start_date'])) {
            $where .= " AND a.appt_date >= ?";
            $params[] = $filters['start_date'];
            $types .= "s";
        }
        
        if (!empty($filters['end_date'])) {
            $where .= " AND a.appt_date <= ?";
            $params[] = $filters['end_date'];
            $types .= "s";
        }
        
        return [$where, $params, $types];
    }
    
    public function updateStatus($id, $status, $notes = null, $cancellationReason = null) {
        $sql = "UPDATE appointments SET status = ?";
        $params = [$status];
        $types = "s";
        
        if ($notes !== null) {
            $sql .= ", doctor_notes = ?";
            $params[] = $notes;
            $types .= "s";
        }
        
        if ($cancellationReason !== null) {
            $sql .= ", cancellation_reason = ?, cancelled_at = NOW()";
            $params[] = $cancellationReason;
            $types .= "s";
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;
        $types .= "i";
        
        return $this->execute($sql, $types, $params);
    }
    
    public function findById($id) {
        $sql = "SELECT a.*, pat.name as patient_name, pat.id as patient_id, pat.email as patient_email,
                       doc.name as doctor_name, d.user_id as doctor_user_id
                FROM appointments a
                JOIN users pat ON a.patient_id = pat.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users doc ON d.user_id = doc.id
                WHERE a.id = ?";
        $result = $this->execute($sql, "i", [$id]);
        return $result->fetch_assoc();
    }
    
    public function getTodayAppointments($doctorId = null) {
        $sql = "SELECT a.*, p.name as patient_name 
                FROM appointments a
                JOIN users p ON a.patient_id = p.id
                WHERE a.appt_date = CURDATE() AND a.status != 'cancelled'";
        $params = [];
        $types = "";
        
        if ($doctorId) {
            $sql .= " AND a.doctor_id = ?";
            $params[] = $doctorId;
            $types = "i";
        }
        
        $sql .= " ORDER BY a.appt_time ASC";
        
        $result = $this->execute($sql, $types, $params);
        $appointments = [];
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
        return $appointments;
    }
    
    public function getTodayCount($doctorId = null) {
        $sql = "SELECT COUNT(*) as total FROM appointments 
                WHERE appt_date = CURDATE() AND status != 'cancelled'";
        $params = [];
        $types = "";
        
        if ($doctorId) {
            $sql .= " AND doctor_id = ?";
            $params[] = $doctorId;
            $types = "i";
        }
        
        $result = $this->execute($sql, $types, $params);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    public function getStatsByStatus($doctorId = null) {
        $sql = "SELECT status, COUNT(*) as total FROM appointments ";
        $params = [];
        $types = "";
        
        if ($doctorId) {
            $sql .= " WHERE doctor_id = ?";
            $params[] = $doctorId;
            $types = "i";
        }
        
        $sql .= " GROUP BY status";
        
        $result = $this->execute($sql, $types, $params);
        $stats = [];
        while ($row = $result->fetch_assoc()) {
            $stats[$row['status']] = $row['total'];
        }
        return $stats;
    }
    
    public function getUpcomingAppointments($patientId, $limit = 5) {
        $sql = "SELECT a.*, u.name as doctor_name
                FROM appointments a
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users u ON d.user_id = u.id
                WHERE a.patient_id = ? AND a.appt_date >= CURDATE() AND a.status IN ('pending', 'confirmed')
                ORDER BY a.appt_date ASC, a.appt_time ASC
                LIMIT ?";
        $result = $this->execute($sql, "ii", [$patientId, $limit]);
        $appointments = [];
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
        return $appointments;
    }
    
    public function getWeeklyAppointments($doctorId, $weekOffset = 0) {
        $date = date('Y-m-d', strtotime("$weekOffset weeks"));
        $startOfWeek = date('Y-m-d', strtotime('monday this week', strtotime($date)));
        $endOfWeek = date('Y-m-d', strtotime('sunday this week', strtotime($date)));
        
        $sql = "SELECT a.*, p.name as patient_name, p.phone as patient_phone
                FROM appointments a
                JOIN users p ON a.patient_id = p.id
                WHERE a.doctor_id = ? AND a.appt_date BETWEEN ? AND ?
                AND a.status != 'cancelled'
                ORDER BY a.appt_date ASC, a.appt_time ASC";
        $result = $this->execute($sql, "iss", [$doctorId, $startOfWeek, $endOfWeek]);
        $appointments = [];
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
        return $appointments;
    }
}
?>