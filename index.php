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

// Incluir cabecera, menú, pie protegidos y centrados en arrays
include($rutas[$tipo_usuario]['cabecera']);
include($rutas[$tipo_usuario]['menu']);

// Lógica de vistas
$vistaValidaMenuInvitado = ['categorias_productos', 'ofertas', 'sobre_nosotros', 'soporte', 'contacto', 'privacidad', 'terminos', 'cookies'];
$vistaValidaMenuAdmin = ['administrador', 'admin_productos', 'admin_pedidos', 'admin_usuarios', 'admin_stock', 'admin_config'];
$vistaValidaMenuUsuario = ['usuario', 'mis_pedidos', 'detalle_pedido', 'favoritos', 'config_usuario'];

$vista = '';
if (isset($_GET['vistaMenu'])) {
    $vistaSolicitada = $_GET['vistaMenu'];
    if (
        ($tipo_usuario === 'administrador' && in_array($vistaSolicitada, $vistaValidaMenuAdmin)) ||
        ($tipo_usuario === 'usuario' && in_array($vistaSolicitada, $vistaValidaMenuUsuario)) ||
        (in_array($vistaSolicitada, $vistaValidaMenuInvitado))
    ) {
        $vista = $vistaSolicitada;
    }
}

if (!empty($vista)) {
    include("./includes/centro/centro_{$vista}.php");
} else {
    include($rutas[$tipo_usuario]['centro']);
}

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

    <script type="module" src="./assets/js/funciones.js"></script>
    <script src="./assets/js/checkout_modal.js"></script>
</body>

</html>