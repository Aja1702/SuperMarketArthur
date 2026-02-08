<?php
include('./config/iniciar_session.php');

$id_categoria = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;

if ($id_categoria > 0) {
    // Mostrar productos de la categoría seleccionada
    // Obtener nombre de la categoría para mostrar título
    $stmtCat = $pdo->prepare("SELECT nombre_categoria FROM categorias WHERE id_categoria = ?");
    $stmtCat->bindParam(1, $id_categoria, PDO::PARAM_INT);
    $stmtCat->execute();
    $resCat = $stmtCat->fetch(PDO::FETCH_ASSOC);

    if ($resCat) {
        $categoria = $resCat;
        $nombre_categoria = htmlspecialchars($categoria['nombre_categoria']);
    }
    else {
        $nombre_categoria = "Categoría no encontrada";
    }

    // Consultar productos de esa categoría
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id_categoria = ?");
    $stmt->bindParam(1, $id_categoria, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <div class="productos-de-categoria">
        <h2>
            Productos de la categoría:
            <?php
    echo $nombre_categoria;
?>
        </h2>
        <?php
    if (count($result) > 0) {
?>
            <div class="lista-productos">
                <?php
        foreach ($result as $producto):
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
                        <button onclick="addToCart(<?php echo $producto['id_producto']; ?>)" class="btn btn-add-cart">
                            🛒 Añadir
                        </button>
                    </article>
                <?php
        endforeach;
?>
            </div>
        <?php
    }
    else {
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
}
else {
    // Mostrar listado de categorías (como en tu código original)
    $sql = "SELECT * FROM categorias";
    $rs = $pdo->query($sql);
?>
    <div class="categorias-productos">
        <h2>
            Catálogo de categorías de productos:
        </h2>
        <div class="lista-categorias">
            <?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)): ?>
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
            <?php
    endwhile; ?>
        </div>
    </div>
<?php
}
?>