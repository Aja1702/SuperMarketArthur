<?php

namespace App\Controllers;

require_once __DIR__ . '/../../models/Order.php';
use Order;

class OrderConfirmationController
{
    public function index()
    {
        global $pdo;
        $id_pedido = $_GET['id'] ?? null;

        if (!$id_pedido) {
            header('Location: /SuperMarketArthur/');
            exit();
        }

        $orderModel = new Order($pdo);
        $order = $orderModel->getOrderById($id_pedido);

        if (!$order) {
            // Si no se encuentra el pedido, redirigir al inicio
            header('Location: /SuperMarketArthur/');
            exit();
        }

        $this->view('order-confirmation', ['order' => $order]);
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
        $content = ob_get_clean();

        require_once __DIR__ . '/../../views/layout.php';
    }
}
