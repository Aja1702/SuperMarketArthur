<?php

// 1. Cargar el núcleo de la aplicación (configuración, sesión, etc.)
require_once __DIR__ . '/bootstrap.php';

// 2. Cargar el Router y las definiciones de rutas
$router = require_once __DIR__ . '/routes.php';

// 3. Capturar la URL que el usuario ha solicitado
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 4. Obtener el método de la petición (GET, POST)
$method = $_SERVER['REQUEST_METHOD'];

// 5. Despachar la ruta: el Router buscará el controlador y lo ejecutará
try {
    $router->dispatch($uri, $method);
} catch (Exception $e) {
    // (Futuro) Aquí podríamos mostrar una página de error 500 bonita
    // Por ahora, para depuración, podemos mostrar el error.
    // En un entorno de producción, esto debería registrar el error y mostrar una página amigable.
    error_log($e->getMessage()); // Registrar el error para nosotros
    // include 'views/errors/500.php'; // Mostrar una página de error bonita
}
