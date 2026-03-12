<?php
class Product {
    private $pdo;
    private $cacheDir;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->cacheDir = __DIR__ . '/../cache';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    public function addProduct($data)
    {
        $allowedColumns = ['nombre_producto', 'descripcion', 'precio', 'stock', 'id_categoria', 'url_imagen'];
        $filteredData = array_intersect_key($data, array_flip($allowedColumns));
        $fields = implode(', ', array_keys($filteredData));
        $placeholders = str_repeat('?, ', count($filteredData) - 1) . '?';
        $sql = "INSERT INTO productos ($fields) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(array_values($filteredData));
    }

    public function updateProduct($id, $data)
    {
        // 1. Filtrar columnas para seguridad
        $allowedColumns = ['nombre_producto', 'descripcion', 'precio', 'stock', 'id_categoria', 'url_imagen'];
        $filteredData = array_intersect_key($data, array_flip($allowedColumns));

        // Si no hay datos válidos para actualizar, no hacemos nada.
        if (empty($filteredData)) {
            return false;
        }

        // 2. Construir la parte SET de la consulta dinámicamente
        $setParts = [];
        foreach ($filteredData as $key => $value) {
            $setParts[] = "$key = ?";
        }
        $setClause = implode(', ', $setParts);

        // 3. Preparar los valores para la ejecución
        $values = array_values($filteredData);
        $values[] = $id; // Añadir el ID al final para el WHERE

        // 4. Construir y ejecutar la consulta SQL
        $sql = "UPDATE productos SET $setClause WHERE id_producto = ?";
        $stmt = $this->pdo->prepare($sql);

        // 5. Devolver el resultado de la ejecución
        return $stmt->execute($values);
    }

    /**
     * Elimina un producto de la base de datos por su ID.
     */
    public function deleteProductById($id)
    {
        $sql = "DELETE FROM productos WHERE id_producto = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getAllProducts($limit = null, $offset = 0) {
        $sql = "SELECT p.*, c.nombre_categoria FROM productos p LEFT JOIN categorias c ON p.id_categoria = c.id_categoria ORDER BY p.nombre_producto";
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
            require_once 'Rating.php';
            $ratingModel = new Rating($this->pdo);
            $ratingInfo = $ratingModel->getAverageRating($id);
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

    public function getTotalProductsByCategory($categoryId)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM productos WHERE id_categoria = ?");
        $stmt->execute([$categoryId]);
        return $stmt->fetchColumn();
    }

    public function getFeaturedProducts($limit = 10) {
        $cacheKey = 'featured_products_' . $limit;
        $cacheFile = $this->cacheDir . '/' . $cacheKey . '.json';
        $cacheTime = 3600; // 1 hour

        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
            $cachedProducts = file_get_contents($cacheFile);
            return json_decode($cachedProducts, true);
        }

        $stmt = $this->pdo->prepare("SELECT * FROM productos ORDER BY id_producto DESC LIMIT :limit");
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        file_put_contents($cacheFile, json_encode($products));

        return is_array($products) ? $products : [];
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

    public function searchProducts($query, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM productos WHERE nombre_producto LIKE :query OR descripcion LIKE :query ORDER BY nombre_producto";
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        $stmt = $this->pdo->prepare($sql);
        $searchQuery = "%$query%";
        $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
        if ($limit) {
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return is_array($results) ? $results : [];
    }

    public function getTotalProducts() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM productos");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getLowStockCount($threshold = 5)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM productos WHERE stock <= ?");
        $stmt->execute([$threshold]);
        return $stmt->fetchColumn();
    }
}
