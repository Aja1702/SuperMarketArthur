<?php

require_once __DIR__ . '/src/Core/Router.php';

// Cargar controllers manualmente
require_once __DIR__ . '/src/Controllers/HomeController.php';
require_once __DIR__ . '/src/Controllers/OfertasController.php';
require_once __DIR__ . '/src/Controllers/SobreNosotrosController.php';
require_once __DIR__ . '/src/Controllers/SoporteController.php';
require_once __DIR__ . '/src/Controllers/ContactoController.php';
require_once __DIR__ . '/src/Controllers/LegalController.php';
require_once __DIR__ . '/src/Controllers/OrderConfirmationController.php';
require_once __DIR__ . '/src/Controllers/Shop/ProductoController.php';
require_once __DIR__ . '/src/Controllers/Shop/CartController.php';
require_once __DIR__ . '/src/Controllers/Shop/CheckoutController.php';
require_once __DIR__ . '/src/Controllers/AccountController.php';
require_once __DIR__ . '/src/Controllers/Shop/FavoriteController.php';
require_once __DIR__ . '/src/Controllers/Shop/SearchController.php';
require_once __DIR__ . '/src/Controllers/Shop/RatingController.php';
require_once __DIR__ . '/src/Controllers/Auth/AuthController.php';
require_once __DIR__ . '/src/Controllers/Admin/AdminController.php';
require_once __DIR__ . '/src/Controllers/Admin/AdminProductosController.php';
require_once __DIR__ . '/src/Controllers/Admin/AdminUsuariosController.php';
require_once __DIR__ . '/src/Controllers/Admin/AdminCategoriasController.php';
require_once __DIR__ . '/src/Controllers/Admin/AdminConfigController.php';
require_once __DIR__ . '/src/Controllers/Admin/AdminPedidosController.php';

$router = new \App\Core\Router();

// --- Rutas Principales ---
$router->add('GET', '/SuperMarketArthur/', ['App\Controllers\HomeController', 'index']);
$router->add('GET', '/SuperMarketArthur/ofertas', ['App\Controllers\OfertasController', 'index']);
$router->add('GET', '/SuperMarketArthur/productos', ['App\\Controllers\\Shop\\ProductoController', 'index']);
$router->add('GET', '/SuperMarketArthur/producto', ['App\\Controllers\\Shop\\ProductoController', 'show']);
$router->add('GET', '/SuperMarketArthur/sobre-nosotros', ['App\Controllers\SobreNosotrosController', 'index']);
$router->add('GET', '/SuperMarketArthur/soporte', ['App\Controllers\SoporteController', 'index']);
$router->add('GET', '/SuperMarketArthur/contacto', ['App\Controllers\ContactoController', 'index']);
$router->add('POST', '/SuperMarketArthur/contacto', ['App\Controllers\ContactoController', 'send']);

// --- Rutas de Autenticación ---
$router->add('GET', '/SuperMarketArthur/login', ['App\\Controllers\\Auth\\AuthController', 'showLoginForm']);
$router->add('POST', '/SuperMarketArthur/login', ['App\\Controllers\\Auth\\AuthController', 'processLogin']);
$router->add('GET', '/SuperMarketArthur/registro', ['App\\Controllers\\Auth\\AuthController', 'showRegisterForm']);
$router->add('POST', '/SuperMarketArthur/registro', ['App\\Controllers\\Auth\\AuthController', 'processRegister']);
$router->add('POST', '/SuperMarketArthur/logout', ['App\\Controllers\\Auth\\AuthController', 'logout']);

// --- Rutas de Área de Usuario ---
$router->add('GET', '/SuperMarketArthur/favoritos', ['App\\Controllers\\Shop\\FavoriteController', 'index']);
$router->add('GET', '/SuperMarketArthur/mi-cuenta', ['App\Controllers\AccountController', 'index']);

// --- Rutas del Panel de Administración ---
$router->add('GET', '/SuperMarketArthur/admin', ['App\\Controllers\\Admin\\AdminController', 'index']);
$router->add('GET', '/SuperMarketArthur/admin/pedidos', ['App\\Controllers\\Admin\\AdminPedidosController', 'index']);
$router->add('GET', '/SuperMarketArthur/admin/productos', ['App\\Controllers\\Admin\\AdminProductosController', 'index']);
$router->add('GET', '/SuperMarketArthur/admin/productos/nuevo', ['App\\Controllers\\Admin\\AdminProductosController', 'showNewProductForm']);
$router->add('POST', '/SuperMarketArthur/admin/productos/crear', ['App\\Controllers\\Admin\\AdminProductosController', 'createProduct']);
$router->add('GET', '/SuperMarketArthur/admin/productos/editar', ['App\\Controllers\\Admin\\AdminProductosController', 'showEditForm']);
$router->add('POST', '/SuperMarketArthur/admin/productos/actualizar', ['App\\Controllers\\Admin\\AdminProductosController', 'updateProduct']);
$router->add('GET', '/SuperMarketArthur/admin/productos/eliminar', ['App\\Controllers\\Admin\\AdminProductosController', 'showDeleteConfirmation']);
$router->add('POST', '/SuperMarketArthur/admin/productos/eliminar', ['App\\Controllers\\Admin\\AdminProductosController', 'deleteProduct']);
$router->add('GET', '/SuperMarketArthur/admin/usuarios', ['App\\Controllers\\Admin\\AdminUsuariosController', 'index']);
$router->add('GET', '/SuperMarketArthur/admin/categorias', ['App\\Controllers\\Admin\\AdminCategoriasController', 'index']);
$router->add('GET', '/SuperMarketArthur/admin/config', ['App\\Controllers\\Admin\\AdminConfigController', 'index']);
$router->add('POST', '/SuperMarketArthur/admin/config/save', ['App\\Controllers\\Admin\\AdminConfigController', 'save']);

// --- Rutas del Proceso de Compra (Stripe) ---
$router->add('GET', '/SuperMarketArthur/checkout', ['App\\Controllers\\Shop\\CheckoutController', 'index']);
$router->add('POST', '/SuperMarketArthur/checkout/pay', ['App\\Controllers\\Shop\\CheckoutController', 'createStripeSession']);
$router->add('POST', '/SuperMarketArthur/checkout', ['App\\Controllers\\Shop\\CheckoutController', 'processOrder']);
$router->add('GET', '/SuperMarketArthur/checkout/success', ['App\\Controllers\\Shop\\CheckoutController', 'confirmStripePayment']);
$router->add('GET', '/SuperMarketArthur/order-confirmation', ['App\Controllers\Shop\OrderConfirmationController', 'index']);

// --- Rutas de Contenido Legal ---
$router->add('GET', '/SuperMarketArthur/privacidad', ['App\Controllers\LegalController', 'privacidad']);
$router->add('GET', '/SuperMarketArthur/terminos', ['App\Controllers\LegalController', 'terminos']);
$router->add('GET', '/SuperMarketArthur/cookies', ['App\Controllers\LegalController', 'cookies']);

// --- API del Lado del Servidor ---
$router->add('GET', '/SuperMarketArthur/api/cart/items', ['App\\Controllers\\Shop\\CartController', 'getItems']);
$router->add('POST', '/SuperMarketArthur/api/cart/add', ['App\\Controllers\\Shop\\CartController', 'addItem']);
$router->add('POST', '/SuperMarketArthur/api/cart/update', ['App\\Controllers\\Shop\\CartController', 'updateQuantity']);
$router->add('POST', '/SuperMarketArthur/api/favorite/toggle', ['App\\Controllers\\Shop\\FavoriteController', 'toggle']);

// --- Ruta para procesar valoraciones ---
$router->add('POST', '/SuperMarketArthur/rating/submit', ['App\\Controllers\\Shop\\RatingController', 'submitRating']);

// --- API para la Búsqueda (AJAX) ---
$router->add('GET', '/SuperMarketArthur/api/search', ['App\\Controllers\\Shop\\SearchController', 'search']);

return $router;
