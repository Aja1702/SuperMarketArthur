<?php
class Rating {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Agrega una nueva valoración a un producto.
     */
    public function create($id_producto, $id_usuario, $puntuacion, $comentario) {
        // CORREGIDO: Se usa CURRENT_TIMESTAMP en lugar de NOW() para ser compatible con SQLite y MySQL.
        $sql = "INSERT INTO valoraciones (id_producto, id_usuario, puntuacion, comentario, fecha) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_producto, $id_usuario, $puntuacion, $comentario]);
    }

    /**
     * Obtiene todas las valoraciones para un producto específico.
     */
    public function getByProduct($id_producto) {
        $sql = "SELECT v.*, u.nombre AS nombre_usuario
                FROM valoraciones v
                JOIN usuarios u ON v.id_usuario = u.id_usuario
                WHERE v.id_producto = ?
                ORDER BY v.fecha DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_producto]);
        return $stmt->fetchAll();
    }

    /**
     * Calcula la puntuación media y el total de valoraciones para un producto.
     */
    public function getAverageRating($id_producto) {
        $sql = "SELECT AVG(puntuacion) as average, COUNT(id_valoracion) as total FROM valoraciones WHERE id_producto = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_producto]);
        $result = $stmt->fetch();

        return [
            'average' => $result['average'] ?? 0,
            'total' => $result['total'] ?? 0
        ];
    }
}
?>