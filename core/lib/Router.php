<?php

class Router {
	private $pdo ;
    private $routes = [
        'GET' => [],
        'POST' => []
    ];
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        }

    public function get($uri, $handler) {
        $this->routes['GET'][$uri] = $handler;
    }

    public function post($uri, $handler) {
        $this->routes['POST'][$uri] = $handler;
    }

    public function dispatch($currentUri, $currentMethod) {

        // Normalize URL (optional)
        $currentUri = strtok($currentUri, '?');

        if (!isset($this->routes[$currentMethod][$currentUri])) {
            http_response_code(404);
            echo json_encode(['error' => "Route '$currentUri' not found"]);
            return;
        }

        $handler = $this->routes[$currentMethod][$currentUri];

        list($controllerClass, $method) = $handler;

        // Instantiate controller
        $controller = new $controllerClass($this->pdo);

        // 🟢 Capture input safely
        $request = [
            'get'  => $_GET,
            'post' => $_POST,
            'json' => json_decode(file_get_contents("php://input"), true),
            'body' => file_get_contents("php://input"),
        ];

        // Call controller with request object
        return $controller->$method($request);
    }
}


?>




