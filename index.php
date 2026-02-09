<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('./config/iniciar_session.php');

// Validar el tipo de usuario con control de errores
$tipo_usu = $_SESSION['tipo_usu'] ?? 'invitado';

$tipos_validos = ['a', 'u', 'i'];
if (!in_array($tipo_usu, $tipos_validos)) {
    $tipo_usu = 'invitado'; // Valor por defecto si hay dato inválido
}

$tipo_usuario = $tipo_usu === 'a' ? 'administrador' : ($tipo_usu === 'u' ? 'usuario' : 'invitado');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Supermarket Arthur - Encuentra los mejores productos al mejor precio." />
    <meta name="keywords" content="supermercado, compras, productos, ofertas" />
    <title>Supermarket Arthur</title>
    <link rel="icon" href="./assets/img/logo/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="./assets/css/styles.css" />
    <link rel="stylesheet" href="./assets/css/header.css" />
    <link rel="stylesheet" href="./assets/css/nav.css" />
    <link rel="stylesheet" href="./assets/css/main.css" />
    <link rel="stylesheet" href="./assets/css/footer.css" />
    <link rel="stylesheet" href="./assets/css/modal.css" />
    <link rel="stylesheet" href="./assets/css/product-detail.css" />
    <link rel="stylesheet" href="./assets/css/forms.css" />
    <link rel="stylesheet" href="./assets/css/admin.css" />
    <link rel="stylesheet" href="./assets/css/catalog.css" />
</head>

<body>
    <?php
//Rutas a incluir según el tipo de usuario
$rutas = [
    'administrador' => [
        'cabecera' => './includes/cabecera/cabecera_administrador.php',
        'menu' => './includes/menu/menu_administrador.php',
        'centro' => './includes/centro/centro_administrador.php',
        'pie' => './includes/pie/pie_administrador.php'
    ],
    'usuario' => [
        'cabecera' => './includes/cabecera/cabecera_logueado.php',
        'menu' => './includes/menu/menu_logueado.php',
        'centro' => './includes/centro/centro_logueado.php',
        'pie' => './includes/pie/pie_logueado.php'
    ],
    'invitado' => [
        'cabecera' => './includes/cabecera/cabecera_invitado.php',
        'menu' => './includes/menu/menu_invitado.php',
        'centro' => './includes/centro/centro_invitado.php',
        'pie' => './includes/pie/pie_invitado.php'
    ]
];

// Incluir cabecera y menú
include($rutas[$tipo_usuario]['cabecera']);
include($rutas[$tipo_usuario]['menu']);

// --- LÓGICA DE VISTAS MEJORADA ---
$vistaValidaUser = ['login', 'registro', 'perfil', 'recuperar'];
$vistaValidaMenuInvitado = ['categorias_productos', 'ofertas', 'sobre_nosotros', 'soporte', 'contacto', 'privacidad', 'terminos', 'cookies'];
$vistaValidaMenuAdmin = ['administrador', 'admin_productos', 'admin_pedidos', 'admin_usuarios', 'admin_stock', 'admin_config'];
$vistaValidaMenuUsuario = ['usuario', 'mis_pedidos', 'detalle_pedido', 'favoritos', 'config_usuario'];

// Prioridad 1: Detalle de producto (si se pasa un id_producto)
if (isset($_GET['id_producto'])) {
    include("./includes/centro/centro_detalle_producto.php");
}
// Prioridad 2: Formularios de sesión de usuario (ej: ?userSession=login)
else if (isset($_GET['userSession']) && in_array($_GET['userSession'], $vistaValidaUser)) {
    $vista = $_GET['userSession'];
    include("./includes/centro/form_{$vista}.php");
}
// Prioridad 3: Vistas del menú principal (ej: ?vistaMenu=ofertas)
else if (isset($_GET['vistaMenu'])) {
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
        include("./includes/centro/centro_{$vistaSolicitada}.php");
    } else {
        // Si la vista solicitada no es válida para el rol, se carga la vista por defecto
        include($rutas[$tipo_usuario]['centro']);
    }
}
// Prioridad 4: Si no se solicita ninguna vista, cargar la por defecto
else {
    include($rutas[$tipo_usuario]['centro']);
}

// Incluir pie de página
include($rutas[$tipo_usuario]['pie']);

// Incluir el modal de login solo si el usuario es un invitado
if ($tipo_usuario === 'invitado') {
    include('./includes/modals/login_modal.php');
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

    <script src="./assets/js/cart.js"></script>
    <script src="./assets/js/search.js"></script>
    <script type="module" src="./assets/js/auth.js"></script>
    <script src="./assets/js/checkout_modal.js"></script>
    <script src="./assets/js/rating.js"></script>
</body>

</html>
