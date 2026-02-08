<?php
session_start();
require_once 'config/iniciar_session.php';
require_once 'models/Cart.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;
$cart = new Cart($pdo, $id_usuario);

$items = $cart->getItems();
$total = $cart->getTotal();

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
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/nav.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/modal.css">
</head>
<body>
    <div class="checkout-container">
        <h1>Finalizar Compra</h1>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message">
                <?php
                echo htmlspecialchars($_SESSION['error_message']);
                unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($items)): ?>
            <form action="controllers/checkout_process.php" method="POST" class="checkout-form">

                <div class="checkout-section">
                    <h2>Resumen de tu pedido</h2>
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['nombre_producto']); ?></td>
                                    <td><?php echo htmlspecialchars($item['cantidad']); ?></td>
                                    <td><?php echo number_format($item['precio'] * $item['cantidad'], 2, ',', '.'); ?>€</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" style="text-align:right;"><strong>Total:</strong></td>
                                <td><strong><?php echo number_format($total, 2, ',', '.'); ?>€</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="checkout-section">
                    <h2>Información de Pago (Simulación)</h2>
                    <div class="form-grid-payment">
                        <div class="form-group-payment full-width">
                            <label for="card_name">Nombre en la tarjeta</label>
                            <input type="text" id="card_name" value="Arthur Morgan" required>
                        </div>
                        <div class="form-group-payment full-width">
                            <label for="card_number">Número de tarjeta</label>
                            <input type="text" id="card_number" value="1234 5678 9101 1121" required>
                        </div>
                        <div class="form-group-payment">
                            <label for="card_expiry">Fecha de caducidad</label>
                            <input type="text" id="card_expiry" placeholder="MM/AA" value="12/25" required>
                        </div>
                        <div class="form-group-payment">
                            <label for="card_cvc">CVC</label>
                            <input type="text" id="card_cvc" value="123" required>
                        </div>
                    </div>
                </div>

                <!-- ID de dirección (debería ser dinámico en una app real) -->
                <input type="hidden" name="id_direccion" value="1">

                <div class="checkout-actions">
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
