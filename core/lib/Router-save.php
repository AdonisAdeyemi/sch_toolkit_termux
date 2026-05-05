<?php
class Router {
    private $routes = [
        'GET' => [
        
        ],
        'POST' => [
        
        
        ]
    ];

    public function get($path, $action) {
        $this->routes['GET'][$path] = $action;
    }

    public function post($path, $action) {
        $this->routes['POST'][$path] = $action;
    }

    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        
        // Reject requests ending with .php so they do not hit API branches -- condider DRY... routes/auth.php shares this code too
if (substr($uri, -4) === '.php') {
    $uri = substr($uri, 0, -4); // strip .php extension
}

        
        
        

        // Clean trailing slash
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        // Check if route exists
        if (!isset($this->routes[$method][$uri])) {
            http_response_code(404);
            echo json_encode(['error' => 'Route not found']);
            return;
        }

        $action = $this->routes[$method][$uri];
        list($controller, $methodName) = explode('@', $action);

        require_once __DIR__ . "/../controllers/$controller.php";

        $instance = new $controller;
        $instance->$methodName();
    }
}


?>


















