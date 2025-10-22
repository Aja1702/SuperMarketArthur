<?php
include('./config/iniciar_session.php');

$id_categoria = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;

if ($id_categoria > 0) {
    // Mostrar productos de la categoría seleccionada
    // Obtener nombre de la categoría para mostrar título
    $stmtCat = $conn->prepare("SELECT nombre_categoria FROM categorias WHERE id_categoria = ?");
    $stmtCat->bind_param("i", $id_categoria);
    $stmtCat->execute();
    $resCat = $stmtCat->get_result();

    if ($resCat->num_rows > 0) {
        $categoria = $resCat->fetch_assoc();
        $nombre_categoria = htmlspecialchars($categoria['nombre_categoria']);
    } else {
        $nombre_categoria = "Categoría no encontrada";
    }

    // Consultar productos de esa categoría
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id_categoria = ?");
    $stmt->bind_param("i", $id_categoria);
    $stmt->execute();
    $result = $stmt->get_result();
?>

    <div class="productos-de-categoria">
        <h2>
            Productos de la categoría:
            <?php
            echo $nombre_categoria;
            ?>
        </h2>
        <?php
        if ($result->num_rows > 0) {
        ?>
            <div class="lista-productos">
                <?php
                while ($producto = $result->fetch_assoc()):
                ?>
                    <article class="producto">
                        <img src="<?php echo htmlspecialchars($producto['url_imagen']); ?>" alt="imagen de <?php echo htmlspecialchars($producto['nombre_producto']); ?>">
                        <h3>
                            <?php echo htmlspecialchars($producto['nombre_producto']); ?>
                        </h3>
                        <p>
                            <?php echo htmlspecialchars($producto['descripcion']); ?>
                        </p>
                        <p>Precio:
                            <?php echo number_format($producto['precio'], 2); ?>
                            €
                        </p>
                        <!-- Aquí puedes añadir más detalles o botones -->
                    </article>
                <?php
                endwhile;
                ?>
            </div>
        <?php
        } else {
        ?>
        <p>No hay productos en esta categoría.</p>
        <?php
        }
        ?>
        <p>
            <a href="./?vistaMenu=categorias_productos">← Volver a categorías</a>
        </p>
    </div>
<?php
} else {
    // Mostrar listado de categorías (como en tu código original)
    $sql = "SELECT * FROM categorias";
    $rs = $conn->query($sql);
?>
    <div class="categorias-productos">
        <h2>
            Catálogo de categorías de productos:
        </h2>
        <div class="lista-categorias">
            <?php while ($row = $rs->fetch_assoc()): ?>
                <article class="categoria">
                    <a href="./?vistaMenu=categorias_productos&cat=<?php echo $row['id_categoria']; ?>" style="text-decoration: none; color: inherit;">
                        <h3>
                            <?php echo htmlspecialchars($row['nombre_categoria']); ?>
                        </h3>
                        <p>
                            <?php echo htmlspecialchars($row['descripcion']); ?>
                        </p>
                    </a>
                </article>
            <?php endwhile; ?>
        </div>
    </div>
<?php
}
?>