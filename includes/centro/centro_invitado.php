<h2>Bienvenido a Supermarket Arthur</h2>
<p>Explora nuestras ofertas y productos destacados.</p>

<div class="productos-destacados">
    <?php
    // Ejemplo sencillo: cargar productos con mysqli (conexion previa)
    $query = "SELECT * FROM productos LIMIT 6";
    $result = $conn->query($query);
    if ($result) {
        while ($prod = $result->fetch_assoc()) {
            echo "<div class='producto'>";
            echo "<img src='./img/productos/" . htmlspecialchars($prod['url_imagen']) . "' alt='" . htmlspecialchars($prod['nombre_producto']) . "'>";
            echo "<h3>" . htmlspecialchars($prod['nombre_producto']) . "</h3>";
            echo "<p>Precio: €" . number_format($prod['precio'], 2) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No hay productos destacados disponibles.</p>";
    }
    ?>
</div>