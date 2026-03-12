<main class="centro-<?php echo htmlspecialchars($tipo_usuario); ?>" role="main" aria-label="Contenido principal para <?php echo htmlspecialchars($tipo_usuario); ?>">
    <section class="banner-<?php echo htmlspecialchars($tipo_usuario); ?>">
        <div class="banner-texto">
            <h2>Bienvenido a SuperMarketArthur</h2>
            <p>Los mejores productos frescos y ofertas exclusivas solo para ti.</p>
            <a href="/SuperMarketArthur/registro" class="btn btn-registro-destacado">Regístrate ahora</a>
        </div>
    </section>

    <section class="seccion-catalogo" style="padding-top: 4rem;">
        <h2 class="titulo-seccion-premium">Productos Destacados</h2>
        <div class="grid-productos">
            <?php foreach ($productosDestacados as $producto): ?>
                <article class="card-producto">
                    <div class="producto-imagen-wrapper">
                        <a href="/SuperMarketArthur/producto?id=<?php echo $producto['id_producto']; ?>">
                             <img src="<?php echo htmlspecialchars($producto['url_imagen'] ?: './assets/img/productos/default.jpg'); ?>"
                                 alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>"
                                 onerror="this.onerror=null;this.src='./assets/img/logo/logo_supermarket.png'">
                        </a>
                    </div>
                    <div class="producto-info">
                        <h3><?php echo htmlspecialchars($producto['nombre_producto']); ?></h3>
                        <p class="producto-desc">Calidad premium garantizada directamente en tu mesa.</p>
                    </div>
                    <div class="producto-footer">
                        <div class="producto-precio">
                            <?php echo number_format($producto['precio'], 2, ',', '.'); ?><span><?php echo htmlspecialchars($simbolo_moneda); ?></span>
                        </div>
                        <div class="producto-card-actions" style="display:flex; gap: 0.5rem;">
                            <button class="btn-add-cart-premium" onclick="addToCart(<?php echo $producto['id_producto']; ?>)" title="Añadir al carrito">🛒</button>
                            <button class="btn-favorite js-toggle-favorite" data-id="<?php echo $producto['id_producto']; ?>" title="Añadir a favoritos">
                                <i class="<?php echo (isset($producto['is_favorite']) && $producto['is_favorite']) ? 'fas fa-heart' : 'far fa-heart'; ?>"></i>
                            </button>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="porque-elegirnos-premium" style="padding-bottom: 4rem;">
        <!-- ... (código de ventajas) ... -->
    </section>
</main>
