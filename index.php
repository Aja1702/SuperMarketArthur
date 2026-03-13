<?php
// Iniciar output buffering para evitar Quirks Mode
ob_start();

// Establecer Content-Type correcto
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}

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
    // Registrar el error para nosotros
    error_log($e->getMessage());
    
    // Mostrar página de error 500 bonita
    http_response_code(500);
    $errorFile = __DIR__ . '/views/errors/500.php';
    
    if (file_exists($errorFile)) {
        $nombre_sitio = 'SuperMarketArthur';
        $rutas = ['base_url' => '/SuperMarketArthur/'];
        include $errorFile;
    } else {
        echo "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <title>Error 500</title>
        </head>
        <body>
            <h1>500 - Error del servidor</h1>
            <p>Algo salió mal. Por favor, inténtalo más tarde.</p>
            <a href='/SuperMarketArthur/'>Volver al inicio</a>
        </body>
        </html>";
    }
    exit();
}
