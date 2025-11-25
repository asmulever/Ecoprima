<?php

declare(strict_types=1);

namespace App\Presentation\API;

use App\Presentation\API\Http\Request;
use App\Presentation\API\Http\Response;
use RuntimeException;

class Router
{
    private array $routes = [];

    public function add(string $method, string $path, callable $handler, array $middlewares = []): void
    {
        $method = strtoupper($method);
        $this->routes[$method][$path] = [
            'handler' => $handler,
            'middlewares' => $middlewares,
        ];
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        if (!isset($this->routes[$method][$path])) {
            return new Response(['error' => 'Ruta no encontrada'], 404);
        }

        $route = $this->routes[$method][$path];
        $handler = $route['handler'];

        $pipeline = array_reduce(
            array_reverse($route['middlewares']),
            fn($next, $middleware) => fn(Request $req) => $middleware->handle($req, $next),
            $handler
        );

        $result = $pipeline($request);

        if (!$result instanceof Response) {
            throw new RuntimeException('El handler debe retornar una instancia de Response');
        }

        return $result;
    }
}
