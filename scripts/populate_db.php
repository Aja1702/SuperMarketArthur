<?php
require_once '../config/iniciar_session.php';

try {
    // Insertar categorías si no existen
    $pdo->exec("INSERT IGNORE INTO categorias (id_categoria, nombre_categoria, descripcion) VALUES
        (1, 'Frutas y Verduras', 'Productos frescos y naturales'),
        (2, 'Lácteos', 'Leche, queso, yogur y derivados'),
        (3, 'Carnes', 'Carnes frescas y procesadas'),
        (4, 'Panadería', 'Pan, bollería y repostería')");

    // Insertar productos de prueba
    $pdo->exec("INSERT IGNORE INTO productos (id_producto, nombre_producto, descripcion, precio, stock, id_categoria, url_imagen) VALUES
        (1, 'Manzana Roja', 'Manzanas frescas de temporada', 1.50, 100, 1, './assets/img/productos/manzana.webp'),
        (2, 'Plátano', 'Plátanos maduros y dulces', 0.80, 150, 1, './assets/img/productos/platanos.webp'),
        (3, 'Leche Entera', 'Leche fresca pasteurizada', 1.20, 50, 2, './assets/img/productos/default.jpg'),
        (4, 'Queso Cheddar', 'Queso cheddar curado', 4.50, 30, 2, './assets/img/productos/default.jpg'),
        (5, 'Pollo Entero', 'Pollo fresco de corral', 6.90, 20, 3, './assets/img/productos/default.jpg'),
        (6, 'Pan de Molde', 'Pan de molde integral', 2.10, 40, 4, './assets/img/productos/default.jpg')");

    echo "✅ Productos de prueba añadidos correctamente\n";
    echo "Ahora puedes probar el carrito añadiendo productos desde el catálogo.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
