<?php
class SpecializationModel extends BaseModel {
    protected $table = 'specializations';
    
    public function getAll() {
        $result = $this->execute("SELECT * FROM specializations ORDER BY name");
        $specializations = [];
        while ($row = $result->fetch_assoc()) {
            $specializations[] = $row;
        }
        return $specializations;
    }
    
    public function findById($id) {
        $result = $this->execute("SELECT * FROM specializations WHERE id = ?", "i", [$id]);
        return $result->fetch_assoc();
    }
    
    public function create($name) {
        return $this->execute("INSERT INTO specializations (name) VALUES (?)", "s", [$name]);
    }
    
    public function update($id, $name) {
        return $this->execute("UPDATE specializations SET name = ? WHERE id = ?", "si", [$name, $id]);
    }
    
    public function delete($id) {
        if (!$this->isSafeToDelete($id)) {
            return false;
        }
        return $this->execute("DELETE FROM specializations WHERE id = ?", "i", [$id]);
    }
    
    public function isSafeToDelete($id) {
        $result = $this->execute(
            "SELECT COUNT(*) as total FROM doctors WHERE specialization_id = ?",
            "i", [$id]
        );
        $row = $result->fetch_assoc();
        return $row['total'] == 0;
    }
    
    public function getDoctorsCount($id) {
        $result = $this->execute(
            "SELECT COUNT(*) as total FROM doctors WHERE specialization_id = ?",
            "i", [$id]
        );
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>