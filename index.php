<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/CSRF.php';
require_once __DIR__ . '/core/Paginator.php';
require_once __DIR__ . '/models/BaseModel.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/models/SpecializationModel.php';
require_once __DIR__ . '/models/DoctorModel.php';
require_once __DIR__ . '/models/AppointmentModel.php';
require_once __DIR__ . '/models/PrescriptionModel.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/DoctorController.php';
require_once __DIR__ . '/controllers/AppointmentController.php';
require_once __DIR__ . '/controllers/PrescriptionController.php';
require_once __DIR__ . '/controllers/ReportController.php';
require_once __DIR__ . '/controllers/SpecializationController.php';
require_once __DIR__ . '/controllers/ProfileController.php';

$page = $_GET['page'] ?? 'login';

if ($page == 'login' && Auth::check()) {
    header('Location: index.php?page=dashboard');
    exit();
}
if ($page != 'login' && !Auth::check()) {
    header('Location: index.php?page=login');
    exit();
}

$routes = [
    'login' => 'AuthController',
    'logout' => 'AuthController',
    'dashboard' => 'DashboardController',
    'profile' => 'ProfileController',
    'users' => 'UserController',
    'doctors' => 'DoctorController',
    'specializations' => 'SpecializationController',
    'appointments' => 'AppointmentController',
    'prescriptions' => 'PrescriptionController',
    'reports' => 'ReportController',
];

$controllerName = $routes[$page] ?? null;

if ($controllerName && class_exists($controllerName)) {
    $action = $_GET['action'] ?? 'index';
    $method = $action . 'Action';
    $controller = new $controllerName();
    if (method_exists($controller, $method)) {
        $controller->$method();
    } else {
        if (method_exists($controller, 'indexAction')) {
            $controller->indexAction();
        } else {
            http_response_code(404);
            echo "<h1>404 - Page Not Found</h1>";
        }
    }
} else {
    http_response_code(404);
    echo "<h1>404 - Page Not Found</h1>";
}
?> 
