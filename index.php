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
session_start();
include('./config/iniciar_session.php');

// Validar el tipo de usuario con control de errores
$tipo_usu = $_SESSION['tipo_usu'] ?? 'invitado';

$tipos_validos = ['a', 'u', 'i'];
if (!in_array($tipo_usu, $tipos_validos)) {
    $tipo_usu = 'invitado'; // Valor por defecto si hay dato inválido
}

$tipo_usuario = $tipo_usu === 'a' ? 'administrador' : ($tipo_usu === 'u' ? 'usuario' : 'invitado');

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

// Mostrar contenido basado en parámetros GET con validación segura
$vistaValidaUser = ['login', 'registro', 'perfil', 'recuperar'];
$vistaValidaMenuInvitado = ['categorias_productos', 'ofertas', 'sobre_nosotros', 'soporte', 'contacto'];
$vistaValidaMenuAdmin = ['administrador', 'admin_productos', 'admin_pedidos', 'admin_usuarios', 'admin_stock', 'admin_config'];
$vistaValidaMenuUsuario = ['usuario', 'mis_pedidos', 'favoritos', 'config_usuario'];

// Validar y mostrar vistas de administrador
if ($tipo_usuario === 'administrador' && isset($_GET['vistaMenu']) && in_array($_GET['vistaMenu'], $vistaValidaMenuAdmin)) {
    $vista = $_GET['vistaMenu'];
    include("./includes/centro/centro_{$vista}.php");
}
else


    // Incluir formularios o vistas específicas basadas en parámetros GET con validación de usuarios
    if (isset($_GET['userSession']) && in_array($_GET['userSession'], $vistaValidaUser)) {
        $vista = $_GET['userSession'];
        include("./includes/centro/form_{$vista}.php");
    }
    elseif ($tipo_usuario === 'usuario' && isset($_GET['vistaMenu']) && in_array($_GET['vistaMenu'], $vistaValidaMenuUsuario)) {
        $vista = $_GET['vistaMenu'];
        include("./includes/centro/centro_{$vista}.php");
    }
    elseif (isset($_GET['vistaMenu']) && in_array($_GET['vistaMenu'], $vistaValidaMenuInvitado)) {
        $vista = $_GET['vistaMenu'];
        include("./includes/centro/centro_{$vista}.php");
    }
    else {
        // Incluye la vista por defecto segun el rol
        include($rutas[$tipo_usuario]['centro']);
    }

include($rutas[$tipo_usuario]['pie']);
?>
    <!-- Estructura del Carrito Lateral -->
    <div class="cart-overlay" id="cartOverlay"></div>
    <div class="cart-panel" id="cartPanel" role="dialog" aria-modal="true" aria-labelledby="cartTitle">
        <div class="cart-header">
            <h3 id="cartTitle">Tu Carrito</h3>
            <button class="close-cart" id="closeCart" aria-label="Cerrar carrito">&times;</button>
        </div>
        <div class="cart-content" id="cartContent">
            <!-- Los productos se cargarán aquí dinámicamente -->
            <div class="empty-cart-message" style="text-align: center; padding: 2rem; opacity: 0.6;">
                <p>Tu carrito está vacío</p>
            </div>
        </div>
        <div class="cart-footer">
            <div class="cart-total">
                <span>Total:</span>
                <span id="cartTotalAmount">0,00€</span>
            </div>
            <a href="./?vistaMenu=carrito" class="btn-checkout">Finalizar Compra</a>
        </div>
    </div>

    <script type="module" src="./assets/js/funciones.js"></script>
</body>

</html>