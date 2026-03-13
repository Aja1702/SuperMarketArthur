<?php

namespace App\Controllers\Shop;

require_once __DIR__ . '/../../Models/Cart.php';
require_once __DIR__ . '/../../Models/Order.php';
require_once __DIR__ . '/../../Services/Payment/StripePayment.php';

use Cart;
use Order;
use App\Services\Payment\StripePayment;

class CheckoutController
{
    public function index()
    {
        global $pdo, $simbolo_moneda;

        if (!isset($_SESSION['id_usuario'])) {
            header('Location: /SuperMarketArthur/login');
            exit();
        }

        $cart = new Cart($pdo, $_SESSION['id_usuario']);
        $cartItems = $cart->getItems();
        $cartTotal = $cart->getTotal();

        if (empty($cartItems)) {
            header('Location: /SuperMarketArthur/productos');
            exit();
        }

        // Obtener clave pública de Stripe
        $stripePayment = new StripePayment();
        $stripePublishableKey = $stripePayment->getPublishableKey();

        $data = [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'cartTotalFormatted' => number_format($cartTotal, 2, ',', '.') . htmlspecialchars($simbolo_moneda),
            'stripePublishableKey' => $stripePublishableKey,
            'stripeTestMode' => $stripePayment->isTestMode()
        ];

        $this->view('checkout', $data);
    }

    /**
     * Crea sesión de pago con Stripe
     */
    public function createStripeSession()
    {
        global $pdo;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['id_usuario'])) {
            header('Location: /SuperMarketArthur/checkout');
            exit();
        }

        // Verificar CSRF
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
            die('Error CSRF: Token inválido');
        }

        $id_usuario = $_SESSION['id_usuario'];
        $cart = new Cart($pdo, $id_usuario);
        $cartItems = $cart->getItems();
        $cartTotal = $cart->getTotal();

        if (empty($cartItems)) {
            header('Location: /SuperMarketArthur/productos');
            exit();
        }

        try {
            $stripePayment = new StripePayment();
            $session = $stripePayment->createCheckoutSession($cartItems, $cartTotal);
            
            // Guardar datos del pedido en sesión para después del pago
            $_SESSION['pending_order'] = [
                'cart_items' => $cartItems,
                'cart_total' => $cartTotal,
                'direccion' => [
                    'nombre_completo' => $_POST['nombre_completo'] ?? '',
                    'direccion' => $_POST['direccion'] ?? '',
                    'ciudad' => $_POST['ciudad'] ?? '',
                    'codigo_postal' => $_POST['codigo_postal'] ?? '',
                    'telefono' => $_POST['telefono'] ?? ''
                ],
                'session_id' => $session->id
            ];

            // Redirigir a Stripe
            header('Location: ' . $session->url);
            exit();
            
        } catch (\Exception $e) {
            error_log('Stripe Error: ' . $e->getMessage());
            header('Location: /SuperMarketArthur/checkout?error=stripe');
            exit();
        }
    }

    public function processOrder()
    {
        global $pdo;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['id_usuario'])) {
            header('Location: /SuperMarketArthur/checkout');
            exit();
        }

        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
            die('Error CSRF: Token inválido');
        }

        $id_usuario = $_SESSION['id_usuario'];
        $cart = new Cart($pdo, $id_usuario);
        $cartItems = $cart->getItems();
        $cartTotal = $cart->getTotal();

        if (empty($cartItems)) {
            header('Location: /SuperMarketArthur/productos');
            exit();
        }

        $direccion = [
            'nombre_completo' => $_POST['nombre_completo'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'ciudad' => $_POST['ciudad'] ?? '',
            'codigo_postal' => $_POST['codigo_postal'] ?? '',
            'telefono' => $_POST['telefono'] ?? ''
        ];

        $orderModel = new Order($pdo);
        $id_pedido = $orderModel->createOrder($id_usuario, $cartItems, $cartTotal, $direccion);

        if ($id_pedido) {
            $cart->clearCart();
            header('Location: /SuperMarketArthur/order-confirmation?id=' . $id_pedido);
            exit();
        } else {
            // Si falla, redirigir de vuelta con un error
            header('Location: /SuperMarketArthur/checkout?error=1');
            exit();
        }
    }

    /**
     * Confirma el pago de Stripe (callback desde Stripe)
     */
    public function confirmStripePayment()
    {
        global $pdo;

        $sessionId = $_GET['session_id'] ?? null;

        if (!$sessionId || !isset($_SESSION['pending_order'])) {
            header('Location: /SuperMarketArthur/checkout');
            exit();
        }

        try {
            $stripePayment = new StripePayment();
            
            // Verificar el pago
            if ($stripePayment->verifyPayment($sessionId)) {
                // El pago fue exitoso - crear el pedido
                $pendingOrder = $_SESSION['pending_order'];
                
                $orderModel = new Order($pdo);
                $id_pedido = $orderModel->createOrder(
                    $_SESSION['id_usuario'],
                    $pendingOrder['cart_items'],
                    $pendingOrder['cart_total'],
                    $pendingOrder['direccion']
                );

                if ($id_pedido) {
                    // Limpiar carrito y sesión pendiente
                    $cart = new Cart($pdo, $_SESSION['id_usuario']);
                    $cart->clearCart();
                    unset($_SESSION['pending_order']);

                    header('Location: /SuperMarketArthur/order-confirmation?id=' . $id_pedido);
                    exit();
                }
            }
            
            // Si falla la verificación
            header('Location: /SuperMarketArthur/checkout?error=payment_failed');
            exit();
            
        } catch (\Exception $e) {
            error_log('Stripe Confirm Error: ' . $e->getMessage());
            header('Location: /SuperMarketArthur/checkout?error=stripe');
            exit();
        }
    }

    protected function view($view, $data = [])
    {
        global $nombre_sitio, $cache_version, $rutas, $tipo_usuario, $simbolo_moneda;

        $data = array_merge($data, [
            'nombre_sitio' => $nombre_sitio,
            'cache_version' => $cache_version,
            'rutas' => $rutas,
            'tipo_usuario' => $tipo_usuario,
            'simbolo_moneda' => $simbolo_moneda
        ]);

        extract($data);

        ob_start();
        require_once __DIR__ . "/../../views/{$view}.php";
        $content = (string)ob_get_clean();

        require_once __DIR__ . '/../../views/layout.php';
    }
}
