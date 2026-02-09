<?php
class Product {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllProducts($limit = null, $offset = 0) {
        $sql = "SELECT * FROM productos ORDER BY id_producto";
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
        }
        $stmt = $this->pdo->prepare($sql);
        if ($limit) {
            $stmt->execute([$limit, $offset]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM productos WHERE id_producto = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            // Incluimos el modelo de Rating y calculamos la información
            require_once 'Rating.php';
            $ratingModel = new Rating($this->pdo);
            $ratingInfo = $ratingModel->getAverageRating($id);

            // Añadimos la información al array del producto
            $product['rating_average'] = round($ratingInfo['average'], 1);
            $product['rating_total'] = $ratingInfo['total'];
        }

        return $product;
    }

    public function getProductsByCategory($categoryId, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM productos WHERE id_categoria = ? ORDER BY nombre_producto";
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
        }
        $stmt = $this->pdo->prepare($sql);
        if ($limit) {
            $stmt->execute([$categoryId, $limit, $offset]);
        } else {
            $stmt->execute([$categoryId]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchProducts($query, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM productos WHERE nombre_producto LIKE ? OR descripcion LIKE ? ORDER BY nombre_producto";
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
        }
        $stmt = $this->pdo->prepare($sql);
        $searchTerm = "%$query%";
        if ($limit) {
            $stmt->execute([$searchTerm, $searchTerm, $limit, $offset]);
        } else {
            $stmt->execute([$searchTerm, $searchTerm]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductsByPriceRange($minPrice, $maxPrice, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM productos WHERE precio BETWEEN ? AND ? ORDER BY precio";
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
        }
        $stmt = $this->pdo->prepare($sql);
        if ($limit) {
            $stmt->execute([$minPrice, $maxPrice, $limit, $offset]);
        } else {
            $stmt->execute([$minPrice, $maxPrice]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFeaturedProducts($limit = 10) {
        $stmt = $this->pdo->prepare("SELECT * FROM productos WHERE destacado = 1 ORDER BY id_producto DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductsOnSale($limit = null, $offset = 0) {
        $sql = "SELECT * FROM productos WHERE precio_oferta IS NOT NULL AND precio_oferta < precio ORDER BY (precio - precio_oferta) DESC";
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
        }
        $stmt = $this->pdo->prepare($sql);
        if ($limit) {
            $stmt->execute([$limit, $offset]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateProduct($id, $data) {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $sql = "UPDATE productos SET " . implode(', ', $fields) . " WHERE id_producto = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    public function deleteProduct($id) {
        $stmt = $this->pdo->prepare("DELETE FROM productos WHERE id_producto = ?");
        return $stmt->execute([$id]);
    }

    public function addProduct($data) {
        $fields = implode(', ', array_keys($data));
        $placeholders = str_repeat('?, ', count($data) - 1) . '?';
        $sql = "INSERT INTO productos ($fields) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(array_values($data));
    }

    public function getTotalProducts() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM productos");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getCategories() {
        $stmt = $this->pdo->prepare("SELECT * FROM categorias ORDER BY nombre_categoria");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM categorias WHERE id_categoria = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
