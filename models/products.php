<?php
class Product {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllProducts($limit = null, $offset = 0) {
        $sql = "SELECT id_producto, nombre_producto, descripcion, precio, stock, id_categoria, url_imagen FROM productos ORDER BY nombre_producto ASC";
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
        }
        $stmt = $this->conn->prepare($sql);
        if ($limit !== null) {
            $stmt->bind_param("ii", $limit, $offset);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getProductById($id) {
        $sql = "SELECT * FROM productos WHERE id_producto = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function searchProducts($query, $category = null, $minPrice = null, $maxPrice = null, $limit = 20, $offset = 0) {
        $sql = "SELECT id_producto, nombre_producto, descripcion, precio, stock, id_categoria, url_imagen FROM productos WHERE nombre_producto LIKE ? OR descripcion LIKE ?";
        $params = ["%{$query}%", "%{$query}%"];
        $types = "ss";

        if ($category !== null) {
            $sql .= " AND id_categoria = ?";
            $params[] = $category;
            $types .= "i";
        }
        if ($minPrice !== null) {
            $sql .= " AND precio >= ?";
            $params[] = $minPrice;
            $types .= "d";
        }
        if ($maxPrice !== null) {
            $sql .= " AND precio <= ?";
            $params[] = $maxPrice;
            $types .= "d";
        }

        $sql .= " ORDER BY nombre_producto ASC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getProductsByCategory($categoryId, $limit = null, $offset = 0) {
        $sql = "SELECT id_producto, nombre_producto, descripcion, precio, stock, id_categoria, url_imagen FROM productos WHERE id_categoria = ? ORDER BY nombre_producto ASC";
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
        }
        $stmt = $this->conn->prepare($sql);
        if ($limit !== null) {
            $stmt->bind_param("iii", $categoryId, $limit, $offset);
        } else {
            $stmt->bind_param("i", $categoryId);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCategories() {
        $sql = "SELECT id_categoria, nombre_categoria, descripcion FROM categorias ORDER BY nombre_categoria ASC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateStock($productId, $newStock) {
        $sql = "UPDATE productos SET stock = ? WHERE id_producto = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $newStock, $productId);
        return $stmt->execute();
    }

    public function getLowStockProducts($threshold = 10) {
        $sql = "SELECT id_producto, nombre_producto, stock FROM productos WHERE stock <= ? ORDER BY stock ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $threshold);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
