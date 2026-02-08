<?php
session_start();
require_once '../config/iniciar_session.php'; // Para obtener $pdo

header('Content-Type: application/json');

$action = $_GET['action'] ?? ($_POST['action'] ?? '');
$id_usuario = $_SESSION['id_usuario'] ?? null;
$es_invitado = ($id_usuario === null);

// --- Funciones de Utilidad ---
function getProductData($pdo, $id_producto)
{
    if (!$pdo)
        return null;
    $stmt = $pdo->prepare("SELECT id_producto, nombre_producto, precio, url_imagen, tipo_iva FROM productos WHERE id_producto = ?");
    $stmt->execute([$id_producto]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// --- Lógica del Carrito ---

if ($action === 'get_items') {
    $items = [];
    $total = 0;

    if ($es_invitado) {
        $carrito_sesion = $_SESSION['carrito'] ?? [];
        foreach ($carrito_sesion as $id => $cantidad) {
            $p = getProductData($pdo, $id);
            if ($p) {
                $p['cantidad'] = $cantidad;
                $p['subtotal'] = $p['precio'] * $cantidad;
                $p['precio_formatted'] = number_format($p['precio'], 2, ',', '.') . '€';
                $items[] = $p;
                $total += $p['subtotal'];
            }
        }
    }
    else {
        // Logueado: Buscar en tablas carrito_temp y carrito_items
        $stmt = $pdo->prepare("
            SELECT p.id_producto, p.nombre_producto, p.precio, p.url_imagen, ci.cantidad 
            FROM carrito_items ci
            JOIN carrito_temp ct ON ci.id_carrito = ct.id_carrito
            JOIN productos p ON ci.id_producto = p.id_producto
            WHERE ct.id_usuario = ?
        ");
        $stmt->execute([$id_usuario]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($res as $row) {
            $row['subtotal'] = $row['precio'] * $row['cantidad'];
            $row['precio_formatted'] = number_format($row['precio'], 2, ',', '.') . '€';
            $items[] = $row;
            $total += $row['subtotal'];
        }
    }

    echo json_encode([
        'success' => true,
        'items' => $items,
        'total' => $total,
        'total_formatted' => number_format($total, 2, ',', '.') . '€'
    ]);
    exit;
}

if ($action === 'add') {
    $id_producto = $_POST['id_producto'] ?? 0;
    if (!$id_producto) {
        echo json_encode(['success' => false, 'message' => 'Producto no válido']);
        exit;
    }

    if ($es_invitado) {
        if (!isset($_SESSION['carrito']))
            $_SESSION['carrito'] = [];
        if (isset($_SESSION['carrito'][$id_producto])) {
            $_SESSION['carrito'][$id_producto]++;
        }
        else {
            $_SESSION['carrito'][$id_producto] = 1;
        }
    }
    else {
        // Logueado: Asegurar carrito_temp y añadir a carrito_items
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("SELECT id_carrito FROM carrito_temp WHERE id_usuario = ?");
            $stmt->execute([$id_usuario]);
            $carrito = $stmt->fetch();

            if (!$carrito) {
                $stmt = $pdo->prepare("INSERT INTO carrito_temp (id_usuario) VALUES (?)");
                $stmt->execute([$id_usuario]);
                $id_carrito = $pdo->lastInsertId();
            }
            else {
                $id_carrito = $carrito['id_carrito'];
            }

            // Comprobar si ya existe el item
            $stmt = $pdo->prepare("SELECT id_item, cantidad FROM carrito_items WHERE id_carrito = ? AND id_producto = ?");
            $stmt->execute([$id_carrito, $id_producto]);
            $item = $stmt->fetch();

            if ($item) {
                $stmt = $pdo->prepare("UPDATE carrito_items SET cantidad = cantidad + 1 WHERE id_item = ?");
                $stmt->execute([$item['id_item']]);
            }
            else {
                $stmt = $pdo->prepare("INSERT INTO carrito_items (id_carrito, id_producto, cantidad) VALUES (?, ?, 1)");
                $stmt->execute([$id_carrito, $id_producto]);
            }
            $pdo->commit();
        }
        catch (Exception $e) {
            if ($pdo->inTransaction())
                $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'update_qty') {
    $id_producto = $_POST['id_producto'] ?? 0;
    $delta = (int)($_POST['delta'] ?? 0);

    if (!$id_producto) {
        echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
        exit;
    }

    if ($es_invitado) {
        if (isset($_SESSION['carrito'][$id_producto])) {
            $_SESSION['carrito'][$id_producto] += $delta;
            if ($_SESSION['carrito'][$id_producto] <= 0) {
                unset($_SESSION['carrito'][$id_producto]);
            }
        }
    }
    else {
        // En BD
        $stmt = $pdo->prepare("
            UPDATE carrito_items ci
            JOIN carrito_temp ct ON ci.id_carrito = ct.id_carrito
            SET ci.cantidad = ci.cantidad + ?
            WHERE ct.id_usuario = ? AND ci.id_producto = ?
        ");
        $stmt->execute([$delta, $id_usuario, $id_producto]);

        // Limpiar si cantidad <= 0
        $stmt = $pdo->prepare("
            DELETE ci FROM carrito_items ci
            JOIN carrito_temp ct ON ci.id_carrito = ct.id_carrito
            WHERE ct.id_usuario = ? AND ci.cantidad <= 0
        ");
        $stmt->execute([$id_usuario]);
    }

    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Acción no reconocida']);
