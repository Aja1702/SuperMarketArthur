<?php
class Order {
    private $pdo;
    public $lastError; // Para almacenar el último error

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createOrder($userId, $cartItems, $addressId, $paymentMethod = 'tarjeta') {
        $this->lastError = null; // Limpiar errores previos
        $this->pdo->beginTransaction();

        try {
            // Calcular total
            $total = 0;
            foreach ($cartItems as $item) {
                // Asegurarse de que el precio es numérico
                if (!is_numeric($item['precio']) || !is_numeric($item['cantidad'])) {
                    throw new Exception("Precio o cantidad inválidos para el producto ID: " . $item['id_producto']);
                }
                $total += $item['precio'] * $item['cantidad'];
            }

            // Crear pedido
            $sql = "INSERT INTO pedidos (id_usuario, total, id_direccion) VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId, $total, $addressId]);
            $orderId = $this->pdo->lastInsertId();

            if (!$orderId) {
                throw new Exception("No se pudo obtener el ID del nuevo pedido.");
            }

            // Agregar items del pedido y actualizar stock
            $itemStmt = $this->pdo->prepare("INSERT INTO pedido_items (id_pedido, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
            $stockStmt = $this->pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ?");

            foreach ($cartItems as $item) {
                $itemStmt->execute([$orderId, $item['id_producto'], $item['cantidad'], $item['precio']]);
                $stockStmt->execute([$item['cantidad'], $item['id_producto']]);
            }

            // Crear registro de pago
            $sql = "INSERT INTO pagos (id_pedido, metodo_pago) VALUES (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$orderId, $paymentMethod]);

            $this->pdo->commit();
            return $orderId;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $this->lastError = $e->getMessage(); // Guardar el mensaje de error real
            return false;
        }
    }

    // ... resto de los métodos sin cambios ...
    public function getOrderById($orderId, $userId = null) {
        $sql = "SELECT p.*, u.nombre, u.apellido1, u.email, d.calle, d.ciudad, d.provincia, d.cp, d.pais
                FROM pedidos p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                LEFT JOIN direcciones d ON p.id_direccion = d.id_direccion
                WHERE p.id_pedido = ?";
        $params = [$orderId];

        if ($userId) {
            $sql .= " AND p.id_usuario = ?";
            $params[] = $userId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $order = $stmt->fetch();

        if ($order) {
            $order['items'] = $this->getOrderItems($orderId);
        }
        return $order;
    }

    public function getOrderItems($orderId) {
        $sql = "SELECT pi.*, p.nombre_producto, p.url_imagen
                FROM pedido_items pi
                JOIN productos p ON pi.id_producto = p.id_producto
                WHERE pi.id_pedido = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public function getUserOrders($userId, $limit = 10, $offset = 0) {
        $sql = "SELECT p.*, COUNT(pi.id_pedido_item) as num_items
                FROM pedidos p
                LEFT JOIN pedido_items pi ON p.id_pedido = pi.id_pedido
                WHERE p.id_usuario = ?
                GROUP BY p.id_pedido
                ORDER BY p.fecha DESC
                LIMIT ? OFFSET ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function getAllOrders($limit = 50, $offset = 0) {
        $sql = "SELECT p.*, u.nombre, u.apellido1, u.email, COUNT(pi.id_pedido_item) as num_items
                FROM pedidos p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                LEFT JOIN pedido_items pi ON p.id_pedido = pi.id_pedido
                GROUP BY p.id_pedido
                ORDER BY p.fecha DESC
                LIMIT ? OFFSET ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public function updateOrderStatus($orderId, $status) {
        $validStatuses = ['pendiente', 'pagado', 'enviado', 'entregado', 'cancelado'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        $sql = "UPDATE pedidos SET estado = ? WHERE id_pedido = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$status, $orderId]);
    }

    public function getOrdersByStatus($status, $limit = 20, $offset = 0) {
        $sql = "SELECT p.*, u.nombre, u.apellido1, u.email
                FROM pedidos p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                WHERE p.estado = ?
                ORDER BY p.fecha DESC
                LIMIT ? OFFSET ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$status, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function getOrderStats() {
        $sql = "SELECT
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN estado = 'pagado' THEN 1 ELSE 0 END) as paid,
                    SUM(CASE WHEN estado = 'enviado' THEN 1 ELSE 0 END) as shipped,
                    SUM(CASE WHEN estado = 'entregado' THEN 1 ELSE 0 END) as delivered,
                    SUM(CASE WHEN estado = 'cancelado' THEN 1 ELSE 0 END) as cancelled,
                    SUM(total) as total_revenue
                FROM pedidos";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch();
    }

    public function cancelOrder($orderId, $userId) {
        $this->pdo->beginTransaction();

        try {
            // Verificar que el pedido pertenece al usuario y está en estado pendiente
            $sql = "SELECT estado FROM pedidos WHERE id_pedido = ? AND id_usuario = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$orderId, $userId]);
            $order = $stmt->fetch();

            if (!$order || $order['estado'] !== 'pendiente') {
                $this->pdo->rollBack();
                return false;
            }

            // Restaurar stock
            $items = $this->getOrderItems($orderId);
            foreach ($items as $item) {
                $sql = "UPDATE productos SET stock = stock + ? WHERE id_producto = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$item['cantidad'], $item['id_producto']]);
            }

            // Cancelar pedido
            $this->updateOrderStatus($orderId, 'cancelado');

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

}
?>