<?php
class PrescriptionModel extends BaseModel {
    protected $table = 'prescriptions';
    
    public function create($data) {
        $sql = "INSERT INTO prescriptions (appointment_id, diagnosis, medications, notes, file_path) 
                VALUES (?, ?, ?, ?, ?)";
        return $this->execute($sql, "issss", [
            $data['appointment_id'], 
            $data['diagnosis'], 
            $data['medications'], 
            $data['notes'] ?? null, 
            $data['file_path'] ?? null
        ]);
    }
    
    public function findByAppointmentId($appointmentId) {
        $result = $this->execute(
            "SELECT * FROM prescriptions WHERE appointment_id = ?",
            "i", [$appointmentId]
        );
        return $result->fetch_assoc();
    }
    
    public function findById($id) {
        $result = $this->execute(
            "SELECT p.*, a.appt_date, a.doctor_id, a.patient_id,
                    doc.name as doctor_name, pat.name as patient_name
             FROM prescriptions p
             JOIN appointments a ON p.appointment_id = a.id
             JOIN doctors d ON a.doctor_id = d.id
             JOIN users doc ON d.user_id = doc.id
             JOIN users pat ON a.patient_id = pat.id
             WHERE p.id = ?",
            "i", [$id]
        );
        return $result->fetch_assoc();
    }
    
    public function getByPatient($patientId) {
        $sql = "SELECT p.*, a.appt_date, u.name as doctor_name, d.id as doctor_id
                FROM prescriptions p
                JOIN appointments a ON p.appointment_id = a.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users u ON d.user_id = u.id
                WHERE a.patient_id = ?
                ORDER BY a.appt_date DESC";
        $result = $this->execute($sql, "i", [$patientId]);
        $prescriptions = [];
        while ($row = $result->fetch_assoc()) {
            $prescriptions[] = $row;
        }
        return $prescriptions;
    }
    
    public function getByDoctor($doctorId) {
        $sql = "SELECT p.*, a.appt_date, pat.name as patient_name
                FROM prescriptions p
                JOIN appointments a ON p.appointment_id = a.id
                JOIN users pat ON a.patient_id = pat.id
                WHERE a.doctor_id = ?
                ORDER BY a.appt_date DESC";
        $result = $this->execute($sql, "i", [$doctorId]);
        $prescriptions = [];
        while ($row = $result->fetch_assoc()) {
            $prescriptions[] = $row;
        }
        return $prescriptions;
    }
    
    public function existsForAppointment($appointmentId) {
        $result = $this->execute(
            "SELECT COUNT(*) as total FROM prescriptions WHERE appointment_id = ?",
            "i", [$appointmentId]
        );
        $row = $result->fetch_assoc();
        return $row['total'] > 0;
    }
    
    public function update($id, $data) {
        $sql = "UPDATE prescriptions SET diagnosis = ?, medications = ?, notes = ?, file_path = ? 
                WHERE id = ?";
        return $this->execute($sql, "ssssi", [
            $data['diagnosis'],
            $data['medications'],
            $data['notes'] ?? null,
            $data['file_path'] ?? null,
            $id
        ]);
    }
    
    public function delete($id) {
        $prescription = $this->findById($id);
        if ($prescription && $prescription['file_path']) {
            $filePath = UPLOAD_PATH . 'prescriptions/' . $prescription['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        return $this->execute("DELETE FROM prescriptions WHERE id = ?", "i", [$id]);
    }
    
    public function getCountByPatient($patientId) {
        $result = $this->execute(
            "SELECT COUNT(*) as total FROM prescriptions p 
             JOIN appointments a ON p.appointment_id = a.id 
             WHERE a.patient_id = ?",
            "i", [$patientId]
        );
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>