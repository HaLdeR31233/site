<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Classes\Viewer;
use App\Classes\HomePageController;
use App\Classes\AboutMeController;


ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');


$logger = null; 


$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = trim($path, '/');


try {
    $viewer = new Viewer(__DIR__ . '/../src/templates', __DIR__ . '/../temp');

    switch ($path) {
        case '':
        case 'home':
            $controller = new HomePageController($viewer);
            $controller->index();
            break;

        case 'aboutme':
            $controller = new AboutMeController($viewer);
            $controller->show();
            break;

        default:
            echo "404 Not Found";
            break;
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
