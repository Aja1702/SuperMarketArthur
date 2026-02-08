<?php
// En un sistema real, buscaríamos productos con campo 'descuento' o similar
// Para este ejemplo, mostraremos los 8 productos más recientes como "Ofertas Especiales"
$stmt = $pdo->query("SELECT * FROM productos ORDER BY id_producto DESC LIMIT 8");
$ofertas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="seccion-catalogo">
    <div style="text-align: center; margin-bottom: 4rem;">
        <h2 class="titulo-seccion-premium">Ofertas Irresistibles</h2>
        <p style="color: #64748b; font-size: 1.1rem;">Ahorra en tus compras diarias con nuestros descuentos exclusivos</p>
    </div>

    <div class="grid-productos">
        <?php foreach ($ofertas as $producto): ?>
            <article class="card-producto">
                <div class="producto-imagen-wrapper">
                    <span style="position: absolute; top: 1rem; right: 1rem; background: #f43f5e; color: white; padding: 0.4rem 0.8rem; border-radius: 50px; font-weight: 800; font-size: 0.8rem; z-index: 10;">
                        -15%
                    </span>
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
                        <span style="text-decoration: line-through; color: #94a3b8; font-size: 0.9rem; margin-right: 0.5rem;">
                            <?php echo number_format($producto['precio'] * 1.15, 2, ',', '.'); ?>€
                        </span><br>
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
</div>