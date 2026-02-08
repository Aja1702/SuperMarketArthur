<main class="centro-invitado" role="main" aria-label="Contenido principal para usuarios invitados">
    <section class="banner-principal">
        <div class="banner-texto">
            <h2>Bienvenido a SuperMarketArthur</h2>
            <p>Los mejores productos frescos y ofertas exclusivas solo para ti.</p>
            <a href="./?userSession=registro" class="btn btn-registro-destacado">Regístrate ahora</a>
        </div>
    </section>

    <?php
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

    <section class="seccion-catalogo" style="padding-top: 4rem;">
        <h2 class="titulo-seccion-premium">Productos Destacados</h2>
        <div class="grid-productos">
            <?php foreach ($productosDestacados as $producto): ?>
                <article class="card-producto">
                    <div class="producto-imagen-wrapper">
                        <img src="<?php echo htmlspecialchars($producto['url_imagen'] ?: './assets/img/productos/default.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>"
                             onerror="this.onerror=null;this.src='./assets/img/logo/logo_supermarket.png'">
                    </div>
                    <div class="producto-info">
                        <h3><?php echo htmlspecialchars($producto['nombre_producto']); ?></h3>
                        <p class="producto-desc">Calidad premium garantizada directamente en tu mesa.</p>
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
    </section>

    <section class="porque-elegirnos-premium" style="padding-bottom: 4rem;">
        <h2 class="titulo-seccion-premium">¿Por qué elegir SuperMarketArthur?</h2>
        <div class="grid-ventajas" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin: 2rem 5% 0 5%;">
            <div class="ventaja-card" style="background: white; padding: 2.5rem; border-radius: 20px; box-shadow: var(--sombra-suave); text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🌟</div>
                <h4 style="color: var(--azul-primario); margin-bottom: 1rem;">Calidad Superior</h4>
                <p style="color: #64748b; font-size: 0.9rem;">Seleccionamos personalmente cada producto para asegurar que solo lo mejor llegue a tu hogar.</p>
            </div>
            <div class="ventaja-card" style="background: white; padding: 2.5rem; border-radius: 20px; box-shadow: var(--sombra-suave); text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🚚</div>
                <h4 style="color: var(--azul-primario); margin-bottom: 1rem;">Envío Express</h4>
                <p style="color: #64748b; font-size: 0.9rem;">Entregamos tus pedidos en tiempo récord para que disfrutes de la frescura máxima.</p>
            </div>
            <div class="ventaja-card" style="background: white; padding: 2.5rem; border-radius: 20px; box-shadow: var(--sombra-suave); text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🤝</div>
                <h4 style="color: var(--azul-primario); margin-bottom: 1rem;">Atención Cercana</h4>
                <p style="color: #64748b; font-size: 0.9rem;">Estamos aquí para ayudarte en cada paso. Tu satisfacción es nuestra prioridad absoluta.</p>
            </div>
        </div>
    </section>
</main>