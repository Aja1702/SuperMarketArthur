<?php
class Rating {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Agregar una valoración
    public function addRating($id_usuario, $id_producto, $valoracion, $comentario = null) {
        // Verificar si ya existe una valoración
        $stmt = $this->pdo->prepare("SELECT id_valoracion FROM valoraciones WHERE id_usuario = ? AND id_producto = ?");
        $stmt->execute([$id_usuario, $id_producto]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Actualizar valoración existente
            $stmt = $this->pdo->prepare("UPDATE valoraciones SET valoracion = ?, comentario = ?, fecha_valoracion = NOW() WHERE id_valoracion = ?");
            return $stmt->execute([$valoracion, $comentario, $existing['id_valoracion']]);
        } else {
            // Insertar nueva valoración
            $stmt = $this->pdo->prepare("INSERT INTO valoraciones (id_usuario, id_producto, valoracion, comentario, fecha_valoracion) VALUES (?, ?, ?, ?, NOW())");
            return $stmt->execute([$id_usuario, $id_producto, $valoracion, $comentario]);
        }
    }

    // Obtener valoraciones de un producto
    public function getProductRatings($id_producto) {
        $stmt = $this->pdo->prepare("
            SELECT v.*, u.nombre, u.apellido1
            FROM valoraciones v
            JOIN usuarios u ON v.id_usuario = u.id_usuario
            WHERE v.id_producto = ?
            ORDER BY v.fecha_valoracion DESC
        ");
        $stmt->execute([$id_producto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Calcular promedio de valoraciones
    public function getAverageRating($id_producto) {
        $stmt = $this->pdo->prepare("SELECT AVG(valoracion) as promedio, COUNT(*) as total FROM valoraciones WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'promedio' => round($result['promedio'] ?? 0, 1),
            'total' => $result['total'] ?? 0
        ];
    }

    // Obtener valoración de un usuario para un producto
    public function getUserRating($id_usuario, $id_producto) {
        $stmt = $this->pdo->prepare("SELECT * FROM valoraciones WHERE id_usuario = ? AND id_producto = ?");
        $stmt->execute([$id_usuario, $id_producto]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Eliminar valoración
    public function deleteRating($id_usuario, $id_producto) {
        $stmt = $this->pdo->prepare("DELETE FROM valoraciones WHERE id_usuario = ? AND id_producto = ?");
        return $stmt->execute([$id_usuario, $id_producto]);
    }
}
