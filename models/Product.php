<?php
class Product {
    private $pdo;
    private $cacheDir;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        // Define el directorio de caché y lo crea si no existe
        $this->cacheDir = __DIR__ . '/../cache';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    public function getAllProducts($limit = null, $offset = 0) {
        $sql = "SELECT p.*, c.nombre_categoria
                FROM productos p
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                ORDER BY p.nombre_producto";

        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        } else {
            $stmt = $this->pdo->prepare($sql);
        }
        $stmt->execute();
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
        $sql = "SELECT * FROM productos WHERE id_categoria = :categoryId ORDER BY nombre_producto";
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        if ($limit) {
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchProducts($query, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM productos WHERE nombre_producto LIKE :query OR descripcion LIKE :query ORDER BY nombre_producto";
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':query', "%$query%", PDO::PARAM_STR);
        if ($limit) {
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductsByPriceRange($minPrice, $maxPrice, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM productos WHERE precio BETWEEN :minPrice AND :maxPrice ORDER BY precio";
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':minPrice', $minPrice);
        $stmt->bindValue(':maxPrice', $maxPrice);
        if ($limit) {
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFeaturedProducts($limit = 10) {
        $cacheKey = 'featured_products_' . $limit;
        $cacheFile = $this->cacheDir . '/' . $cacheKey . '.json';
        $cacheTime = 3600; // 1 hora de duración de la caché

        // Intenta leer desde la caché primero
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
            $cachedProducts = file_get_contents($cacheFile);
            return json_decode($cachedProducts, true);
        }

        // Si la caché no es válida o no existe, consulta la base de datos
        $stmt = $this->pdo->prepare("SELECT * FROM productos ORDER BY id_producto DESC LIMIT :limit");
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Guarda el nuevo resultado en la caché para la próxima vez
        file_put_contents($cacheFile, json_encode($products));

        return $products;
    }

    public function getProductsOnSale($limit = null, $offset = 0) {
        $sql = "SELECT * FROM productos WHERE precio_oferta IS NOT NULL AND precio_oferta < precio ORDER BY (precio - precio_oferta) DESC";
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        $stmt = $this->pdo->prepare($sql);
        if ($limit) {
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
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

    public function getLowStockProducts($limit, $offset, $threshold = 5) {
        $sql = "SELECT * FROM productos WHERE stock <= :threshold ORDER BY stock ASC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':threshold', $threshold, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
