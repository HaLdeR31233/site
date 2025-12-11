<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Classes\Viewer;
use App\Classes\HomePageController;
use App\Classes\LoginController;
use App\Classes\AuthController;
use App\Classes\ErrorController;
use App\Classes\RegisterController;
use App\Classes\PropertyController;
use App\Classes\AboutMeController;
use App\Classes\MyModel;
use App\Classes\PropertyApiController;
use App\Classes\Security;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\VarDumper\VarDumper;
use Carbon\Carbon;

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

putenv('APP_DEBUG=true');

$logger = new Logger('app');
$logger->pushHandler(new StreamHandler(__DIR__ . '/php_errors.log', Logger::DEBUG));

if (getenv('APP_DEBUG') === 'true') {
    VarDumper::dump([
        'server_info' => [
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ],
        'packages_used' => [
            'monolog' => 'Logger initialized',
            'var_dumper' => 'VarDumper ready',
            'uuid' => 'UUID generator ready',
            'carbon' => 'DateTime library ready'
        ]
    ]);
}

// Фільтруємо вхідні дані для захисту від XSS
Security::sanitizePostData();
Security::sanitizeGetData();

$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = trim($path, '/');

$logger->info('Request received', ['path' => $path, 'method' => $_SERVER['REQUEST_METHOD']]);

try {
    $viewer = new Viewer(__DIR__ . '/../src/templates', __DIR__ . '/../temp');
    $myModel = new MyModel($logger);
    $apiController = new PropertyApiController($myModel, $logger);

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

        case 'properties':
            $controller = new PropertyController($viewer, $logger);
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->store();
            } else {
                $controller->index();
            }
            break;

        case (preg_match('/^properties\/(\d+)$/', $path, $matches) ? true : false):
            $controller = new PropertyController($viewer, $logger);
            $propertyId = (int) $matches[1];
            $controller->show($propertyId);
            break;

        case (preg_match('/^properties\/(\d+)\/edit$/', $path, $matches) ? true : false):
            $controller = new PropertyController($viewer, $logger);
            $propertyId = (int) $matches[1];
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->update($propertyId);
            } else {
                $controller->edit($propertyId);
            }
            break;

        case (preg_match('/^properties\/(\d+)\/delete$/', $path, $matches) ? true : false):
            $controller = new PropertyController($viewer, $logger);
            $propertyId = (int) $matches[1];
            $controller->delete($propertyId);
            break;

        case 'properties/create':
            $controller = new PropertyController($viewer, $logger);
            $controller->create();
            break;

        case 'properties/my':
            $controller = new PropertyController($viewer, $logger);
            $controller->myProperties();
            break;

        case 'properties/search':
            $controller = new PropertyController($viewer, $logger);
            $controller->search();
            break;

        case 'aboutme':
            $controller = new AboutMeController($viewer, $logger);
            $controller->show();
            break;

        case (preg_match('/^api\/properties$/', $path, $matches) ? true : false):
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $apiController->index();
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $apiController->store();
            }
            break;

        case (preg_match('/^api\/properties\/(\d+)$/', $path, $matches) ? true : false):
            $propertyId = (int) $matches[1];
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $apiController->show($propertyId);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                $apiController->update($propertyId);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                $apiController->delete($propertyId);
            }
            break;

        case 'api/properties/search':
            $apiController->search();
            break;

        case 'api/properties/statistics':
            $apiController->statistics();
            break;

        case 'api/properties/report':
            $apiController->report();
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
