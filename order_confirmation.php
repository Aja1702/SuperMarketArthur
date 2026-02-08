<?php
session_start();
require_once 'config/iniciar_session.php';
require_once 'models/Order.php';

// 1. Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

// 2. Obtener el ID del pedido desde la URL
$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    // Si no hay ID de pedido, redirigir al inicio
    header('Location: index.php');
    exit;
}

// 3. Obtener los detalles del pedido para mostrarlos
$order_model = new Order($pdo);
$order = $order_model->getOrderById($order_id, $_SESSION['id_usuario']);

// Verificar que el pedido pertenece al usuario actual
if (!$order) {
    echo "<p>Pedido no encontrado o no tienes permiso para verlo.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedido Confirmado</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>¡Gracias por tu compra!</h1>
        <p>Tu pedido ha sido procesado con éxito.</p>

        <h2>Resumen del Pedido #<?php echo htmlspecialchars($order['id_pedido']); ?></h2>
        <p><strong>Fecha:</strong> <?php echo date("d/m/Y", strtotime($order['fecha'])); ?></p>
        <p><strong>Total:</strong> <?php echo number_format($order['total'], 2, ',', '.'); ?>€</p>

        <h3>Artículos del pedido:</h3>
        <ul>
            <?php foreach ($order['items'] as $item): ?>
                <li>
                    <?php echo htmlspecialchars($item['nombre_producto']); ?> -
                    Cantidad: <?php echo htmlspecialchars($item['cantidad']); ?> -
                    Precio: <?php echo number_format($item['precio_unitario'], 2, ',', '.'); ?>€
                </li>
            <?php endforeach; ?>
        </ul>

        <a href="index.php" class="btn">Seguir comprando</a>
    </div>
</body>
</html>
