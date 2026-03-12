<?php
// Generar token CSRF para el formulario de valoración
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['csrf_token'];

// Función para generar las estrellas (podría moverse a un helper global)
function render_stars($rating) {
    $stars = '';
    $rating_int = floor($rating);
    for ($i = 0; $i < 5; $i++) {
        if ($i < $rating_int) {
            $stars .= '<i class="fas fa-star"></i>';
        } else {
            $stars .= '<i class="far fa-star"></i>';
        }
    }
    return $stars;
}
?>

<div class="container-detalle-producto">
    <a href="#" class="js-back-button btn-volver">⬅️ Volver</a>

    <div class="producto-detalle-grid">
        <div class="producto-imagen-container">
            <img src="<?php echo htmlspecialchars($producto['url_imagen']); ?>" 
                 alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>"
                 loading="lazy">
        </div>
        <div class="producto-info-container">
            <h2><?php echo htmlspecialchars($producto['nombre_producto']); ?></h2>

            <?php if ($valoraciones_habilitadas): ?>
                <div class="valoracion-summary">
                    <div class="stars-display"><?php echo render_stars($producto['rating_average']); ?></div>
                    <span class="ml-2">(<?php echo htmlspecialchars($producto['rating_total']); ?> valoraciones)</span>
                </div>
            <?php endif; ?>

            <p class="producto-descripcion"><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>

            <div class="producto-precio-detalle">
                <span><?php echo number_format($producto['precio'], 2, ',', '.'); ?><?php echo htmlspecialchars($simbolo_moneda); ?></span>
                <small class="iva-incl" style="font-size: 0.9rem; color: #64748b; font-weight: 500;">(IVA incluido)</small>
            </div>

            <div class="producto-acciones" style="display: flex; gap: 1rem; align-items: center;">
                <button class="btn btn-primary btn-add-to-cart"
                        data-id="<?php echo $producto['id_producto']; ?>"
                        onclick="addToCart(<?php echo $producto['id_producto']; ?>)">
                    <i class="fas fa-shopping-cart mr-2"></i> Añadir al carrito
                </button>
                <button class="btn-favorite js-toggle-favorite" data-id="<?php echo $producto['id_producto']; ?>" title="Añadir a favoritos">
                    <i class="<?php echo (isset($producto['is_favorite']) && $producto['is_favorite']) ? 'fas fa-heart' : 'far fa-heart'; ?>"></i>
                </button>
            </div>
        </div>
    </div>

    <?php if ($valoraciones_habilitadas): ?>
        <!-- ... (resto del código de valoraciones) ... -->
    <?php endif; ?>
</div>
