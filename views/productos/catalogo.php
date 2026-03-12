<?php if ($id_categoria > 0): ?>

    <div class="seccion-catalogo">
        <a href="/SuperMarketArthur/productos" class="btn-volver">⬅️ Volver al catálogo</a>

        <h2 class="titulo-seccion-premium">
            <?php echo htmlspecialchars($nombre_categoria); ?>
        </h2>

        <?php if (count($productos) > 0): ?>
            <div class="grid-productos">
                <?php foreach ($productos as $producto): ?>
                    <article class="card-producto">
                        <div class="producto-imagen-wrapper">
                            <a href="/SuperMarketArthur/producto?id=<?php echo $producto['id_producto']; ?>">
                                <img src="<?php echo htmlspecialchars($producto['url_imagen'] ?: './assets/img/productos/default.jpg'); ?>"
                                     alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>"
                                     loading="lazy"
                                     onerror="this.onerror=null;this.src='./assets/img/logo/logo_supermarket.png'">
                            </a>
                        </div>
                        <div class="producto-info">
                            <h3><?php echo htmlspecialchars($producto['nombre_producto']); ?></h3>
                            <p class="producto-desc"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                        </div>
                        <div class="producto-footer">
                            <div class="producto-precio">
                                <?php echo number_format($producto['precio'], 2, ',', '.'); ?><span><?php echo htmlspecialchars($simbolo_moneda); ?></span>
                                <span class="iva-incl">IVA incl.</span>
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

            <!-- PAGINACIÓN -->
            <!-- ... (código de paginación) ... -->

        <?php else: ?>
            <div class="empty-state">
                <p>No hay productos disponibles en esta categoría actualmente.</p>
            </div>
        <?php endif; ?>
    </div>

<?php else: ?>

    <div class="seccion-catalogo">
        <a href="/SuperMarketArthur/" class="btn-volver">⬅️ Volver al inicio</a>

        <h2 class="titulo-seccion-premium">
            Explora nuestro catálogo
        </h2>

        <div class="grid-categorias">
            <?php foreach ($categorias as $categoria): ?>
                <a href="/SuperMarketArthur/productos?cat=<?php echo $categoria['id_categoria']; ?>" class="card-categoria">
                    <h3><?php echo htmlspecialchars($categoria['nombre_categoria']); ?></h3>
                    <p><?php echo htmlspecialchars($categoria['descripcion']); ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

<?php endif; ?>
