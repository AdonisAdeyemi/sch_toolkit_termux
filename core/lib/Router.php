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

    
public function dispatch($currentUri, $currentMethod)
{
    $currentUri = strtok($currentUri, '?');

    if (!isset($this->routes[$currentMethod])) {
        http_response_code(404);
        echo json_encode(['error' => 'Method not supported']);
        return;
    }

    foreach ($this->routes[$currentMethod] as $route => $handler) {

        // Convert:
        // /classes/{classId}/subjects
        // into:
        // #^/classes/([^/]+)/subjects$#

        $pattern = preg_replace(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            '([^/]+)',
            $route
        );

        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $currentUri, $matches)) {

            array_shift($matches); // remove full match

            [$controllerClass, $method] = $handler;

            $controller = new $controllerClass($this->pdo);

            $request = [
                'get'  => $_GET,
                'post' => $_POST,
                'json' => json_decode(file_get_contents("php://input"), true),
                'body' => file_get_contents("php://input"),
            ];

            return $controller->$method(
                $request,
                ...$matches
            );
        }
    }

    http_response_code(404);

    echo json_encode([
        'error' => "Route '$currentUri' not found"
    ]);
}
}


?>




