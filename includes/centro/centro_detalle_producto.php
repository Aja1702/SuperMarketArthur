<?php
require_once __DIR__ . '../../../models/Product.php';
require_once __DIR__ . '../../../models/Rating.php';

// 1. Obtener el ID del producto de la URL
$id_producto = $_GET['id_producto'] ?? null;

if (!$id_producto) {
    echo "<p>Producto no encontrado.</p>";
    return;
}

// 2. Obtener los detalles del producto
$productModel = new Product($pdo);
$producto = $productModel->getProductById($id_producto);

if (!$producto) {
    echo "<p>Producto no encontrado.</p>";
    return;
}

// 3. Obtener las valoraciones de este producto
$ratingModel = new Rating($pdo);
$valoraciones = $ratingModel->getByProduct($id_producto);

// Generar token CSRF para el formulario de valoración
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['csrf_token'];

// Función para generar las estrellas
function render_stars($rating) {
    $stars = '';
    $rating_int = floor($rating);
    for ($i = 0; $i < 5; $i++) {
        if ($i < $rating_int) {
            $stars .= '<i class="fas fa-star"></i>'; // Estrella llena
        } else {
            $stars .= '<i class="far fa-star"></i>'; // Estrella vacía
        }
    }
    return $stars;
}
?>

<div class="container-detalle-producto">
    <div class="producto-detalle-grid">
        <div class="producto-imagen-container">
            <img src="<?php echo htmlspecialchars($producto['url_imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">
        </div>
        <div class="producto-info-container">
            <h2><?php echo htmlspecialchars($producto['nombre_producto']); ?></h2>

            <!-- Sección de Valoraciones -->
            <div class="valoracion-summary">
                <div class="stars-display"><?php echo render_stars($producto['rating_average']); ?></div>
                <span class="ml-2">(<?php echo htmlspecialchars($producto['rating_total']); ?> valoraciones)</span>
            </div>

            <p class="producto-descripcion"><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>

            <div class="producto-precio-detalle">
                <span><?php echo number_format($producto['precio'], 2, ',', '.'); ?>€</span>
            </div>

            <div class="producto-acciones">
                <button class="btn btn-primary btn-add-to-cart" data-id="<?php echo $producto['id_producto']; ?>">
                    <i class="fas fa-shopping-cart mr-2"></i> Añadir al carrito
                </button>
            </div>
        </div>
    </div>

    <!-- Sección de Comentarios y Formulario para valorar -->
    <div class="valoraciones-seccion">
        <h3>Opiniones de nuestros clientes</h3>

        <!-- Formulario para dejar una valoración (solo para usuarios logueados) -->
        <?php if (isset($_SESSION['id_usuario'])): ?>
            <div class="valoracion-form-container">
                <h4>Deja tu valoración</h4>
                <form action="controllers/procesar_valoracion.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
                    <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">

                    <div class="form-group">
                        <label>Puntuación</label>
                        <div class="star-rating">
                            <input type="radio" id="5-stars" name="puntuacion" value="5" /><label for="5-stars">&#9733;</label>
                            <input type="radio" id="4-stars" name="puntuacion" value="4" /><label for="4-stars">&#9733;</label>
                            <input type="radio" id="3-stars" name="puntuacion" value="3" /><label for="3-stars">&#9733;</label>
                            <input type="radio" id="2-stars" name="puntuacion" value="2" /><label for="2-stars">&#9733;</label>
                            <input type="radio" id="1-star" name="puntuacion" value="1" /><label for="1-star">&#9733;</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="comentario">Comentario</label>
                        <textarea name="comentario" class="form-control" rows="3" placeholder="Escribe tu opinión sobre el producto..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar valoración</button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Lista de valoraciones existentes -->
        <div class="lista-valoraciones">
            <?php if (empty($valoraciones)): ?>
                <p>Todavía no hay valoraciones para este producto. ¡Sé el primero!</p>
            <?php else: ?>
                <?php foreach ($valoraciones as $valoracion): ?>
                    <div class="valoracion-item">
                        <div class="valoracion-header">
                            <strong><?php echo htmlspecialchars($valoracion['nombre_usuario']); ?></strong>
                            <div class="stars-display ml-3"><?php echo render_stars($valoracion['puntuacion']); ?></div>
                        </div>
                        <p class="comentario-texto"><?php echo nl2br(htmlspecialchars($valoracion['comentario'])); ?></p>
                        <small class="text-muted"><?php echo date('d/m/Y', strtotime($valoracion['fecha'])); ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
