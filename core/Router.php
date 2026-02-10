<?php

namespace Core;

class Router {
    protected $routes = [];

    public function add($method, $uri, $controller) {
        $this->routes[] = [
            'uri' => $uri,
            'controller' => $controller,
            'method' => $method
        ];
    }

    public function get($uri, $controller) {
        $this->add('GET', $uri, $controller);
    }

    public function post($uri, $controller) {
        $this->add('POST', $uri, $controller);
    }

    public function dispatch($uri, $method) {
        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {
                // Por ahora, simplemente requerimos el controlador
                // Más adelante, aquí instanciaremos clases y llamaremos a métodos
                require_once __DIR__ . '/../controllers/' . $route['controller'];
                return;
            }
        }

        // Si no se encuentra la ruta, lanzamos un 404 (lo mejoraremos luego)
        http_response_code(404);
        echo "404 Not Found";
    }
}
