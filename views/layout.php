<?php
// MODO DEPURACIÓN FORENSE: Forzamos a PHP a mostrar todos los errores.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Generar token CSRF global si no existe
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>" />
    <title><?php echo htmlspecialchars($nombre_sitio); ?></title>
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/img/logo/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/bundle.min.css?v=<?php echo $cache_version; ?>" />
</head>
<body class="role-<?php echo htmlspecialchars($tipo_usuario); ?>">
    <?php
    global $rutas, $tipo_usuario;

    // Cargar las partes de la plantilla según el tipo de usuario
    include($rutas[$tipo_usuario]['cabecera']);
    include($rutas[$tipo_usuario]['menu']);
    ?>

    <main role="main">
        <?php echo $content; // Aquí se inyecta el contenido de la vista específica ?>
    </main>

    <?php
    include($rutas[$tipo_usuario]['pie']);

    // Cargar modales y otros elementos del final del body, solo para la tienda
    if ($tipo_usuario !== 'a') {
        // Modal del carrito, siempre presente en la tienda
        echo '<div class="cart-overlay" id="cartOverlay"></div>';
        echo '<div class="cart-panel" id="cartPanel" role="dialog" aria-modal="true" aria-labelledby="cartTitle">';
        echo '    <div class="cart-header">';
        echo '        <h3 id="cartTitle">Tu Cesta</h3>';
        echo '        <button class="close-cart" id="closeCart" aria-label="Cerrar carrito">&times;</button>';
        echo '    </div>';
        echo '    <div class="cart-content" id="cartContent">';
        echo '        <div class="empty-cart-message" style="text-align: center; padding: 2rem; opacity: 0.6;"><p>Tu cesta está vacía</p></div>';
        echo '    </div>';
        echo '    <div class="cart-footer">';
        echo '        <div class="cart-total"><span>Total:</span> <span id="cartTotalAmount">0,00' . htmlspecialchars($simbolo_moneda) . '</span></div>';
        if (isset($_SESSION['id_usuario'])) {
            echo '        <a href="' . BASE_URL . 'checkout" class="btn-checkout">Finalizar Compra</a>';
        } else {
            echo '        <button id="checkoutBtn" class="btn-checkout">Finalizar Compra</button>';
        }
        echo '    </div>';
        echo '</div>';

        // Modal de login, solo para invitados
        if ($tipo_usuario === 'invitado') {
            include(__DIR__ . '/../includes/modals/login_modal.php');
        }
    }
    ?>

    <script>
        const BASE_URL = "<?php echo BASE_URL; ?>";
    </script>
    <script src="<?php echo BASE_URL; ?>dist/js/app.min.js?v=<?php echo $cache_version; ?>" type="module"></script>
</body>
</html>
