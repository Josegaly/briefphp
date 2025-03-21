<?php
session_start(); // Appel unique ici
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';

$database = new Database();
$pdo = $database->getConnection();

$authController = new AuthController($pdo);
$userController = new UserController($pdo);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

switch ($uri) {
    case '/login':
        $authController->login();
        break;
    case '/register':
        $authController->register();
        break;
    case '/verify-2fa':
        $authController->verify2FA();
        break;
    case '/admin-login':
        $authController->adminLogin();
        break;
    case '/dashboard':
        $userController->dashboard();
        break;
    case '/profile':
        $userController->profile();
        break;
    case '/logout':
        $authController->logout();
        break;
    case preg_match('/^\/user\/update\/(\d+)$/', $uri, $matches) ? $uri : '':
        $userController->update($matches[1]);
        break;
    case preg_match('/^\/user\/delete\/(\d+)$/', $uri, $matches) ? $uri : '':
        $userController->delete($matches[1]);
        break;
    case '/user/create':
        $userController->create();
        break;
    default:
        header("Location: /login");
        exit;
}
?>