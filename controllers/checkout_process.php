<?php
session_start();
require_once '../config/iniciar_session.php';
require_once '../models/Cart.php';
require_once '../models/Order.php';
require_once '../models/User.php'; // Incluir el modelo de usuario

// 1. Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['return_to'] = 'checkout.php';
    header('Location: ../login.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// 2. Instanciar modelos
$cart = new Cart($pdo, $id_usuario);
$user = new User($pdo);
$order = new Order($pdo);

// 3. Obtener items del carrito
$cartItems = $cart->getItems();

if (empty($cartItems)) {
    header('Location: ../index.php');
    exit;
}

// 4. Procesar la creación del pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 5. OBTENER UNA DIRECCIÓN VÁLIDA DEL USUARIO
    $userAddresses = $user->getUserAddresses($id_usuario);

    if (empty($userAddresses)) {
        // Si no hay direcciones, no se puede continuar
        $_SESSION['error_message'] = 'No tienes una dirección de envío guardada. Por favor, añade una desde tu perfil antes de continuar.';
        header('Location: ../checkout.php');
        exit;
    }

    // Usar la primera dirección disponible para el pedido
    $addressId = $userAddresses[0]['id_direccion'];

    // 6. Intentar crear el pedido
    $orderId = $order->createOrder($id_usuario, $cartItems, $addressId);

    if ($orderId) {
        // 7. ÉXITO: Actualizar estado, limpiar carrito y redirigir
        $order->updateOrderStatus($orderId, 'pagado');
        $cart->clearCart();
        header('Location: ../order_confirmation.php?order_id=' . $orderId);
        exit;
    } else {
        // 8. ERROR: Guardar el mensaje de error específico y redirigir
        $errorMessage = $order->lastError ? $order->lastError : 'Ocurrió un error desconocido.';
        $_SESSION['error_message'] = "No se pudo crear el pedido: " . $errorMessage;
        header('Location: ../checkout.php');
        exit;
    }
} else {
    header('Location: ../checkout.php');
    exit;
}
?>