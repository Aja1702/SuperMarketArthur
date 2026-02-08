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
include './config/iniciar_session.php';

// Categorías deseadas (coincidiendo con los nombres reales en BD)
$categorias = ['Frutas frescas', 'Verduras y hortalizas', 'Panadería artesanal', 'Snacks salados', 'Helados y postres congelados', 'Alimentación para gatos', 'Higiene y cuidado'];

// Preparar placeholders para IN
$placeholders = rtrim(str_repeat('?,', count($categorias)), ',');

// Consulta SQL para obtener un producto por categoría
$sql = "
        SELECT p.id_producto, p.nombre_producto, p.precio, p.url_imagen, c.nombre_categoria 
        AS categoria
        FROM productos p
        JOIN categorias c ON p.id_categoria = c.id_categoria
        JOIN (
            SELECT id_categoria, MIN(id_producto) AS min_producto_id
            FROM productos
            GROUP BY id_categoria
            ) mp 
        ON p.id_categoria = mp.id_categoria AND p.id_producto = mp.min_producto_id
        WHERE c.nombre_categoria IN ($placeholders)
        ORDER BY p.nombre_producto ASC;
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
                    <img src="<?php echo htmlspecialchars($producto['url_imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>" />
                    <h4>
                        <?php echo htmlspecialchars($producto['nombre_producto']); ?>
                    </h4>
                    <p class="precio">
                        €<?php echo number_format($producto['precio'], 2, ',', '.'); ?> / unidad
                    </p>
                    <a href="./?vistaMenu=categorias_productos&categoria=<?php echo urlencode($producto['categoria']); ?>" class="btn btn-ver-mas">
                        Ver más
                    </a>
                </article>
            <?php
endforeach; ?>
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