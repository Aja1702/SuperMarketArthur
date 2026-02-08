<?php
if (!isset($_SESSION['id_usuario']) || !isset($_GET['order_id'])) {
    header("Location: /SuperMarketArthur/?userSession=login");
    exit();
}

include './config/iniciar_session.php';
include './models/Order.php';

$orderId = (int)$_GET['order_id'];
$userId = $_SESSION['id_usuario'];

$orderModel = new Order($pdo);
$order = $orderModel->getOrderById($orderId, $userId);

if (!$order) {
    header("Location: /SuperMarketArthur/?vistaMenu=mis_pedidos");
    exit();
}
?>

<div class="confirmation-container">
    <div class="confirmation-card">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>

        <h1>¡Pedido Confirmado!</h1>
        <p class="order-number">Número de pedido: #<?php echo str_pad($order['id_pedido'], 6, '0', STR_PAD_LEFT); ?></p>

        <div class="order-details">
            <div class="detail-row">
                <span>Fecha del pedido:</span>
                <span><?php echo date('d/m/Y H:i', strtotime($order['fecha'])); ?></span>
            </div>
            <div class="detail-row">
                <span>Estado:</span>
                <span class="status-<?php echo $order['estado']; ?>"><?php echo ucfirst($order['estado']); ?></span>
            </div>
            <div class="detail-row">
                <span>Método de pago:</span>
                <span><?php echo ucfirst($order['metodo_pago'] ?? 'Tarjeta'); ?></span>
            </div>
            <div class="detail-row total-row">
                <span>Total pagado:</span>
                <span>€<?php echo number_format($order['total'], 2); ?></span>
            </div>
        </div>

        <div class="order-items">
            <h3>Artículos del pedido</h3>
            <?php foreach ($order['items'] as $item): ?>
                <div class="order-item">
                    <img src="./assets/img/productos/<?php echo htmlspecialchars($item['url_imagen'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($item['nombre_producto']); ?>">
                    <div class="item-info">
                        <h4><?php echo htmlspecialchars($item['nombre_producto']); ?></h4>
                        <p>Cantidad: <?php echo $item['cantidad']; ?> × €<?php echo number_format($item['precio_unitario'], 2); ?></p>
                    </div>
                    <div class="item-price">
                        €<?php echo number_format($item['precio_unitario'] * $item['cantidad'], 2); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="shipping-info">
            <h3>Información de envío</h3>
            <p><?php echo htmlspecialchars($order['nombre'] . ' ' . $order['apellido1']); ?></p>
            <p><?php echo htmlspecialchars($order['calle'] . ', ' . $order['ciudad']); ?></p>
            <p><?php echo htmlspecialchars($order['cp'] . ', ' . $order['provincia'] . ', ' . ($order['pais'] ?? 'España')); ?></p>
            <p><?php echo htmlspecialchars($order['email']); ?></p>
        </div>

        <div class="confirmation-actions">
            <a href="/SuperMarketArthur/?vistaMenu=mis_pedidos" class="btn-primary">Ver Mis Pedidos</a>
            <a href="/SuperMarketArthur/?vistaMenu=categorias_productos" class="btn-secondary">Continuar Comprando</a>
        </div>

        <div class="confirmation-note">
            <p><strong>Nota:</strong> Recibirás un email de confirmación con los detalles de tu pedido.</p>
            <p>El tiempo de entrega estimado es de 3-5 días hábiles.</p>
        </div>
    </div>
</div>

<style>
.confirmation-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 0 2rem;
}

.confirmation-card {
    background: white;
    border-radius: 16px;
    padding: 3rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.success-icon {
    font-size: 4rem;
    color: #48bb78;
    margin-bottom: 1rem;
}

.confirmation-card h1 {
    color: var(--azul-primario);
    margin-bottom: 0.5rem;
    font-size: 2.5rem;
}

.order-number {
    color: var(--azul-vibrante);
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 2rem;
}

.order-details {
    background: #f8fafc;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    text-align: left;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.detail-row:last-child {
    border-bottom: none;
}

.total-row {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--azul-primario);
    border-top: 2px solid var(--azul-vibrante);
    margin-top: 0.5rem;
    padding-top: 1rem;
}

.status-pendiente { color: #ed8936; }
.status-pagado { color: #48bb78; }
.status-enviado { color: #4299e1; }
.status-entregado { color: #48bb78; }
.status-cancelado { color: #f56565; }

.order-items, .shipping-info {
    text-align: left;
    margin-bottom: 2rem;
}

.order-items h3, .shipping-info h3 {
    color: var(--azul-primario);
    margin-bottom: 1rem;
    font-size: 1.3rem;
}

.order-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.order-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}

.item-info h4 {
    margin: 0 0 0.3rem 0;
    font-size: 1rem;
}

.item-info p {
    margin: 0;
    font-size: 0.9rem;
    color: #64748b;
}

.item-price {
    font-weight: 700;
    color: var(--azul-vibrante);
    margin-left: auto;
}

.shipping-info p {
    margin: 0.3rem 0;
    color: #475569;
}

.confirmation-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 2rem;
}

.btn-primary, .btn-secondary {
    padding: 0.8rem 2rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-primary {
    background: var(--azul-vibrante);
    color: white;
}

.btn-primary:hover {
    background: #188fa7;
    transform: translateY(-2px);
}

.btn-secondary {
    background: #e2e8f0;
    color: var(--azul-primario);
}

.btn-secondary:hover {
    background: #cbd5e1;
    transform: translateY(-2px);
}

.confirmation-note {
    background: #fef5e7;
    border: 1px solid #ed8936;
    border-radius: 8px;
    padding: 1rem;
    text-align: left;
}

.confirmation-note p {
    margin: 0.5rem 0;
    font-size: 0.9rem;
    color: #7c2d12;
}

@media (max-width: 768px) {
    .confirmation-card {
        padding: 2rem 1.5rem;
    }

    .confirmation-actions {
        flex-direction: column;
    }

    .btn-primary, .btn-secondary {
        text-align: center;
    }
}
</style>
