<?php

declare(strict_types=1);

namespace App;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function resolve(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        // Versuche exakte Übereinstimmung
        if (isset($this->routes[$method][$uri])) {
            $this->callHandler($this->routes[$method][$uri]);
            return;
        }

        // Versuche dynamische Routen mit Parametern
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $pattern = preg_replace('#\{([a-zA-Z_]+)\}#', '([0-9]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $this->callHandler($handler, $matches);
                return;
            }
        }

        http_response_code(404);
        require __DIR__ . '/Views/errors/404.php';
    }

    private function callHandler(array $handler, array $params = []): void
    {
        [$controllerClass, $method] = $handler;
        $controller = new $controllerClass();
        call_user_func_array([$controller, $method], $params);
    }
}
