<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: /SuperMarketArthur/?vistaMenu=categorias_productos");
    exit();
}

$id_producto = (int)$_GET['id'];

include '../config/iniciar_session.php';
include '../models/products.php';
include '../models/Rating.php';

$productModel = new Product($pdo);
$ratingModel = new Rating($pdo);

$producto = $productModel->getProductById($id_producto);

if (!$producto) {
    header("Location: /SuperMarketArthur/?vistaMenu=categorias_productos");
    exit();
}

// Obtener valoraciones del producto
$valoraciones = $ratingModel->getRatingsByProduct($id_producto);
$promedioRating = $ratingModel->getAverageRating($id_producto);

// Verificar si el usuario ya ha valorado este producto
$usuarioHaValorado = false;
$valoracionUsuario = null;
if (isset($_SESSION['id_usuario'])) {
    $valoracionUsuario = $ratingModel->getUserRating($id_producto, $_SESSION['id_usuario']);
    $usuarioHaValorado = $valoracionUsuario !== null;
}

// Procesar nueva valoración
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['id_usuario'])) {
    if (isset($_POST['rating']) && is_numeric($_POST['rating'])) {
        $rating = (int)$_POST['rating'];
        $comment = trim($_POST['comment'] ?? '');

        if ($rating >= 1 && $rating <= 5) {
            $ratingModel->addRating($id_producto, $_SESSION['id_usuario'], $rating, $comment);
            header("Location: /SuperMarketArthur/?vistaMenu=detalle_producto&id=" . $id_producto);
            exit();
        }
    }
}
?>

<div class="seccion-detalle-producto">
    <a href="./?vistaMenu=categorias_productos" class="btn-volver">
        <span class="icon">🔙</span> Volver al catálogo
    </a>

    <div class="detalle-producto-grid">
        <!-- Imagen del producto -->
        <div class="producto-imagen-detalle">
            <img src="<?php echo htmlspecialchars($producto['url_imagen'] ?: './assets/img/productos/default.jpg'); ?>"
                 alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>"
                 onerror="this.onerror=null;this.src='./assets/img/logo/logo_supermarket.png'">
        </div>

        <!-- Información del producto -->
        <div class="producto-info-detalle">
            <h1><?php echo htmlspecialchars($producto['nombre_producto']); ?></h1>
            <p class="producto-descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></p>

            <!-- Valoración promedio -->
            <div class="rating-promedio">
                <div class="stars">
                    <?php
                    $rating = round($promedioRating['promedio']);
                    for ($i = 1; $i <= 5; $i++) {
                        echo $i <= $rating ? '★' : '☆';
                    }
                    ?>
                </div>
                <span class="rating-text">
                    <?php echo number_format($promedioRating['promedio'], 1); ?> de 5
                    (<?php echo $promedioRating['total']; ?> valoraciones)
                </span>
            </div>

            <div class="producto-precio-detalle">
                <span class="precio"><?php echo number_format($producto['precio'], 2, ',', '.'); ?>€</span>
                <span class="stock">Stock: <?php echo $producto['stock']; ?> unidades</span>
            </div>

            <button onclick="addToCart(<?php echo $producto['id_producto']; ?>)" class="btn-add-cart-detalle">
                🛒 Añadir al carrito
            </button>
        </div>
    </div>

    <!-- Sección de valoraciones -->
    <div class="seccion-valoraciones">
        <h2>Valoraciones de clientes</h2>

        <?php if (isset($_SESSION['id_usuario'])): ?>
            <!-- Formulario para añadir valoración -->
            <div class="form-valoracion">
                <h3><?php echo $usuarioHaValorado ? 'Editar tu valoración' : 'Añadir valoración'; ?></h3>
                <form method="POST" action="">
                    <div class="rating-input">
                        <label>Tu valoración:</label>
                        <div class="stars-input">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>"
                                       <?php echo ($usuarioHaValorado && $valoracionUsuario['puntuacion'] == $i) ? 'checked' : ''; ?> required>
                                <label for="star<?php echo $i; ?>">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="comment-input">
                        <label for="comment">Comentario (opcional):</label>
                        <textarea id="comment" name="comment" rows="3" maxlength="500"
                                  placeholder="Comparte tu experiencia con este producto..."><?php echo $usuarioHaValorado ? htmlspecialchars($valoracionUsuario['comentario']) : ''; ?></textarea>
                    </div>

                    <button type="submit" class="btn-submit-rating">
                        <?php echo $usuarioHaValorado ? 'Actualizar valoración' : 'Enviar valoración'; ?>
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="login-required">
                <p>¿Has comprado este producto? <a href="./?userSession=login">Inicia sesión</a> para dejar tu valoración.</p>
            </div>
        <?php endif; ?>

        <!-- Lista de valoraciones -->
        <div class="lista-valoraciones">
            <?php if (count($valoraciones) > 0): ?>
                <?php foreach ($valoraciones as $valoracion): ?>
                    <div class="valoracion-item">
                        <div class="valoracion-header">
                            <div class="usuario-info">
                                <span class="usuario-nombre">
                                    <?php echo htmlspecialchars($valoracion['nombre'] . ' ' . $valoracion['apellido1']); ?>
                                </span>
                                <span class="valoracion-fecha">
                                    <?php echo date('d/m/Y', strtotime($valoracion['fecha'])); ?>
                                </span>
                            </div>
                            <div class="valoracion-stars">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $valoracion['puntuacion'] ? '★' : '☆';
                                }
                                ?>
                            </div>
                        </div>
                        <?php if (!empty($valoracion['comentario'])): ?>
                            <div class="valoracion-comentario">
                                <?php echo htmlspecialchars($valoracion['comentario']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-valoraciones">
                    <p>Aún no hay valoraciones para este producto. ¡Sé el primero en opinar!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.seccion-detalle-producto {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.btn-volver {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    text-decoration: none;
    color: #495057;
    margin-bottom: 2rem;
    transition: all 0.3s ease;
}

.btn-volver:hover {
    background: #e9ecef;
    color: #212529;
}

.detalle-producto-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    margin-bottom: 3rem;
}

.producto-imagen-detalle img {
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.producto-info-detalle h1 {
    font-size: 2.5rem;
    color: #2d3748;
    margin-bottom: 1rem;
}

.producto-descripcion {
    font-size: 1.1rem;
    color: #4a5568;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.rating-promedio {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.rating-promedio .stars {
    font-size: 1.5rem;
    color: #fbbf24;
}

.rating-text {
    color: #718096;
    font-weight: 500;
}

.producto-precio-detalle {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f7fafc;
    border-radius: 8px;
}

.producto-precio-detalle .precio {
    font-size: 2rem;
    font-weight: bold;
    color: #2d3748;
}

.producto-precio-detalle .stock {
    color: #38a169;
    font-weight: 500;
}

.btn-add-cart-detalle {
    width: 100%;
    padding: 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.btn-add-cart-detalle:hover {
    transform: translateY(-2px);
}

.seccion-valoraciones {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid #e2e8f0;
}

.seccion-valoraciones h2 {
    font-size: 2rem;
    color: #2d3748;
    margin-bottom: 2rem;
}

.form-valoracion {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
}

.form-valoracion h3 {
    color: #2d3748;
    margin-bottom: 1.5rem;
}

.rating-input {
    margin-bottom: 1.5rem;
}

.rating-input label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #4a5568;
}

.stars-input {
    display: flex;
    gap: 0.25rem;
}

.stars-input input[type="radio"] {
    display: none;
}

.stars-input label {
    font-size: 2rem;
    color: #e2e8f0;
    cursor: pointer;
    transition: color 0.2s ease;
}

.stars-input input[type="radio"]:checked ~ label,
.stars-input label:hover,
.stars-input label:hover ~ label {
    color: #fbbf24;
}

.comment-input label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #4a5568;
}

.comment-input textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-family: inherit;
    resize: vertical;
}

.btn-submit-rating {
    padding: 0.75rem 2rem;
    background: #4299e1;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s ease;
}

.btn-submit-rating:hover {
    background: #3182ce;
}

.login-required {
    text-align: center;
    padding: 2rem;
    background: #fef5e7;
    border: 1px solid #f6e05e;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.login-required a {
    color: #4299e1;
    text-decoration: none;
    font-weight: 500;
}

.login-required a:hover {
    text-decoration: underline;
}

.lista-valoraciones {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.valoracion-item {
    padding: 1.5rem;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.valoracion-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.usuario-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.usuario-nombre {
    font-weight: 600;
    color: #2d3748;
}

.valoracion-fecha {
    font-size: 0.875rem;
    color: #718096;
}

.valoracion-stars {
    font-size: 1.25rem;
    color: #fbbf24;
}

.valoracion-comentario {
    color: #4a5568;
    line-height: 1.6;
}

.no-valoraciones {
    text-align: center;
    padding: 3rem;
    color: #718096;
}

@media (max-width: 768px) {
    .detalle-producto-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    .producto-info-detalle h1 {
        font-size: 2rem;
    }

    .valoracion-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>
