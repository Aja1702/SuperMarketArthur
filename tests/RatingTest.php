<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../models/Rating.php';

class RatingTest extends TestCase
{
    private $pdo;
    private $ratingModel;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Para los tests de Rating, necesitamos productos y usuarios a los que asociarlos.
        $this->pdo->exec("CREATE TABLE productos (id_producto INTEGER PRIMARY KEY, nombre_producto TEXT);");
        $this->pdo->exec("CREATE TABLE usuarios (id_usuario INTEGER PRIMARY KEY, nombre TEXT);");
        $this->pdo->exec("CREATE TABLE valoraciones (
            id_valoracion INTEGER PRIMARY KEY AUTOINCREMENT,
            id_producto INTEGER NOT NULL,
            id_usuario INTEGER NOT NULL,
            puntuacion INTEGER NOT NULL,
            comentario TEXT,
            fecha DATETIME NOT NULL
        );");

        $this->ratingModel = new Rating($this->pdo);
    }

    protected function tearDown(): void
    {
        $this->pdo = null;
        $this->ratingModel = null;
    }

    /**
     * Test para verificar que podemos crear una nueva valoración.
     */
    public function testCanCreateRating()
    {
        // 1. Preparar datos de prueba (un usuario y un producto)
        $this->pdo->exec("INSERT INTO usuarios (id_usuario, nombre) VALUES (1, 'Usuario de Prueba');");
        $this->pdo->exec("INSERT INTO productos (id_producto, nombre_producto) VALUES (10, 'Producto de Prueba');");

        // 2. Llamar al método a probar
        $result = $this->ratingModel->create(10, 1, 5, '¡Excelente producto!');

        // 3. Afirmar que la operación fue exitosa
        $this->assertTrue($result);

        // 4. Verificar que el dato está realmente en la base de datos
        $stmt = $this->pdo->query("SELECT * FROM valoraciones WHERE id_producto = 10");
        $rating = $stmt->fetch();

        $this->assertNotFalse($rating);
        $this->assertEquals(5, $rating['puntuacion']);
        $this->assertEquals('¡Excelente producto!', $rating['comentario']);
    }

    /**
     * Test para verificar que obtenemos las valoraciones de un producto.
     */
    public function testGetRatingsByProduct()
    {
        // 1. Preparar datos
        $this->pdo->exec("INSERT INTO usuarios (id_usuario, nombre) VALUES (1, 'Usuario 1'), (2, 'Usuario 2');");
        $this->pdo->exec("INSERT INTO productos (id_producto, nombre_producto) VALUES (11, 'Otro Producto');");
        $this->ratingModel->create(11, 1, 4, 'Muy bueno.');
        $this->ratingModel->create(11, 2, 2, 'No me convenció.');

        // 2. Llamar al método a probar
        $ratings = $this->ratingModel->getByProduct(11);

        // 3. Afirmar los resultados
        $this->assertCount(2, $ratings); // Esperamos obtener 2 valoraciones
        $this->assertEquals('Usuario 1', $ratings[0]['nombre_usuario']);
        $this->assertEquals(4, $ratings[0]['puntuacion']);
    }
}
