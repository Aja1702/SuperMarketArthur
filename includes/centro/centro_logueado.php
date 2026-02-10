<?php
// Incluir los modelos necesarios
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/Order.php';

// Crear instancias de los modelos
$productModel = new Product($pdo);
$orderModel = new Order($pdo);

// Obtener el ID y nombre del usuario de la sesión
$userId = $_SESSION['id_usuario'] ?? 0;
$nombre_usuario = $_SESSION['usuario_nombre'] ?? 'Usuario';

// Obtener los últimos 3 pedidos del usuario
$ultimosPedidos = $orderModel->getUserOrders($userId, 3);

// Obtener los productos destacados (usando la caché)
$productosDestacados = $productModel->getFeaturedProducts(10);

?>
<main class="centro-logueado" role="main" aria-label="Contenido principal para usuarios logueados">

    <section class="dashboard-header" style="padding: 2rem 5%; text-align: center;">
        <h2 class="titulo-seccion-premium">¡Hola de nuevo, <?php echo htmlspecialchars($nombre_usuario); ?>!</h2>
        <p style="font-size: 1.2rem; color: #64748b;">Qué bueno verte por aquí. Aquí tienes un resumen de tu actividad.</p>
    </section>

    <section class="ultimos-pedidos-seccion" style="padding: 2rem 5%; max-width: 1200px; margin: 0 auto;">
        <h3 style="font-size: 1.8rem; color: var(--azul-primario); margin-bottom: 2rem;">Tus Últimos Pedidos</h3>
        <?php if (empty($ultimosPedidos)): ?>
            <div class="empty-state" style="background: white; padding: 3rem; border-radius: 12px; text-align: center; opacity: 0.7;">
                <p>Aún no has realizado ningún pedido. ¡Anímate a explorar nuestros productos!</p>
                <a href="./?vistaMenu=categorias_productos" class="btn-form-registro" style="margin-top: 1rem; display: inline-block; width: auto;">Ver Productos</a>
            </div>
        <?php else: ?>
            <div class="pedidos-lista" style="display: grid; gap: 1.5rem;">
                <?php foreach ($ultimosPedidos as $pedido): ?>
                    <article class="pedido-card" style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: var(--sombra-suave); display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="margin: 0; font-weight: bold;">Pedido #<?php echo htmlspecialchars($pedido['id_pedido']); ?></p>
                            <p style="margin: 0.5rem 0 0 0; color: #64748b;">Fecha: <?php echo date("d/m/Y", strtotime($pedido['fecha'])); ?></p>
                            <p style="margin: 0.5rem 0 0 0; color: #64748b;">Total: <strong><?php echo number_format($pedido['total'], 2, ',', '.'); ?>€</strong></p>
                        </div>
                        <div style="text-align: right;">
                            <span class="estado <?php echo htmlspecialchars($pedido['estado']); ?>" style="padding: 0.5rem 1rem; border-radius: 20px; font-weight: bold; color: white;"><?php echo ucfirst($pedido['estado']); ?></span>
                            <a href="./?vistaMenu=detalle_pedido&id=<?php echo $pedido['id_pedido']; ?>" class="btn-ver-mas" style="margin-top: 1rem; display: block;">Ver Detalles</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="seccion-catalogo" style="padding: 4rem 5%;">
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
            <?php endforeach; ?>
        </div>
    </section>

</main>
