<?php

require_once __DIR__ . '/core/Router.php';

$router = new Core\Router();

// Definimos las rutas de la aplicación
// Sintaxis: $router->get('URL', 'archivo_controlador.php');

// Rutas de ejemplo (luego añadiremos todas las demás)
$router->get('/SuperMarketArthur/', 'home.php');
$router->get('/SuperMarketArthur/?userSession=login', 'login.php');
$router->post('/SuperMarketArthur/login', 'procesar_login.php');


// ... aquí añadiremos el resto de rutas (registro, productos, etc.)


return $router;
