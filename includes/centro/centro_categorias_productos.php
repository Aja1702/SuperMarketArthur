<?php
$id_categoria = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;

if ($id_categoria > 0) {
    // Mostrar productos de la categoría seleccionada
    // Obtener nombre de la categoría para mostrar título
    $stmtCat = $pdo->prepare("SELECT nombre_categoria FROM categorias WHERE id_categoria = ?");
    $stmtCat->bindParam(1, $id_categoria, PDO::PARAM_INT);
    $stmtCat->execute();
    $resCat = $stmtCat->fetch(PDO::FETCH_ASSOC);

    if ($resCat) {
        $nombre_categoria = htmlspecialchars($resCat['nombre_categoria']);
    }
    else {
        $nombre_categoria = "Categoría no encontrada";
    }

    // Consultar productos de esa categoría con valoraciones
    $stmt = $pdo->prepare("SELECT p.*, AVG(v.puntuacion) as promedio_valoracion, COUNT(v.id_valoracion) as total_valoraciones FROM productos p LEFT JOIN valoraciones v ON p.id_producto = v.id_producto WHERE p.id_categoria = ? GROUP BY p.id_producto");
    $stmt->bindParam(1, $id_categoria, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <div class="seccion-catalogo">
        <a href="./?vistaMenu=categorias_productos" class="btn-volver-catalogo">
            <span class="icon">🔙</span> Volver al catálogo
        </a>
        
        <h2 class="titulo-seccion-premium">
            <?php echo $nombre_categoria; ?>
        </h2>

        <?php if (count($result) > 0): ?>
            <div class="grid-productos">
                <?php foreach ($result as $producto): ?>
                    <article class="card-producto">
                        <div class="producto-imagen-wrapper">
                            <img src="<?php echo htmlspecialchars($producto['url_imagen'] ?: './assets/img/productos/default.jpg'); ?>" 
                                 alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>"
                                 onerror="this.onerror=null;this.src='./assets/img/logo/logo_supermarket.png'">
                        </div>
                        <div class="producto-info">
                            <h3><?php echo htmlspecialchars($producto['nombre_producto']); ?></h3>
                            <p class="producto-desc"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                        </div>
                        <div class="producto-footer">
                            <div class="producto-precio">
                                <?php echo number_format($producto['precio'], 2, ',', '.'); ?><span>€</span>
                            </div>
                            <button onclick="addToCart(<?php echo $producto['id_producto']; ?>)" 
                                    class="btn-add-cart-premium" 
                                    title="Añadir al carrito">
                                🛒
                            </button>
                        </div>
                    </article>
                <?php
        endforeach; ?>
            </div>
        <?php
    else: ?>
            <div class="empty-state">
                <p>No hay productos disponibles en esta categoría actualmente.</p>
            </div>
        <?php
    endif; ?>
    </div>

<?php

}
else {
    // Mostrar listado de categorías
    $sql = "SELECT * FROM categorias";
    $rs = $pdo->query($sql);
?>
    <div class="seccion-catalogo">
        <h2 class="titulo-seccion-premium">
            Explora nuestro catálogo
        </h2>
        
        <div class="grid-categorias">
            <?php while ($row = $rs->fetch(PDO::FETCH_ASSOC)): ?>
                <a href="./?vistaMenu=categorias_productos&cat=<?php echo $row['id_categoria']; ?>" class="card-categoria">
                    <h3><?php echo htmlspecialchars($row['nombre_categoria']); ?></h3>
                    <p><?php echo htmlspecialchars($row['descripcion']); ?></p>
                </a>
            <?php
    endwhile; ?>
        </div>
    </div>
<?php

}

?>
