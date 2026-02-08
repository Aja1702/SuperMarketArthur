<?php
class Rating {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addRating($productId, $userId, $rating, $comment = null) {
        // Verificar si el usuario ya ha valorado este producto
        $sqlCheck = "SELECT id_valoracion FROM valoraciones WHERE id_producto = ? AND id_usuario = ?";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->bind_param("ii", $productId, $userId);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            // Actualizar valoración existente
            $sql = "UPDATE valoraciones SET puntuacion = ?, comentario = ?, fecha = NOW() WHERE id_producto = ? AND id_usuario = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("isii", $rating, $comment, $productId, $userId);
        } else {
            // Insertar nueva valoración
            $sql = "INSERT INTO valoraciones (id_producto, id_usuario, puntuacion, comentario) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iiis", $productId, $userId, $rating, $comment);
        }
        return $stmt->execute();
    }

    public function getRatingsByProduct($productId, $limit = 10, $offset = 0) {
        $sql = "SELECT v.*, u.nombre, u.apellido1 FROM valoraciones v JOIN usuarios u ON v.id_usuario = u.id_usuario WHERE v.id_producto = ? ORDER BY v.fecha DESC LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $productId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAverageRating($productId) {
        $sql = "SELECT AVG(puntuacion) as promedio, COUNT(*) as total FROM valoraciones WHERE id_producto = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return [
            'promedio' => round($row['promedio'], 1),
            'total' => $row['total']
        ];
    }

    public function getUserRating($productId, $userId) {
        $sql = "SELECT puntuacion, comentario FROM valoraciones WHERE id_producto = ? AND id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $productId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function deleteRating($productId, $userId) {
        $sql = "DELETE FROM valoraciones WHERE id_producto = ? AND id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $productId, $userId);
        return $stmt->execute();
    }
}
