<?php
class Auth {
    public static function login($user) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'first_login' => $user['first_login'] ?? 1
        ];
    }
    
    public static function logout() {
        $_SESSION = [];
        session_unset();
        session_destroy();
    }
    
    public static function check() {
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }
    
    public static function currentUser() {
        return $_SESSION['user'] ?? null;
    }
    
    public static function role() {
        return $_SESSION['user']['role'] ?? '';
    }
    
    public static function id() {
        return $_SESSION['user']['id'] ?? 0;
    }
    
    public static function name() {
        return $_SESSION['user']['name'] ?? '';
    }
    
    public static function requireRole($roles) {
        if (!self::check()) {
            header('Location: index.php?page=login');
            exit();
        }
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        if (!in_array(self::role(), $roles)) {
            http_response_code(403);
            die("403 - Access Denied");
        }
    }
    
    public static function requireOwnership($userId, $message = "Access Denied") {
        if (self::role() !== 'admin' && self::id() != $userId) {
            http_response_code(403);
            die($message);
        }
    }
}
?>