<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, string $controller, string $action): void
    {
        $this->routes[] = compact('method', 'pattern', 'controller', 'action');
    }

    public function get(string $pattern, string $controller, string $action): void
    {
        $this->add('GET', $pattern, $controller, $action);
    }

    public function post(string $pattern, string $controller, string $action): void
    {
        $this->add('POST', $pattern, $controller, $action);
    }

    public function dispatch(string $url, string $method): void
    {
        $url = rtrim($url, '/') ?: '/';

        foreach ($this->routes as $route) {
            if (strtoupper($route['method']) !== strtoupper($method)) continue;

            $pattern = $this->convertPattern($route['pattern']);

            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches); // Remove full match

                // Named captures are also included as numeric captures by preg_match.
                // Keep only numeric captures so controller actions receive each parameter once.
                $matches = array_values(array_filter(
                    $matches,
                    static fn (int|string $key): bool => is_int($key),
                    ARRAY_FILTER_USE_KEY
                ));

                $controllerClass = 'App\\Controllers\\' . $route['controller'];
                if (!class_exists($controllerClass)) {
                    $this->notFound();
                    return;
                }

                $controller = new $controllerClass();
                $action = $route['action'];

                if (!method_exists($controller, $action)) {
                    $this->notFound();
                    return;
                }

                call_user_func_array([$controller, $action], array_values($matches));
                return;
            }
        }

        $this->notFound();
    }

    private function convertPattern(string $pattern): string
    {
        // Convert :param to named capture groups
        $pattern = preg_replace('/\/:([a-zA-Z_]+)/', '/(?P<$1>[^/]+)', $pattern);
        // Convert * wildcard
        $pattern = str_replace('*', '.*', $pattern);
        return '#^' . $pattern . '$#';
    }

    private function notFound(): void
    {
        http_response_code(404);
        if (file_exists(ROOT_PATH . '/app/views/frontend/pages/404.php')) {
            require ROOT_PATH . '/app/views/frontend/pages/404.php';
        } else {
            echo '<h1>404 - Không tìm thấy trang</h1>';
        }
    }
}
