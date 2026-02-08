<?php
session_start();
require_once '../config/iniciar_session.php';
require_once '../models/Cart.php';
require_once '../models/Order.php';

// 1. Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    // Guardar la intención de ir al checkout y redirigir al login
    $_SESSION['return_to'] = 'checkout.php';
    header('Location: ../login.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// 2. Instanciar modelos
$cart = new Cart($pdo, $id_usuario);
$order = new Order($pdo);

// 3. Obtener items del carrito
$cartItems = $cart->getItems();

if (empty($cartItems)) {
    // Redirigir si el carrito está vacío
    header('Location: ../index.php');
    exit;
}

// 4. Procesar la creación del pedido (ej. desde un POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // En una implementación real, aquí se obtendría el ID de la dirección seleccionada por el usuario.
    // Para este ejemplo, asumiremos un ID de dirección fijo o el primero que se encuentre.
    // Esto debería ser reemplazado por una lógica para que el usuario elija su dirección.
    $addressId = $_POST['id_direccion'] ?? 1; // Asumimos 1 como fallback

    try {
        // 5. Crear el pedido
        $orderId = $order->createOrder($id_usuario, $cartItems, $addressId);

        if ($orderId) {
            // 6. Limpiar el carrito
            $cart->clearCart();

            // 7. Redirigir a la página de confirmación
            header('Location: ../order_confirmation.php?order_id=' . $orderId);
            exit;
        } else {
            // Manejar error en la creación del pedido
            $_SESSION['error_message'] = 'No se pudo crear el pedido.';
            header('Location: ../checkout.php');
            exit;
        }
    } catch (Exception $e) {
        // Manejar excepciones (ej. error de base de datos)
        $_SESSION['error_message'] = 'Error al procesar el pedido: ' . $e->getMessage();
        header('Location: ../checkout.php');
        exit;
    }
} else {
    // Si no es POST, redirigir al checkout para que el usuario vea su pedido
    header('Location: ../checkout.php');
    exit;
}
?>