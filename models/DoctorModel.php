<?php
class DoctorModel extends BaseModel {
    protected $table = 'doctors';
    
    public function findByUserId($userId) {
        $sql = "SELECT d.*, u.name, u.email, u.phone, u.avatar, s.name as specialization_name
                FROM doctors d
                JOIN users u ON d.user_id = u.id
                JOIN specializations s ON d.specialization_id = s.id
                WHERE d.user_id = ?";
        $result = $this->execute($sql, "i", [$userId]);
        return $result->fetch_assoc();
    }
    
    public function findById($id) {
        $sql = "SELECT d.*, u.name, u.email, u.phone, s.name as specialization_name
                FROM doctors d
                JOIN users u ON d.user_id = u.id
                JOIN specializations s ON d.specialization_id = s.id
                WHERE d.id = ?";
        $result = $this->execute($sql, "i", [$id]);
        return $result->fetch_assoc();
    }
    
    public function getAll() {
        $sql = "SELECT d.id, d.user_id, d.consultation_fee, u.name, s.name as specialization_name
                FROM doctors d
                JOIN users u ON d.user_id = u.id
                JOIN specializations s ON d.specialization_id = s.id
                WHERE u.is_active = 1
                ORDER BY u.name";
        $result = $this->execute($sql);
        $doctors = [];
        while ($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }
        return $doctors;
    }
    
    public function getAllPaginated($page, $perPage = 10, $search = '') {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT d.*, u.name, u.email, u.phone, s.name as specialization_name
                FROM doctors d
                JOIN users u ON d.user_id = u.id
                JOIN specializations s ON d.specialization_id = s.id";
        
        $params = [];
        $types = "";
        
        if (!empty($search)) {
            $sql .= " WHERE u.name LIKE ? OR s.name LIKE ?";
            $searchTerm = "%{$search}%";
            $params = [$searchTerm, $searchTerm];
            $types = "ss";
        }
        
        $sql .= " ORDER BY u.name LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        $types .= "ii";
        
        $result = $this->execute($sql, $types, $params);
        $doctors = [];
        while ($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }
        return $doctors;
    }
    
    public function create($data) {
        $sql = "INSERT INTO doctors (user_id, specialization_id, bio, consultation_fee, available_days, photo) 
                VALUES (?, ?, ?, ?, ?, ?)";
        return $this->execute($sql, "iis dss", [
            $data['user_id'], 
            $data['specialization_id'], 
            $data['bio'] ?? null,
            $data['consultation_fee'], 
            $data['available_days'],
            $data['photo'] ?? null
        ]);
    }
    
    public function update($doctorId, $data) {
        $sql = "UPDATE doctors SET specialization_id = ?, bio = ?, consultation_fee = ?, available_days = ? 
                WHERE id = ?";
        return $this->execute($sql, "is dsi", [
            $data['specialization_id'], 
            $data['bio'] ?? null,
            $data['consultation_fee'], 
            $data['available_days'], 
            $doctorId
        ]);
    }
    
    public function updatePhoto($doctorId, $photoPath) {
        $sql = "UPDATE doctors SET photo = ? WHERE id = ?";
        return $this->execute($sql, "si", [$photoPath, $doctorId]);
    }
    
    public function getAvailableDays($doctorId) {
        $sql = "SELECT available_days FROM doctors WHERE id = ?";
        $result = $this->execute($sql, "i", [$doctorId]);
        $row = $result->fetch_assoc();
        return explode(',', $row['available_days']);
    }
    
    public function countAll($search = '') {
        $sql = "SELECT COUNT(*) as total FROM doctors d JOIN users u ON d.user_id = u.id";
        if (!empty($search)) {
            $sql .= " WHERE u.name LIKE ?";
            $searchTerm = "%{$search}%";
            $result = $this->execute($sql, "s", [$searchTerm]);
        } else {
            $result = $this->execute($sql);
        }
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    public function delete($doctorId) {
        $doctor = $this->findById($doctorId);
        if ($doctor) {
            return $this->execute("DELETE FROM users WHERE id = ?", "i", [$doctor['user_id']]);
        }
        return false;
    }
    
    public function getAppointmentsCount($doctorId) {
        $result = $this->execute(
            "SELECT COUNT(*) as total FROM appointments WHERE doctor_id = ?",
            "i", [$doctorId]
        );
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>