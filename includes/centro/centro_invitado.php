<main class="centro-invitado" role="main" aria-label="Contenido principal para usuarios invitados">
    <section class="banner-principal">
        <div class="banner-texto">
            <h2>Bienvenido a SuperMarketArthur</h2>
            <p>Los mejores productos frescos y ofertas exclusivas solo para ti.</p>
            <a href="./?userSession=registro" class="btn btn-registro-destacado">Regístrate ahora</a>
        </div>
    </section>



    <?php
    // Conexión (desde tu iniciar_session.php o similar)
    require 'config/iniciar_session.php';

    // Categorías deseadas
    $categorias = ['alimentos', 'bebida', 'pan', 'frutos secos', 'postres', 'comida animal', 'hogar'];

    // Preparar placeholders para IN
    $placeholders = rtrim(str_repeat('?,', count($categorias)), ',');

    // Consulta SQL para obtener un producto por categoría
    $sql = "
            SELECT p.id_producto, p.nombre_producto, p.precio, p.url_imagen, c.nombre_categoria 
            AS categoria
            FROM productos p
            JOIN categorias c 
            ON p.categoria_id = c.id
            WHERE c.nombre_categoria IN ($placeholders)
            GROUP BY p.id_categoria
            LIMIT 7
        ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($categorias);
    $productosDestacados = $stmt->fetchAll();

    ?>

    <section class="destacados">
        <h3>Productos Destacados</h3>
        <div class="productos-lista">
            <?php foreach ($productosDestacados as $producto): ?>
                <article class="producto">
                    <img src="./img/productos/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" />
                    <h4><?php echo htmlspecialchars($producto['nombre']); ?></h4>
                    <p class="precio">€<?php echo number_format($producto['precio'], 2, ',', '.'); ?> / unidad</p>
                    <a href="./?vistaMenu=categorias_productos&categoria=<?php echo urlencode($producto['categoria']); ?>" class="btn btn-ver-mas">Ver más</a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>


















    <section class="porque-elegirnos">
        <h3>¿Por qué elegir SuperMarketArthur?</h3>
        <ul>
            <li>Calidad garantizada en cada producto</li>
            <li>Envíos rápidos y seguros</li>
            <li>Ofertas exclusivas para miembros registrados</li>
        </ul>
    </section>
</main>