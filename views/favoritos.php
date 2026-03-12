<div class="container-info" id="favorites-page-container">
    <div class="card-info-premium">
        <h2 class="titulo-seccion-premium">Mis Productos Favoritos</h2>
        <p style="text-align: center; color: #64748b; margin-top: -1.5rem; margin-bottom: 3rem;">
            Tu lista personal de productos para tenerlos siempre a mano.
        </p>

        <?php if (empty($favoritos)): ?>
            <div class="empty-state" style="text-align: center; padding: 4rem 2rem; background: var(--bg-card); border-radius: 1rem; border: 1px dashed var(--border-color); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                <i class="far fa-heart" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                <h3 style="color: var(--text-color); font-size: 1.25rem; margin-bottom: 0.5rem;">Tu lista está vacía</h3>
                <p style="color: #64748b; margin-bottom: 1.5rem;">Aún no has añadido ningún producto a tu lista de favoritos. ¡Anímate a explorar!</p>
                <a href="/SuperMarketArthur/productos" class="btn-primary" style="text-decoration:none; padding: 0.75rem 2rem; display:inline-flex; align-items:center; gap: 0.5rem; border-radius: 0.5rem; font-weight: 500;">
                    <i class="fas fa-shopping-bag"></i> Explorar productos
                </a>
            </div>
        <?php else: ?>
            <div class="grid-productos">
                <?php foreach ($favoritos as $producto):
 ?>
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
                                <button class="btn-favorite js-toggle-favorite" data-id="<?php echo $producto['id_producto']; ?>" title="Quitar de favoritos">
                                    <i class="fas fa-heart"></i> <!-- En la lista de favoritos, siempre está relleno -->
                                </button>
                            </div>
                        </div>
                    </article>
                <?php endforeach;
 ?>
            </div>
        <?php endif;
 ?>
    </div>
</div>
