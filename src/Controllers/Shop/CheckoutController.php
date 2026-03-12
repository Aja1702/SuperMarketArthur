<?php

namespace App\Controllers\Shop;

require_once __DIR__ . '/../../Models/Cart.php';
require_once __DIR__ . '/../../Models/Order.php';

use Cart;
use Order;

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

        $data = [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'cartTotalFormatted' => number_format($cartTotal, 2, ',', '.') . htmlspecialchars($simbolo_moneda)
        ];

        $this->view('checkout', $data);
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
