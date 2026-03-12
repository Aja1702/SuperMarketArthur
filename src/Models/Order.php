<?php

class Order {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createOrder($id_usuario, $cartItems, $total, $direccion) {
        try {
            $this->pdo->beginTransaction();
            $id_direccion = 1; // Placeholder
            $stmtPedido = $this->pdo->prepare(
                "INSERT INTO pedidos (id_usuario, total, estado, id_direccion) VALUES (?, ?, 'pendiente', ?)"
            );
            $stmtPedido->execute([$id_usuario, $total, $id_direccion]);
            $id_pedido = $this->pdo->lastInsertId();

            $stmtItem = $this->pdo->prepare(
                "INSERT INTO pedido_items (id_pedido, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)"
            );
            $stmtStock = $this->pdo->prepare(
                "UPDATE productos SET stock = stock - ? WHERE id_producto = ? AND stock >= ?"
            );

            foreach ($cartItems as $item) {
                $stmtItem->execute([$id_pedido, $item['id_producto'], $item['cantidad'], $item['precio']]);
                $stmtStock->execute([$item['cantidad'], $item['id_producto'], $item['cantidad']]);
                if ($stmtStock->rowCount() === 0) {
                    throw new Exception("Stock insuficiente para el producto: " . $item['nombre_producto']);
                }
            }
            $this->pdo->commit();
            return $id_pedido;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function getOrderById($id_pedido) {
        $stmt = $this->pdo->prepare("SELECT * FROM pedidos WHERE id_pedido = ?");
        $stmt->execute([$id_pedido]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            $stmtItems = $this->pdo->prepare(
                "SELECT pi.*, p.nombre_producto, p.url_imagen FROM pedido_items pi JOIN productos p ON pi.id_producto = p.id_producto WHERE pi.id_pedido = ?"
            );
            $stmtItems->execute([$id_pedido]);
            $order['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
        }
        return $order;
    }

    /**
     * Obtiene los pedidos más recientes.
     */
    public function getRecentOrders($limit = 5)
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.id_pedido, u.nombre, p.total, p.estado, DATE_FORMAT(p.fecha, '%d/%m %H:%i') as fecha_fmt
             FROM pedidos p JOIN usuarios u ON p.id_usuario = u.id_usuario
             ORDER BY p.id_pedido DESC LIMIT :limit"
        );
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta el número de pedidos por estado.
     */
    public function getOrdersCountByStatus($status)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM pedidos WHERE estado = ?");
        $stmt->execute([$status]);
        return $stmt->fetchColumn();
    }

    /**
     * Calcula los ingresos totales de los pedidos pagados.
     */
    public function getTotalRevenue()
    {
        $stmt = $this->pdo->query("SELECT COALESCE(SUM(total), 0) FROM pedidos WHERE estado='pagado'");
        return $stmt->fetchColumn();
    }
}
