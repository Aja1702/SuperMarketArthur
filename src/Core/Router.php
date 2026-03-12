<?php

namespace App\Core;

class Router
{
    protected $routes = [];
    protected $basePath = '';

    public function __construct()
    {
        // Detectar automáticamente el base path desde la URI actual
        $this->basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ($this->basePath === '\\' || $this->basePath === '/') {
            $this->basePath = '';
        }
    }

    /**
     * Normaliza una ruta quitando el base path si está hardcodeado
     */
    private function normalizeUri($uri)
    {
        // Si la URI comienza con el base path, quitarlo
        if (!empty($this->basePath) && strpos($uri, $this->basePath) === 0) {
            $uri = substr($uri, strlen($this->basePath));
        }
        
        // Asegurar que la URI siempre empieza por /
        if (empty($uri)) {
            $uri = '/';
        } elseif ($uri[0] !== '/') {
            $uri = '/' . $uri;
        }
        
        return $uri;
    }

    public function add($method, $uri, $controller)
    {
        // Normalizar la URI antes de guardarla
        $normalizedUri = $this->normalizeUri($uri);
        
        $this->routes[] = [
            'uri' => $normalizedUri,
            'controller' => $controller,
            'method' => $method
        ];
    }

    public function get($uri, $controller)
    {
        $this->add('GET', $uri, $controller);
    }

    public function post($uri, $controller)
    {
        $this->add('POST', $uri, $controller);
    }

    public function dispatch($uri, $method)
    {
        // Normalizar la URI recibida
        $normalizedUri = $this->normalizeUri($uri);
        
        foreach ($this->routes as $route) {
            if ($route['uri'] === $normalizedUri && $route['method'] === strtoupper($method)) {

                // Descomponemos el controlador: [App\Controllers\HomeController::class, 'index']
                $controller = $route['controller'][0]; // App\Controllers\HomeController
                $action = $route['controller'][1];     // 'index'

                // Comprobamos si la clase controlador existe
                if (!class_exists($controller)) {
                    throw new \Exception("Controlador no encontrado: {$controller}");
                }

                // Creamos una instancia del controlador
                $controllerInstance = new $controller();

                // Comprobamos si el método (acción) existe en esa clase
                if (!method_exists($controllerInstance, $action)) {
                    throw new \Exception("La acción {$action} no existe en el controlador {$controller}");
                }

                // ¡Magia! Llamamos al método del controlador
                $controllerInstance->$action();
                return;
            }
        }

        // Si no se encuentra la ruta, lanzamos un 404
        $this->abort(404);
    }

    protected function abort($code = 404)
    {
        http_response_code($code);
        // (Futuro) Podríamos cargar una vista de error bonita aquí
        echo "Error {$code} - Página no encontrada";
        exit();
    }
}
