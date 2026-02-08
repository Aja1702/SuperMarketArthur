<?php
class Order {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function createOrder($userId, $cartItems, $addressId, $paymentMethod = 'tarjeta') {
        $this->conn->begin_transaction();

        try {
            // Calcular total
            $total = 0;
            foreach ($cartItems as $item) {
                $total += $item['precio'] * $item['cantidad'];
            }

            // Crear pedido
            $sql = "INSERT INTO pedidos (id_usuario, total, id_direccion) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("idi", $userId, $total, $addressId);
            $stmt->execute();
            $orderId = $this->conn->insert_id;

            // Agregar items del pedido
            foreach ($cartItems as $item) {
                $sql = "INSERT INTO pedido_items (id_pedido, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("iiid", $orderId, $item['id_producto'], $item['cantidad'], $item['precio']);
                $stmt->execute();

                // Reducir stock
                $sql = "UPDATE productos SET stock = stock - ? WHERE id_producto = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("ii", $item['cantidad'], $item['id_producto']);
                $stmt->execute();
            }

            // Crear registro de pago
            $sql = "INSERT INTO pagos (id_pedido, metodo_pago) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("is", $orderId, $paymentMethod);
            $stmt->execute();

            $this->conn->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function getOrderById($orderId, $userId = null) {
        $sql = "SELECT p.*, u.nombre, u.apellido1, u.email, d.calle, d.ciudad, d.provincia, d.cp, d.pais
                FROM pedidos p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                LEFT JOIN direcciones d ON p.id_direccion = d.id_direccion
                WHERE p.id_pedido = ?";
        $params = [$orderId];
        $types = "i";

        if ($userId) {
            $sql .= " AND p.id_usuario = ?";
            $params[] = $userId;
            $types .= "i";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();

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
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserOrders($userId, $limit = 10, $offset = 0) {
        $sql = "SELECT p.*, COUNT(pi.id_pedido_item) as num_items
                FROM pedidos p
                LEFT JOIN pedido_items pi ON p.id_pedido = pi.id_pedido
                WHERE p.id_usuario = ?
                GROUP BY p.id_pedido
                ORDER BY p.fecha DESC
                LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $userId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllOrders($limit = 50, $offset = 0) {
        $sql = "SELECT p.*, u.nombre, u.apellido1, u.email, COUNT(pi.id_pedido_item) as num_items
                FROM pedidos p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                LEFT JOIN pedido_items pi ON p.id_pedido = pi.id_pedido
                GROUP BY p.id_pedido
                ORDER BY p.fecha DESC
                LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateOrderStatus($orderId, $status) {
        $validStatuses = ['pendiente', 'pagado', 'enviado', 'entregado', 'cancelado'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        $sql = "UPDATE pedidos SET estado = ? WHERE id_pedido = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $status, $orderId);
        return $stmt->execute();
    }

    public function getOrdersByStatus($status, $limit = 20, $offset = 0) {
        $sql = "SELECT p.*, u.nombre, u.apellido1, u.email
                FROM pedidos p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                WHERE p.estado = ?
                ORDER BY p.fecha DESC
                LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $status, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
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
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }

    public function cancelOrder($orderId, $userId) {
        $this->conn->begin_transaction();

        try {
            // Verificar que el pedido pertenece al usuario y está en estado pendiente
            $sql = "SELECT estado FROM pedidos WHERE id_pedido = ? AND id_usuario = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $orderId, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $order = $result->fetch_assoc();

            if (!$order || $order['estado'] !== 'pendiente') {
                $this->conn->rollback();
                return false;
            }

            // Restaurar stock
            $items = $this->getOrderItems($orderId);
            foreach ($items as $item) {
                $sql = "UPDATE productos SET stock = stock + ? WHERE id_producto = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("ii", $item['cantidad'], $item['id_producto']);
                $stmt->execute();
            }

            // Cancelar pedido
            $this->updateOrderStatus($orderId, 'cancelado');

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}
