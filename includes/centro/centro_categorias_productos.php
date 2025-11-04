<?php
include './config/iniciar_session.php';

$sql = "SELECT id, nombre_categoria FROM categorias ORDER BY nombre_categoria";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$categorias = $stmt->fetchAll();

foreach ($categorias as $categoria) {
    echo "<div>";
    echo "<h3>" . htmlspecialchars($categoria['nombre_categoria']) . "</h3>";
    // Aquí podrías enlazar hacia los productos de esa categoría, por ejemplo:
    echo "<a href='productos_categoria.php?id=" . intval($categoria['id']) . "'>Ver productos</a>";
    echo "</div>";
}
