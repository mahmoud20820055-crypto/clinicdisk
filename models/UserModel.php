<?php
class UserModel extends BaseModel {
    protected $table = 'users';
    
    public function findById($id) {
        $result = $this->execute(
            "SELECT id, name, email, role, phone, avatar, is_active, first_login, created_at FROM users WHERE id = ?",
            "i", [$id]
        );
        return $result->fetch_assoc();
    }
    
    public function findByEmail($email) {
        $result = $this->execute(
            "SELECT * FROM users WHERE email = ?",
            "s", [$email]
        );
        return $result->fetch_assoc();
    }
    
    public function create($data) {
        $sql = "INSERT INTO users (name, email, password, role, phone, first_login) 
                VALUES (?, ?, ?, ?, ?, 1)";
        return $this->execute($sql, "sssss", [
            $data['name'], 
            $data['email'], 
            $data['password'], 
            $data['role'], 
            $data['phone'] ?? null
        ]);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE users SET name = ?, phone = ? WHERE id = ?";
        return $this->execute($sql, "ssi", [$data['name'], $data['phone'] ?? null, $id]);
    }
    
    public function updatePassword($id, $newHash) {
        $sql = "UPDATE users SET password = ?, first_login = 0 WHERE id = ?";
        return $this->execute($sql, "si", [$newHash, $id]);
    }
    
    public function updateAvatar($id, $avatarPath) {
        $sql = "UPDATE users SET avatar = ? WHERE id = ?";
        return $this->execute($sql, "si", [$avatarPath, $id]);
    }
    
    public function toggleActive($id) {
        $user = $this->findById($id);
        $newStatus = $user['is_active'] ? 0 : 1;
        $sql = "UPDATE users SET is_active = ? WHERE id = ?";
        return $this->execute($sql, "ii", [$newStatus, $id]);
    }
    
    public function getAllPaginated($page, $perPage = 10, $roleFilter = '', $search = '') {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT id, name, email, role, phone, is_active, created_at FROM users WHERE 1=1";
        $params = [];
        $types = "";
        
        if (!empty($roleFilter)) {
            $sql .= " AND role = ?";
            $params[] = $roleFilter;
            $types .= "s";
        }
        
        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR email LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "ss";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        $types .= "ii";
        
        $result = $this->execute($sql, $types, $params);
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        return $users;
    }
    
    public function countAll($roleFilter = '', $search = '') {
        $sql = "SELECT COUNT(*) as total FROM users WHERE 1=1";
        $params = [];
        $types = "";
        
        if (!empty($roleFilter)) {
            $sql .= " AND role = ?";
            $params[] = $roleFilter;
            $types .= "s";
        }
        
        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR email LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "ss";
        }
        
        if (!empty($params)) {
            $result = $this->execute($sql, $types, $params);
        } else {
            $result = $this->execute($sql);
        }
        
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    public function getStatsByRole() {
        $result = $this->execute(
            "SELECT role, COUNT(*) as total FROM users GROUP BY role"
        );
        $stats = [];
        while ($row = $result->fetch_assoc()) {
            $stats[$row['role']] = $row['total'];
        }
        return $stats;
    }
    
    public function getActiveCount() {
        $result = $this->execute("SELECT COUNT(*) as total FROM users WHERE is_active = 1");
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    public function delete($id) {
        return $this->execute("DELETE FROM users WHERE id = ?", "i", [$id]);
    }
}
?>