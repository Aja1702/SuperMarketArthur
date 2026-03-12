<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Rating.php';

class ProductTest extends TestCase
{
    private $pdo;
    private $productModel;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Crear las tablas necesarias para las pruebas de Producto
        $this->pdo->exec("CREATE TABLE productos (
            id_producto INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre_producto VARCHAR(100) NOT NULL,
            descripcion TEXT,
            precio DECIMAL(10,2),
            url_imagen VARCHAR(255)
        );");

        $this->pdo->exec("CREATE TABLE valoraciones (
            id_valoracion INTEGER PRIMARY KEY AUTOINCREMENT,
            id_producto INTEGER NOT NULL,
            puntuacion INTEGER NOT NULL
        );");

        $this->productModel = new Product($this->pdo);
    }

    protected function tearDown(): void
    {
        $this->pdo = null;
        $this->productModel = null;
    }

    /**
     * Test para verificar que podemos obtener un producto por su ID.
     */
    public function testCanGetProductById()
    {
        // 1. Insertar un producto de prueba en la BD
        $stmt = $this->pdo->prepare("INSERT INTO productos (nombre_producto, precio) VALUES (?, ?)");
        $stmt->execute(['Manzanas Golden', 2.99]);
        $productId = $this->pdo->lastInsertId();

        // 2. Llamar al método que queremos probar
        $product = $this->productModel->getProductById($productId);

        // 3. Afirmar que hemos recibido el producto correcto
        $this->assertIsArray($product);
        $this->assertEquals('Manzanas Golden', $product['nombre_producto']);
        $this->assertEquals(2.99, $product['precio']);
    }

    /**
     * Test para verificar que el cálculo del rating es correcto.
     */
    public function testCalculatesRatingCorrectly()
    {
        // 1. Insertar un producto de prueba
        $stmt = $this->pdo->prepare("INSERT INTO productos (nombre_producto, precio) VALUES (?, ?)");
        $stmt->execute(['Plátano de Canarias', 1.99]);
        $productId = $this->pdo->lastInsertId();

        // 2. Insertar algunas valoraciones para este producto
        $this->pdo->prepare("INSERT INTO valoraciones (id_producto, puntuacion) VALUES (?, ?), (?, ?)")->execute([$productId, 5, $productId, 3]);

        // 3. Obtener el producto (el método getProductById ya incluye la lógica del rating)
        $product = $this->productModel->getProductById($productId);

        // 4. Afirmar que los datos del rating son correctos
        $this->assertEquals(2, $product['rating_total']); // 2 valoraciones en total
        $this->assertEquals(4.0, $product['rating_average']); // La media de (5+3)/2 es 4
    }

    /**
     * Test para verificar que el buscador funciona correctamente.
     */
    public function testCanSearchProducts()
    {
        // 1. Insertar productos de prueba
        $this->pdo->prepare("INSERT INTO productos (nombre_producto, descripcion, precio) VALUES (?, ?, ?)")->execute(['Leche Entera', 'Leche de vaca fresca', 1.20]);
        $this->pdo->prepare("INSERT INTO productos (nombre_producto, descripcion, precio) VALUES (?, ?, ?)")->execute(['Leche Desnatada', 'Leche sin grasa', 1.10]);
        $this->pdo->prepare("INSERT INTO productos (nombre_producto, descripcion, precio) VALUES (?, ?, ?)")->execute(['Pan de Molde', 'Pan blanco tierno', 0.90]);

        // 2. Buscar por nombre
        $results = $this->productModel->searchProducts('Leche');
        $this->assertCount(2, $results);
        $this->assertEquals('Leche Desnatada', $results[0]['nombre_producto']); // Ordenado alfabéticamente

        // 3. Buscar por descripción
        $results = $this->productModel->searchProducts('tierno');
        $this->assertCount(1, $results);
        $this->assertEquals('Pan de Molde', $results[0]['nombre_producto']);

        // 4. Buscar sin coincidencias
        $results = $this->productModel->searchProducts('Cerveza');
        $this->assertCount(0, $results);
    }
}
