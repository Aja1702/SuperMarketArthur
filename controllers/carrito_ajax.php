<?php
session_start();
require_once '../config/iniciar_session.php'; // Para obtener $pdo
require_once '../models/Cart.php';

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';
$id_usuario = $_SESSION['id_usuario'] ?? null;

// Crear una instancia del carrito
$cart = new Cart($pdo, $id_usuario);

// --- Lógica del Carrito ---

switch ($action) {
    case 'get_items':
        try {
            $items = $cart->getItems();
            $total = $cart->getTotal();

            // Formatear items para la vista
            $formattedItems = [];
            foreach ($items as $item) {
                $item['subtotal'] = $item['precio'] * $item['cantidad'];
                $item['precio_formatted'] = number_format($item['precio'], 2, ',', '.') . '€';
                $formattedItems[] = $item;
            }

            echo json_encode([
                'success' => true,
                'items' => $formattedItems,
                'total' => $total,
                'total_formatted' => number_format($total, 2, ',', '.') . '€'
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al obtener el carrito: ' . $e->getMessage()]);
        }
        break;

    case 'add':
        $id_producto = $_POST['id_producto'] ?? 0;
        $cantidad = (int)($_POST['cantidad'] ?? 1);

        if (!$id_producto) {
            echo json_encode(['success' => false, 'message' => 'Producto no válido']);
            break;
        }

        if ($cart->addItem($id_producto, $cantidad)) {
            echo json_encode(['success' => true, 'message' => 'Producto añadido']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo añadir el producto (stock insuficiente)']);
        }
        break;

    case 'update_qty':
        $id_producto = $_POST['id_producto'] ?? 0;
        $delta = (int)($_POST['delta'] ?? 0);

        if (!$id_producto || !$delta) {
            echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
            break;
        }

        if ($cart->adjustQuantity($id_producto, $delta)) {
            echo json_encode(['success' => true, 'message' => 'Cantidad actualizada']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo actualizar la cantidad']);
        }
        break;

    case 'remove':
        $id_producto = $_POST['id_producto'] ?? 0;
        if (!$id_producto) {
            echo json_encode(['success' => false, 'message' => 'Producto no válido']);
            break;
        }

        if ($cart->removeItem($id_producto)) {
            echo json_encode(['success' => true, 'message' => 'Producto eliminado']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo eliminar el producto']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no reconocida']);
        break;
}
