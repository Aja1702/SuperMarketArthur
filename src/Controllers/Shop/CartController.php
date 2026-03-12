<?php

namespace App\Controllers\Shop;

require_once __DIR__ . '/../../Models/Cart.php';
use Cart;

class CartController
{
    private $cart;
    private $simbolo_moneda;

    public function __construct() {
        // Forzar el inicio de la sesión si no está iniciada.
        // Esto es crucial para que las llamadas AJAX reconozcan al usuario.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        global $pdo, $simbolo_moneda;
        $id_usuario = $_SESSION['id_usuario'] ?? null;
        $this->cart = new Cart($pdo, $id_usuario);
        $this->simbolo_moneda = $simbolo_moneda;
    }

    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    public function getItems() {
        try {
            $items = $this->cart->getItems();
            $total = $this->cart->getTotal();
            $formattedItems = [];
            foreach ($items as $item) {
                $item['precio_formatted'] = number_format($item['precio'], 2, ',', '.') . htmlspecialchars($this->simbolo_moneda);
                $formattedItems[] = $item;
            }
            $subtotal = $total / 1.10;
            $iva = $total - $subtotal;

            $this->jsonResponse([
                'success' => true,
                'items' => $formattedItems,
                'total_formatted' => number_format($total, 2, ',', '.') . htmlspecialchars($this->simbolo_moneda),
                'subtotal_formatted' => number_format($subtotal, 2, ',', '.') . htmlspecialchars($this->simbolo_moneda),
                'iva_formatted' => number_format($iva, 2, ',', '.') . htmlspecialchars($this->simbolo_moneda)
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function addItem() {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
            return $this->jsonResponse(['success' => false, 'message' => 'Error CSRF: Token inválido']);
        }

        $id_producto = $_POST['id_producto'] ?? 0;
        $cantidad = (int)($_POST['cantidad'] ?? 1);

        if (!$id_producto) {
            return $this->jsonResponse(['success' => false, 'message' => 'Producto no válido']);
        }

        if ($this->cart->addItem($id_producto, $cantidad)) {
            $this->jsonResponse(['success' => true, 'message' => 'Producto añadido']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'No se pudo añadir (stock insuficiente)']);
        }
    }

    public function updateQuantity() {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
            return $this->jsonResponse(['success' => false, 'message' => 'Error CSRF: Token inválido']);
        }

        $id_producto = $_POST['id_producto'] ?? 0;
        $delta = (int)($_POST['delta'] ?? 0);

        if (!$id_producto || !$delta) {
            return $this->jsonResponse(['success' => false, 'message' => 'Parámetros inválidos']);
        }

        if ($this->cart->adjustQuantity($id_producto, $delta)) {
            $this->jsonResponse(['success' => true, 'message' => 'Cantidad actualizada']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'No se pudo actualizar']);
        }
    }
}
