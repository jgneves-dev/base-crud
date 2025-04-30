<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\PostController;
use App\Controllers\AuthController;
use FastRoute\RouteCollector;

$dispatcher = FastRoute\simpleDispatcher(function(RouteCollector $r) {
    // Post routes
    $r->addRoute('GET', '/posts', [PostController::class, 'index']);
    $r->addRoute('GET', '/posts/create', [PostController::class, 'create']);
    $r->addRoute('POST', '/posts/store', [PostController::class, 'store']);
    $r->addRoute('GET', '/posts/edit/{id:\d+}', [PostController::class, 'edit']);
    $r->addRoute('POST', '/posts/update/{id:\d+}', [PostController::class, 'update']);
    $r->addRoute('POST', '/posts/delete/{id:\d+}', [PostController::class, 'delete']);

    // Auth routes
    $r->addRoute('GET', '/login', [AuthController::class, 'showLogin']);
    $r->addRoute('POST', '/login', [AuthController::class, 'login']);
    $r->addRoute('GET', '/register', [AuthController::class, 'showRegister']);
    $r->addRoute('POST', '/register', [AuthController::class, 'register']);
    $r->addRoute('GET', '/logout', [AuthController::class, 'logout']);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 Not Found';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '405 Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        [$class, $method] = $handler;
        (new $class())->$method(...array_values($vars));
        break;
}
