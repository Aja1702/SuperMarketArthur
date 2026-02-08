<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: /SuperMarketArthur/?userSession=login");
    exit();
}

include '../config/iniciar_session.php';
include '../models/Cart.php';
include '../models/Order.php';
include '../models/User.php';

$userId = $_SESSION['id_usuario'];
$cartModel = new Cart($pdo);
$orderModel = new Order($pdo);
$userModel = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /SuperMarketArthur/?vistaMenu=checkout");
    exit();
}

// Validar CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Error CSRF: Token inválido');
}

$paymentMethod = $_POST['payment_method'] ?? 'tarjeta';

// Obtener carrito del usuario
$cartItems = $cartModel->getCartItems($userId);
$user = $userModel->getUserById($userId);

if (empty($cartItems)) {
    header("Location: /SuperMarketArthur/?vistaMenu=categorias_productos");
    exit();
}

// Validar datos de pago según método
$paymentErrors = [];

if ($paymentMethod === 'tarjeta') {
    $cardNumber = trim($_POST['card_number'] ?? '');
    $expiryDate = trim($_POST['expiry_date'] ?? '');
    $cvv = trim($_POST['cvv'] ?? '');
    $cardName = trim($_POST['card_name'] ?? '');

    // Validaciones básicas de tarjeta
    if (empty($cardNumber) || !preg_match('/^\d{4}\s\d{4}\s\d{4}\s\d{4}$/', $cardNumber)) {
        $paymentErrors[] = "Número de tarjeta inválido";
    }

    if (empty($expiryDate) || !preg_match('/^\d{2}\/\d{2}$/', $expiryDate)) {
        $paymentErrors[] = "Fecha de expiración inválida";
    }

    if (empty($cvv) || !preg_match('/^\d{3,4}$/', $cvv)) {
        $paymentErrors[] = "CVV inválido";
    }

    if (empty($cardName)) {
        $paymentErrors[] = "Nombre en la tarjeta requerido";
    }

    // Validar fecha de expiración (mes/año)
    if (!empty($expiryDate) && preg_match('/^(\d{2})\/(\d{2})$/', $expiryDate, $matches)) {
        $month = (int)$matches[1];
        $year = (int)$matches[2] + 2000;
        $currentYear = (int)date('Y');
        $currentMonth = (int)date('m');

        if ($month < 1 || $month > 12) {
            $paymentErrors[] = "Mes de expiración inválido";
        }

        if ($year < $currentYear || ($year == $currentYear && $month < $currentMonth)) {
            $paymentErrors[] = "Tarjeta expirada";
        }
    }
}

if (!empty($paymentErrors)) {
    $_SESSION['payment_errors'] = $paymentErrors;
    header("Location: /SuperMarketArthur/?vistaMenu=checkout");
    exit();
}

// Simular procesamiento de pago (en producción usarías Stripe/PayPal API)
$paymentSuccess = simulatePayment($paymentMethod, $cartItems);

if (!$paymentSuccess) {
    $_SESSION['payment_errors'] = ["Error en el procesamiento del pago. Inténtalo de nuevo."];
    header("Location: /SuperMarketArthur/?vistaMenu=checkout");
    exit();
}

// Crear pedido
$addressId = 1; // Por simplicidad, usar dirección por defecto. En producción, permitir selección.

$orderId = $orderModel->createOrder($userId, $cartItems, $addressId, $paymentMethod);

if (!$orderId) {
    $_SESSION['payment_errors'] = ["Error al crear el pedido. Inténtalo de nuevo."];
    header("Location: /SuperMarketArthur/?vistaMenu=checkout");
    exit();
}

// Limpiar carrito
$cartModel->clearCart($userId);

// Redirigir a confirmación
header("Location: /SuperMarketArthur/?vistaMenu=confirmacion_pedido&order_id=" . $orderId);
exit();

function simulatePayment($method, $cartItems) {
    // Simulación de pago - en producción integrar con Stripe/PayPal
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }

    // Simular diferentes escenarios
    if ($method === 'tarjeta') {
        // 95% éxito para tarjetas
        return rand(1, 100) <= 95;
    } elseif ($method === 'paypal') {
        // 98% éxito para PayPal
        return rand(1, 100) <= 98;
    }

    return false;
}
?>
