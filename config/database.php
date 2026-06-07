<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'clinicdesk_db');

// App Configuration
define('APP_NAME', 'ClinicDesk');
define('APP_URL', 'http://localhost/clinicdesk');
define('APP_ROOT', dirname(__DIR__));

// Upload Configuration
define('MAX_FILE_SIZE', 3145728); // 3MB
define('UPLOAD_PATH', APP_ROOT . '/public/uploads/');
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/jpg']);
define('ALLOWED_PDF_TYPE', 'application/pdf');

// Session & Error Settings
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
session_start();
?>