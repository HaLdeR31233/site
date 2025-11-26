<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Classes\Viewer;
use App\Classes\HomePageController;
use App\Classes\LoginController;
use App\Classes\AuthController;
use App\Classes\ErrorController;
use App\Classes\RegisterController;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\VarDumper\VarDumper;
use Carbon\Carbon;

// Initialize error logging
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

// Create logger
$logger = new Logger('app');
$logger->pushHandler(new StreamHandler(__DIR__ . '/php_errors.log', Logger::DEBUG));

// Simple router
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = trim($path, '/');

$logger->info('Request received', ['path' => $path, 'method' => $_SERVER['REQUEST_METHOD']]);

try {
    $viewer = new Viewer(__DIR__ . '/../src/templates', __DIR__ . '/../temp');

    switch ($path) {
        case '':
        case 'home':
            $controller = new HomePageController($viewer, $logger);
            $controller->index();
            break;

        case 'login':
            $controller = new LoginController($viewer, $logger);
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->login();
            } else {
                $controller->showLoginForm();
            }
            break;

        case 'register':
            $controller = new RegisterController($viewer, $logger);
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->register();
            } else {
                $controller->showRegisterForm();
            }
            break;

        case 'auth':
            $controller = new AuthController($viewer, $logger);
            $controller->handleAuth();
            break;

        default:
            $controller = new ErrorController($viewer, $logger);
            if (strpos($path, 'admin') === 0) {
                $controller->error403();
            } else {
                $controller->error404();
            }
            break;
    }

} catch (Exception $e) {
    $logger->error('Application error', [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);

    $viewer = new Viewer(__DIR__ . '/../src/templates', __DIR__ . '/../temp');
    $errorController = new ErrorController($viewer, $logger);
    $errorController->error500();
}
