<?php
abstract class BaseModel {
    protected $db;
    protected $table;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    protected function execute($sql, $types = "", $params = []) {
        try {
            return $this->db->query($sql, $types, $params);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    protected function escape($string) {
        return $this->db->escape($string);
    }
    
    public function lastInsertId() {
        return $this->db->lastInsertId();
    }
}
?>