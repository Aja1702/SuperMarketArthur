<?php
/**
 * SCRIPT DE IMPORTACIÓN AUTOMÁTICA - SUPERMARKET ARTHUR
 * Este script lee el archivo TXT unificado e inserta categorías y productos en la BD.
 */

// 1. Incluimos la conexión a la base de datos
require_once __DIR__ . '/../config/iniciar_session.php';

// Verificamos si estamos en la base de datos correcta
try {
    $pdo->exec("USE supermarketarthur");
}
catch (Exception $e) {
    die("Error: No se pudo conectar a la base de datos 'supermarketarthur'. " . $e->getMessage());
}

// 2. Ruta del archivo a importar
$archivoPath = __DIR__ . '/../docs/listado_supermercado_total.txt';

if (!file_exists($archivoPath)) {
    die("Error: No se encuentra el archivo en $archivoPath");
}

echo "<h1>🚀 Iniciando Importación de SuperMarketArthur</h1>";
echo "<p>Leyendo archivo: <code>listado_supermercado_total.txt</code></p>";

// Limpiamos tablas previas para evitar duplicados
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0; TRUNCATE TABLE productos; TRUNCATE TABLE categorias; SET FOREIGN_KEY_CHECKS = 1;");

$lineas = file($archivoPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$id_categoria_actual = null;
$stats_cats = 0;
$stats_prods = 0;

foreach ($lineas as $linea) {
    // Saltamos la primera línea de cabecera si existe
    if (strpos($linea, '#') === 0)
        continue;

    // Detectamos si es una CATEGORÍA (Ej: "1. Frutas frescas:")
    if (preg_match('/^(\d+)\.\s*(.*?):?$/', trim($linea), $matches)) {
        $nombre_categoria = trim($matches[2]);
        $nombre_categoria = rtrim($nombre_categoria, ':');

        // Determinar IVA por categoría
        $iva = 10.00; // Por defecto 10% (Reducido)
        $nombre_cat_upper = mb_strtoupper($nombre_categoria, 'UTF-8');

        // IVA 4% (Superreducido: Básicos)
        $superreducido = ['FRUTAS', 'VERDURAS', 'PANADERÍA', 'HUEVOS', 'LECHE', 'ARROCES', 'LEGUMBRES', 'HARINAS'];
        foreach ($superreducido as $palabra) {
            if (str_contains($nombre_cat_upper, $palabra)) {
                $iva = 4.00;
                break;
            }
        }

        // IVA 21% (General: Limpieza, Alcohol, Mascotas, etc.)
        $general = ['CERVEZAS', 'VINOS', 'LICORES', 'LIMPIEZA', 'LAVAVAJILLAS', 'HIGIENE', 'COSMÉTICA', 'CUIDADO', 'PAPEL', 'PERROS', 'GATOS', 'ACCESORIOS', 'SALUD'];
        foreach ($general as $palabra) {
            if (str_contains($nombre_cat_upper, $palabra)) {
                $iva = 21.00;
                break;
            }
        }

        $iva_actual = $iva; // Guardamos para los productos de esta sección

        // Insertamos la categoría
        $stmt = $pdo->prepare("INSERT INTO categorias (nombre_categoria, descripcion) VALUES (?, ?)");
        $stmt->execute([$nombre_categoria, "Categoría de $nombre_categoria (IVA: $iva%)"]);
        $id_categoria_actual = $pdo->lastInsertId();

        $stats_cats++;
        echo "📂 Categoría añadida: <b>$nombre_categoria</b> (IVA: $iva%)<br>";
    }
    // Detectamos si es un PRODUCTO
    else if ($id_categoria_actual !== null) {
        $nombre_producto = trim($linea);

        if (!empty($nombre_producto)) {
            // Insertamos el producto con el IVA de su categoría
            $stmt = $pdo->prepare("INSERT INTO productos (nombre_producto, descripcion, precio, stock, id_categoria, url_imagen, tipo_iva) VALUES (?, ?, ?, ?, ?, ?, ?)");

            $descripcion = "Producto fresco de la sección de " . $nombre_producto;
            $precio = rand(150, 1500) / 100; // Precio entre 1.50 y 15.00
            $stock = 100;
            $imagen = "./assets/img/productos/default.jpg";

            $stmt->execute([$nombre_producto, $descripcion, $precio, $stock, $id_categoria_actual, $imagen, $iva_actual]);
            $stats_prods++;
        }
    }
}

echo "<h2>✅ Importación Finalizada</h2>";
echo "<ul>";
echo "<li>Categorías insertadas: $stats_cats</li>";
echo "<li>Productos insertados: $stats_prods</li>";
echo "</ul>";
echo "<p><a href='../index.php'>Ir a la tienda</a></p>";
