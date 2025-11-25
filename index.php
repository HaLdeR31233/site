<?php
class Router {
    private $routes = [];
    
    public function addRoute($path, $template, $title) {
        $this->routes[$path] = [
            'template' => $template,
            'title' => $title
        ];
    }
    
    public function getCurrentPath() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = trim($path, '/');
        return $path ?: 'home';
    }
    
    public function dispatch() {
        $path = $this->getCurrentPath();
        
        if (isset($this->routes[$path])) {
            $route = $this->routes[$path];
            return [
                'template' => $route['template'],
                'title' => $route['title'],
                'path' => $path
            ];
        }
        
        return [
            'template' => 'pages/404.php',
            'title' => '404 - Сторінку не знайдено',
            'path' => '404'
        ];
    }
}

$router = new Router();

$router->addRoute('home', 'pages/home.php', 'Перевірена нерухомість на мапі');
$router->addRoute('', 'pages/home.php', 'Перевірена нерухомість на мапі');
$router->addRoute('login', 'pages/login.php', 'Вхід - DIM.RIA');
$router->addRoute('register', 'pages/login.php', 'Реєстрація - DIM.RIA');

try {
    $route = $router->dispatch();
    $currentPath = $route['path'];
    $pageTitle = $route['title'];
    $template = $route['template'];
    
    if (file_exists($template)) {
        include $template;
    } else {
        http_response_code(404);
        include 'pages/404.php';
    }
} catch (Exception $e) {
    http_response_code(500);
    include 'pages/500.php';
}
