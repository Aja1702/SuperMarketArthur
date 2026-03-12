<?php

require_once __DIR__ . '/core/Router.php';

$router = new Core\Router();

// Sintaxis: $router->add('MÉTODO', '/url', [Controlador::class, 'método']);

// --- Rutas Principales ---
$router->add('GET', '/SuperMarketArthur/', [\App\Controllers\HomeController::class, 'index']);
$router->add('GET', '/SuperMarketArthur/ofertas', [\App\Controllers\OfertasController::class, 'index']);
$router->add('GET', '/SuperMarketArthur/productos', [\App\Controllers\ProductoController::class, 'index']);
$router->add('GET', '/SuperMarketArthur/producto', [\App\Controllers\ProductoController::class, 'show']);
$router->add('GET', '/SuperMarketArthur/sobre-nosotros', [\App\Controllers\SobreNosotrosController::class, 'index']);
$router->add('GET', '/SuperMarketArthur/soporte', [\App\Controllers\SoporteController::class, 'index']);
$router->add('GET', '/SuperMarketArthur/contacto', [\App\Controllers\ContactoController::class, 'index']);

// --- Rutas de Autenticación ---
$router->add('GET', '/SuperMarketArthur/login', [\App\Controllers\AuthController::class, 'showLoginForm']);
$router->add('POST', '/SuperMarketArthur/login', [\App\Controllers\AuthController::class, 'processLogin']);
$router->add('GET', '/SuperMarketArthur/registro', [\App\Controllers\AuthController::class, 'showRegisterForm']);
$router->add('POST', '/SuperMarketArthur/registro', [\App\Controllers\AuthController::class, 'processRegister']);
$router->add('POST', '/SuperMarketArthur/logout', [\App\Controllers\AuthController::class, 'logout']);

// --- Rutas de Área de Usuario ---
$router->add('GET', '/SuperMarketArthur/favoritos', [\App\Controllers\FavoritosController::class, 'index']);
$router->add('GET', '/SuperMarketArthur/mi-cuenta', [\App\Controllers\AccountController::class, 'index']);

// --- Rutas del Panel de Administración ---
$router->add('GET', '/SuperMarketArthur/admin', [\App\Controllers\AdminController::class, 'index']);
$router->add('GET', '/SuperMarketArthur/admin/pedidos', [\App\Controllers\AdminPedidosController::class, 'index']);
$router->add('GET', '/SuperMarketArthur/admin/productos', [\App\Controllers\AdminProductosController::class, 'index']);
$router->add('GET', '/SuperMarketArthur/admin/productos/nuevo', [\App\Controllers\AdminProductosController::class, 'showNewProductForm']);
$router->add('POST', '/SuperMarketArthur/admin/productos/crear', [\App\Controllers\AdminProductosController::class, 'createProduct']);
$router->add('GET', '/SuperMarketArthur/admin/productos/editar', [\App\Controllers\AdminProductosController::class, 'showEditForm']);
$router->add('POST', '/SuperMarketArthur/admin/productos/actualizar', [\App\Controllers\AdminProductosController::class, 'updateProduct']);
$router->add('GET', '/SuperMarketArthur/admin/productos/eliminar', [\App\Controllers\AdminProductosController::class, 'showDeleteConfirmation']); // <-- RUTA DE CONFIRMACIÓN
$router->add('POST', '/SuperMarketArthur/admin/productos/eliminar', [\App\Controllers\AdminProductosController::class, 'deleteProduct']);      // <-- RUTA DE EJECUCIÓN
$router->add('GET', '/SuperMarketArthur/admin/usuarios', [\App\Controllers\AdminUsuariosController::class, 'index']);
$router->add('GET', '/SuperMarketArthur/admin/categorias', [\App\Controllers\AdminCategoriasController::class, 'index']);
$router->add('GET', '/SuperMarketArthur/admin/config', [\App\Controllers\AdminConfigController::class, 'index']);
$router->add('POST', '/SuperMarketArthur/admin/config/save', [\App\Controllers\AdminConfigController::class, 'save']);

// --- Rutas del Proceso de Compra ---
$router->add('GET', '/SuperMarketArthur/checkout', [\App\Controllers\CheckoutController::class, 'index']);
$router->add('POST', '/SuperMarketArthur/checkout', [\App\Controllers\CheckoutController::class, 'processOrder']);
$router->add('GET', '/SuperMarketArthur/order-confirmation', [\App\Controllers\OrderConfirmationController::class, 'index']);

// --- Rutas de Contenido Legal ---
$router->add('GET', '/SuperMarketArthur/privacidad', [\App\Controllers\LegalController::class, 'privacidad']);
$router->add('GET', '/SuperMarketArthur/terminos', [\App\Controllers\LegalController::class, 'terminos']);
$router->add('GET', '/SuperMarketArthur/cookies', [\App\Controllers\LegalController::class, 'cookies']);

// --- API del Lado del Servidor ---
$router->add('GET', '/SuperMarketArthur/api/cart/items', [\App\Controllers\CartController::class, 'getItems']);
$router->add('POST', '/SuperMarketArthur/api/cart/add', [\App\Controllers\CartController::class, 'addItem']);
$router->add('POST', '/SuperMarketArthur/api/cart/update', [\App\Controllers\CartController::class, 'updateQuantity']);
$router->add('POST', '/SuperMarketArthur/api/favorite/toggle', [\App\Controllers\FavoriteController::class, 'toggle']);

// --- Ruta para procesar valoraciones ---
$router->add('POST', '/SuperMarketArthur/rating/submit', [\App\Controllers\RatingController::class, 'submitRating']);

// --- API para la Búsqueda (AJAX) ---
$router->add('GET', '/SuperMarketArthur/api/search', [\App\Controllers\SearchController::class, 'search']);

return $router;
