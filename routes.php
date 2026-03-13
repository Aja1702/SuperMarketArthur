<?php

require_once __DIR__ . '/src/Core/Router.php';

// ============================================
// CARGAR CONTROLLERS
// ============================================

// Controllers principales
require_once __DIR__ . '/src/Controllers/HomeController.php';
require_once __DIR__ . '/src/Controllers/OfertasController.php';
require_once __DIR__ . '/src/Controllers/SobreNosotrosController.php';
require_once __DIR__ . '/src/Controllers/SoporteController.php';
require_once __DIR__ . '/src/Controllers/ContactoController.php';
require_once __DIR__ . '/src/Controllers/LegalController.php';
require_once __DIR__ . '/src/Controllers/OrderConfirmationController.php';
require_once __DIR__ . '/src/Controllers/AccountController.php';

// Controllers de Shop
require_once __DIR__ . '/src/Controllers/Shop/ProductoController.php';
require_once __DIR__ . '/src/Controllers/Shop/CartController.php';
require_once __DIR__ . '/src/Controllers/Shop/CheckoutController.php';
require_once __DIR__ . '/src/Controllers/Shop/FavoriteController.php';
require_once __DIR__ . '/src/Controllers/Shop/SearchController.php';
require_once __DIR__ . '/src/Controllers/Shop/RatingController.php';

// Controllers de Auth
require_once __DIR__ . '/src/Controllers/Auth/AuthController.php';

// Controllers de Admin
require_once __DIR__ . '/src/Controllers/Admin/AdminController.php';
require_once __DIR__ . '/src/Controllers/Admin/AdminProductosController.php';
require_once __DIR__ . '/src/Controllers/Admin/AdminUsuariosController.php';
require_once __DIR__ . '/src/Controllers/Admin/AdminCategoriasController.php';
require_once __DIR__ . '/src/Controllers/Admin/AdminConfigController.php';
require_once __DIR__ . '/src/Controllers/Admin/AdminPedidosController.php';

$router = new \App\Core\Router();

// ============================================
// RUTAS PÚBLICAS - PÁGINAS PRINCIPALES
// ============================================

// Home y páginas principales
$router->get('/', ['App\Controllers\HomeController', 'index']);
$router->get('/ofertas', ['App\Controllers\OfertasController', 'index']);

// Productos - IMPORTANTE: orden específico para evitar conflictos
// Las rutas más específicas primero:
// 1. producto/{id} - detalle de producto (solo dígitos)
// 2. productos/{id} - ver producto por ID en formato plural (solo dígitos)  
// 3. productos - catálogo general
// 4. productos/{categoria} - catálogo por categoría (texto)
$router->get('/producto/{id}', ['App\Controllers\Shop\ProductoController', 'show']);
$router->get('/productos/{id}', ['App\Controllers\Shop\ProductoController', 'show']);
$router->get('/productos', ['App\Controllers\Shop\ProductoController', 'index']);
$router->get('/productos/{categoria}', ['App\Controllers\Shop\ProductoController', 'index']);

// Páginas informativas
$router->get('/sobre-nosotros', ['App\Controllers\SobreNosotrosController', 'index']);
$router->get('/soporte', ['App\Controllers\SoporteController', 'index']);

// Contacto
$router->get('/contacto', ['App\Controllers\ContactoController', 'index']);
$router->post('/contacto', ['App\Controllers\ContactoController', 'send']);

// ============================================
// AUTENTICACIÓN
// ============================================

$router->get('/login', ['App\Controllers\Auth\AuthController', 'showLoginForm']);
$router->post('/login', ['App\Controllers\Auth\AuthController', 'processLogin']);
$router->get('/registro', ['App\Controllers\Auth\AuthController', 'showRegisterForm']);
$router->post('/registro', ['App\Controllers\Auth\AuthController', 'processRegister']);
$router->post('/logout', ['App\Controllers\Auth\AuthController', 'logout']);

// ============================================
// ÁREA DE USUARIO
// ============================================

$router->get('/favoritos', ['App\Controllers\Shop\FavoriteController', 'index']);
$router->get('/mi-cuenta', ['App\Controllers\AccountController', 'index']);

// ============================================
// CHECKOUT Y PEDIDOS
// ============================================

$router->get('/checkout', ['App\Controllers\Shop\CheckoutController', 'index']);
$router->post('/checkout', ['App\Controllers\Shop\CheckoutController', 'processOrder']);
$router->post('/checkout/pay', ['App\Controllers\Shop\CheckoutController', 'createStripeSession']);
$router->get('/checkout/success', ['App\Controllers\Shop\CheckoutController', 'confirmStripePayment']);
$router->get('/order-confirmation', ['App\Controllers\Shop\OrderConfirmationController', 'index']);

// ============================================
// PÁGINAS LEGALES
// ============================================

$router->get('/privacidad', ['App\Controllers\LegalController', 'privacidad']);
$router->get('/terminos', ['App\Controllers\LegalController', 'terminos']);
$router->get('/cookies', ['App\Controllers\LegalController', 'cookies']);

// ============================================
// PANEL DE ADMINISTRACIÓN
// ============================================

// Dashboard principal
$router->get('/admin', ['App\Controllers\Admin\AdminController', 'index']);

// Productos
$router->get('/admin/productos', ['App\Controllers\Admin\AdminProductosController', 'index']);
$router->get('/admin/productos/nuevo', ['App\Controllers\Admin\AdminProductosController', 'showNewProductForm']);
$router->post('/admin/productos/crear', ['App\Controllers\Admin\AdminProductosController', 'createProduct']);
$router->get('/admin/productos/editar/{id}', ['App\Controllers\Admin\AdminProductosController', 'showEditForm']);
$router->post('/admin/productos/actualizar', ['App\Controllers\Admin\AdminProductosController', 'updateProduct']);
$router->get('/admin/productos/eliminar/{id}', ['App\Controllers\Admin\AdminProductosController', 'showDeleteConfirmation']);
$router->post('/admin/productos/eliminar', ['App\Controllers\Admin\AdminProductosController', 'deleteProduct']);

// Usuarios
$router->get('/admin/usuarios', ['App\Controllers\Admin\AdminUsuariosController', 'index']);

// Categorías
$router->get('/admin/categorias', ['App\Controllers\Admin\AdminCategoriasController', 'index']);

// Pedidos
$router->get('/admin/pedidos', ['App\Controllers\Admin\AdminPedidosController', 'index']);

// Configuración
$router->get('/admin/config', ['App\Controllers\Admin\AdminConfigController', 'index']);
$router->post('/admin/config/save', ['App\Controllers\Admin\AdminConfigController', 'save']);

// ============================================
// API - CARRITO
// ============================================

$router->get('/api/cart/items', ['App\Controllers\Shop\CartController', 'getItems']);
$router->post('/api/cart/add', ['App\Controllers\Shop\CartController', 'addItem']);
$router->post('/api/cart/update', ['App\Controllers\Shop\CartController', 'updateQuantity']);

// ============================================
// API - FAVORITOS
// ============================================

$router->post('/api/favorite/toggle', ['App\Controllers\Shop\FavoriteController', 'toggle']);

// ============================================
// API - VALORACIONES
// ============================================

$router->post('/rating/submit', ['App\Controllers\Shop\RatingController', 'submitRating']);

// ============================================
// API - BÚSQUEDA
// ============================================

$router->get('/api/search', ['App\Controllers\Shop\SearchController', 'search']);

return $router;
