<?php
session_start();
require_once 'config/iniciar_session.php';
require_once 'models/Cart.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;
$cart = new Cart($pdo, $id_usuario);

$items = $cart->getItems();
$total = $cart->getTotal();

// Si el carrito está vacío y no hay un mensaje de error, redirigir a la tienda
if (empty($items) && !isset($_SESSION['error_message'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Compra - SuperMarket Arthur</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Finalizar Compra</h1>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message" style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                <?php
                echo htmlspecialchars($_SESSION['error_message']);
                unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($items)): ?>
            <h2>Resumen de tu pedido</h2>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['nombre_producto']); ?></td>
                            <td><?php echo htmlspecialchars($item['cantidad']); ?></td>
                            <td><?php echo number_format($item['precio'], 2, ',', '.'); ?>€</td>
                            <td><?php echo number_format($item['precio'] * $item['cantidad'], 2, ',', '.'); ?>€</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align:right;"><strong>Total:</strong></td>
                        <td><strong><?php echo number_format($total, 2, ',', '.'); ?>€</strong></td>
                    </tr>
                </tfoot>
            </table>

            <form action="controllers/checkout_process.php" method="POST" style="margin-top: 2rem;">
                <input type="hidden" name="id_direccion" value="1"> <!-- ID de dirección de ejemplo -->

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <a href="index.php" class="btn btn-secondary">Seguir Comprando</a>
                    <button type="submit" class="btn btn-primary">Confirmar y Pagar</button>
                </div>
            </form>
        <?php else: ?>
            <p>Tu carrito está vacío.</p>
            <a href="index.php" class="btn">Volver a la tienda</a>
        <?php endif; ?>
    </div>
</body>
</html>
