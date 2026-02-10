<?php
// Incluir el manejador de errores ANTES de cualquier otra cosa
require_once __DIR__ . '/config/error_handler.php';

// --- CONFIGURACIÓN DE RUTA BASE Y CACHÉ ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
define('BASE_URL', $protocol . $host . $path . '/');
$cache_version = time();

// Iniciar la sesión y la conexión a la base de datos
session_start();
require_once __DIR__ . '/config/iniciar_session.php';

// --- LÓGICA DE USUARIO Y DATOS ---
$tipo_usu = $_SESSION['tipo_usu'] ?? 'invitado';
$tipos_validos = ['a', 'u', 'i'];
if (!in_array($tipo_usu, $tipos_validos)) {
    $tipo_usu = 'invitado';
}
$tipo_usuario = $tipo_usu === 'a' ? 'administrador' : ($tipo_usu === 'u' ? 'usuario' : 'invitado');

// Carga de datos específicos para el panel de administración
if ($tipo_usuario === 'administrador') {
    require_once __DIR__ . '/models/User.php';
    $userModel = new User($pdo);
    $admin_stats = $userModel->getAdminDashboardStats();

    // Cargar datos para la página de PRODUCTOS del admin
    if (isset($_GET['vistaMenu']) && $_GET['vistaMenu'] === 'admin_productos') {
        require_once __DIR__ . '/models/Product.php';
        $productModel = new Product($pdo);

        $pagina_actual = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $productos_por_pagina = 20;
        $offset = ($pagina_actual - 1) * $productos_por_pagina;
        $productos = $productModel->getAllProducts($productos_por_pagina, $offset);
        $total_productos = $admin_stats['total_products'];
        $total_paginas = ceil($total_productos / $productos_por_pagina);
        $categorias = $productModel->getCategories();

    // Cargar datos para la página de PEDIDOS del admin
    } elseif (isset($_GET['vistaMenu']) && $_GET['vistaMenu'] === 'admin_pedidos') {
        require_once __DIR__ . '/models/Order.php';
        $orderModel = new Order($pdo);

        $pagina_actual = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $pedidos_por_pagina = 15;
        $offset = ($pagina_actual - 1) * $pedidos_por_pagina;
        $pedidos = $orderModel->getAllOrders($pedidos_por_pagina, $offset);
        $total_pedidos = $pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn(); // Obtenemos el total para la paginación
        $total_paginas = ceil($total_pedidos / $pedidos_por_pagina);

    // Cargar datos para la página de USUARIOS del admin
    } elseif (isset($_GET['vistaMenu']) && $_GET['vistaMenu'] === 'admin_usuarios') {
        $pagina_actual = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $usuarios_por_pagina = 20;
        $offset = ($pagina_actual - 1) * $usuarios_por_pagina;
        $usuarios = $userModel->getAllUsers($usuarios_por_pagina, $offset);
        $total_usuarios = $admin_stats['total_users'];
        $total_paginas = ceil($total_usuarios / $usuarios_por_pagina);

    // Cargar datos para la página de STOCK BAJO del admin
    } elseif (isset($_GET['vistaMenu']) && $_GET['vistaMenu'] === 'admin_stock') {
        require_once __DIR__ . '/models/Product.php';
        $productModel = new Product($pdo);

        $pagina_actual = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $productos_por_pagina = 20;
        $offset = ($pagina_actual - 1) * $productos_por_pagina;
        $productos_stock_bajo = $productModel->getLowStockProducts($productos_por_pagina, $offset);
        $total_productos_stock_bajo = $admin_stats['low_stock_products'];
        $total_paginas = ceil($total_productos_stock_bajo / $productos_por_pagina);
    }
}

// --- DEFINICIÓN DE RUTAS DE VISTAS ---
$rutas = [
    'administrador' => [
        'cabecera' => __DIR__ . '/includes/cabecera/cabecera_administrador.php',
        'menu'     => __DIR__ . '/includes/menu/menu_administrador.php',
        'centro'   => __DIR__ . '/includes/centro/centro_administrador.php',
        'pie'      => __DIR__ . '/includes/pie/pie_administrador.php'
    ],
    'usuario' => [
        'cabecera' => __DIR__ . '/includes/cabecera/cabecera_logueado.php',
        'menu'     => __DIR__ . '/includes/menu/menu_logueado.php',
        'centro'   => __DIR__ . '/includes/centro/centro_logueado.php',
        'pie'      => __DIR__ . '/includes/pie/pie_logueado.php'
    ],
    'invitado' => [
        'cabecera' => __DIR__ . '/includes/cabecera/cabecera_invitado.php',
        'menu'     => __DIR__ . '/includes/menu/menu_invitado.php',
        'centro'   => __DIR__ . '/includes/centro/centro_invitado.php',
        'pie'      => __DIR__ . '/includes/pie/pie_invitado.php'
    ]
];

// --- RENDERIZADO DE LA PÁGINA ---
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Supermarket Arthur</title>
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/img/logo/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/bundle.min.css?v=<?php echo $cache_version; ?>" />
</head>
<body>
    <?php
    // Cargar Cabecera y Menú
    include($rutas[$tipo_usuario]['cabecera']);
    include($rutas[$tipo_usuario]['menu']);

    // --- Lógica de Enrutamiento para el Contenido Central ---
    $vistaValidaUser = ['login', 'registro', 'perfil', 'recuperar'];
    $vistaValidaMenuInvitado = ['categorias_productos', 'ofertas', 'sobre_nosotros', 'soporte', 'contacto', 'privacidad', 'terminos', 'cookies'];
    $vistaValidaMenuAdmin = ['administrador', 'admin_productos', 'admin_pedidos', 'admin_usuarios', 'admin_stock', 'admin_config'];
    $vistaValidaMenuUsuario = ['usuario', 'mis_pedidos', 'detalle_pedido', 'favoritos', 'config_usuario'];

    if (isset($_GET['id_producto'])) {
        include(__DIR__ . "/includes/centro/centro_detalle_producto.php");
    } else if (isset($_GET['userSession']) && in_array($_GET['userSession'], $vistaValidaUser)) {
        $vista = $_GET['userSession'];
        include(__DIR__ . "/includes/centro/form_{$vista}.php");
    } else if (isset($_GET['vistaMenu'])) {
        $vistaSolicitada = $_GET['vistaMenu'];
        $vistaValida = false;

        if ($tipo_usuario === 'administrador' && in_array($vistaSolicitada, $vistaValidaMenuAdmin)) {
            $vistaValida = true;
        } elseif ($tipo_usuario === 'usuario' && in_array($vistaSolicitada, $vistaValidaMenuUsuario)) {
            $vistaValida = true;
        } elseif (in_array($vistaSolicitada, $vistaValidaMenuInvitado)) {
            $vistaValida = true;
        }

        if ($vistaValida) {
            include(__DIR__ . "/includes/centro/centro_{$vistaSolicitada}.php");
        } else {
            include($rutas[$tipo_usuario]['centro']);
        }
    } else {
        include($rutas[$tipo_usuario]['centro']);
    }

    // Cargar Pie de Página
    include($rutas[$tipo_usuario]['pie']);

    // Cargar Modales y otros elementos del final del body
    if ($tipo_usuario === 'invitado') {
        include(__DIR__ . '/includes/modals/login_modal.php');
    }
    ?>

    <!-- Estructura del Carrito Lateral -->
    <div class="cart-overlay" id="cartOverlay"></div>
    <div class="cart-panel" id="cartPanel" role="dialog" aria-modal="true" aria-labelledby="cartTitle">
        <div class="cart-header">
            <h3 id="cartTitle">Tu Carrito</h3>
            <button class="close-cart" id="closeCart" aria-label="Cerrar carrito">&times;</button>
        </div>
        <div class="cart-content" id="cartContent">
            <div class="empty-cart-message" style="text-align: center; padding: 2rem; opacity: 0.6;">
                <p>Tu carrito está vacío</p>
            </div>
        </div>
        <div class="cart-footer">
            <div class="cart-total">
                <span>Total:</span>
                <span id="cartTotalAmount">0,00€</span>
            </div>
            <?php if ($tipo_usuario === 'invitado'): ?>
                <button id="checkoutBtn" class="btn-checkout">Finalizar Compra</button>
            <?php else: ?>
                <a href="checkout.php" class="btn-checkout">Finalizar Compra</a>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?php echo BASE_URL; ?>dist/js/app.min.js?v=<?php echo $cache_version; ?>" type="module"></script>
</body>
</html>
