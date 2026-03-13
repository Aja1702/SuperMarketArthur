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
            // Verificar método HTTP
            if ($route['method'] !== strtoupper($method)) {
                continue;
            }
            
            // Comprobar coincidencia exacta primero
            if ($route['uri'] === $normalizedUri) {
                $this->executeController($route['controller']);
                return;
            }
            
            // Comprobar si la ruta tiene parámetros (/ruta/{parametro})
            // Si el parámetro se llama 'id', usar solo dígitos ([0-9]+)
            $routePattern = $route['uri'];
            if (strpos($routePattern, '{id}') !== false) {
                $routePattern = str_replace('{id}', '([0-9]+)', $routePattern);
            }
            $routePattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePattern);
            
            // Usar # como delimitador para evitar conflictos con /
            $fullPattern = '#^' . $routePattern . '$#';
            $matchResult = preg_match($fullPattern, $normalizedUri, $matches);
            
            if ($matchResult) {
                // Extraer nombres de parámetros de la ruta
                preg_match_all('/\{([^}]+)\}/', $route['uri'], $paramNames);
                $params = [];
                if (!empty($paramNames[1])) {
                    foreach ($paramNames[1] as $index => $name) {
                        $params[$name] = $matches[$index + 1];
                    }
                }
                
                // Añadir parámetros como GET
                $_GET = array_merge($_GET, $params);
                
                $this->executeController($route['controller']);
                return;
            }
        }

        // Si no se encuentra la ruta, lanzamos un 404
        $this->abort(404);
    }
    
    protected function executeController($controllerInfo)
    {
        $controller = $controllerInfo[0];
        $action = $controllerInfo[1];

        if (!class_exists($controller)) {
            throw new \Exception("Controlador no encontrado: {$controller}");
        }

        $controllerInstance = new $controller();

        if (!method_exists($controllerInstance, $action)) {
            throw new \Exception("La acción {$action} no existe en el controlador {$controller}");
        }

        $controllerInstance->$action();
    }

    protected function abort($code = 404)
    {
        http_response_code($code);
        
        // Cargar la vista de error correspondiente
        $errorFile = dirname(__DIR__, 2) . '/views/errors/' . $code . '.php';
        
        if (file_exists($errorFile)) {
            // Configurar variables mínimas necesarias para la vista
            $nombre_sitio = 'SuperMarketArthur';
            $rutas = ['base_url' => '/SuperMarketArthur/'];
            
            include $errorFile;
        } else {
            // Fallback si no existe la vista
            echo "<!DOCTYPE html>
            <html lang='es'>
            <head>
                <meta charset='UTF-8'>
                <title>Error {$code}</title>
                <style>
                    body { font-family: sans-serif; text-align: center; padding: 50px; }
                    h1 { font-size: 72px; color: #e53e3e; }
                </style>
            </head>
            <body>
                <h1>{$code}</h1>
                <p>Error {$code} - Página no encontrada</p>
                <a href='/SuperMarketArthur/'>Volver al inicio</a>
            </body>
            </html>";
        }
        exit();
    }
}
