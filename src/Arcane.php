<?php

declare(strict_types=1);

namespace App;

final class Arcane
{
    /** @var array<string, callable> */
    private array $routes = [];

    public function get(string $path, callable $action): void
    {
        $this->routes['GET ' . $this->normalize($path)] = $action;
    }

    public function post(string $path, callable $action): void
    {
        $this->routes['POST ' . $this->normalize($path)] = $action;
    }

    public function dispatch(string $method, string $uri): void
    {
        $routeKey = $method . ' ' . $this->normalize(parse_url($uri, PHP_URL_PATH) ?: '/');

        if (!isset($this->routes[$routeKey])) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        ($this->routes[$routeKey])();
    }

    private function normalize(string $path): string
    {
        if ($path === '') {
            return '/';
        }

        return rtrim($path, '/') ?: '/';
    }
}
