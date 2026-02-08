<?php
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ./?userSession=login");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener pedidos del usuario
$stmt = $pdo->prepare("
    SELECT p.*, COUNT(pi.id_pedido_item) as total_items 
    FROM pedidos p
    LEFT JOIN pedido_items pi ON p.id_pedido = pi.id_pedido
    WHERE p.id_usuario = ?
    GROUP BY p.id_pedido
    ORDER BY p.fecha DESC
");
$stmt->execute([$id_usuario]);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="mis-pedidos-container">
    <div class="header-seccion">
        <h2>Mis Pedidos</h2>
        <p>Gestiona y revisa el historial de tus compras</p>
    </div>

    <?php if (empty($pedidos)): ?>
        <div class="empty-state">
            <div class="empty-icon">📦</div>
            <h3>Aún no has realizado ningún pedido</h3>
            <p>Explora nuestras categorías y encuentra los mejores productos.</p>
            <a href="./?vistaMenu=categorias_productos" class="btn btn-primary">Ir a la tienda</a>
        </div>
    <?php
else: ?>
        <div class="pedidos-lista-v2">
            <?php foreach ($pedidos as $pedido): ?>
                <div class="pedido-card">
                    <div class="pedido-header">
                        <div class="pedido-info-principal">
                            <span class="pedido-id">Pedido #<?php echo str_pad($pedido['id_pedido'], 5, '0', STR_PAD_LEFT); ?></span>
                            <span class="pedido-fecha"><?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?></span>
                        </div>
                        <div class="pedido-status <?php echo $pedido['estado']; ?>">
                            <?php echo ucfirst($pedido['estado']); ?>
                        </div>
                    </div>
                    
                    <div class="pedido-body">
                        <div class="pedido-stats">
                            <div class="stat">
                                <span class="label">Artículos</span>
                                <span class="value"><?php echo $pedido['total_items']; ?></span>
                            </div>
                            <div class="stat">
                                <span class="label">Total</span>
                                <span class="value-total"><?php echo number_format($pedido['total'], 2, ',', '.'); ?>€</span>
                            </div>
                        </div>
                        <div class="pedido-acciones">
                            <a href="./?vistaMenu=detalle_pedido&id=<?php echo $pedido['id_pedido']; ?>" class="btn-detalle">Ver detalles</a>
                        </div>
                    </div>
                </div>
            <?php
    endforeach; ?>
        </div>
    <?php
endif; ?>
</div>

<style>
.mis-pedidos-container {
    max-width: 1000px;
    margin: 2rem auto;
    padding: 0 1.5rem;
}

.header-seccion {
    margin-bottom: 2.5rem;
    text-align: center;
}

.header-seccion h2 {
    font-size: 2.5rem;
    color: var(--azul-primario);
    margin-bottom: 0.5rem;
}

.header-seccion p {
    color: #64748b;
}

.pedidos-lista-v2 {
    display: grid;
    gap: 1.5rem;
}

.pedido-card {
    background: white;
    border-radius: 16px;
    box-shadow: var(--sombra-suave);
    overflow: hidden;
    border: 1px solid rgba(0,0,0,0.05);
    transition: transform 0.3s ease;
}

.pedido-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--sombra-premium);
}

.pedido-header {
    background: #f8fafc;
    padding: 1.2rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e2e8f0;
}

.pedido-info-principal {
    display: flex;
    flex-direction: column;
}

.pedido-id {
    font-weight: 800;
    color: var(--azul-primario);
    font-size: 1.1rem;
}

.pedido-fecha {
    font-size: 0.85rem;
    color: #64748b;
}

.pedido-status {
    padding: 0.4rem 1rem;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
}

.status.pendiente { background: #fef3c7; color: #92400e; }
.status.pagado { background: #dcfce7; color: #166534; }
.status.entregado { background: #dbeafe; color: #1e40af; }

.pedido-body {
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pedido-stats {
    display: flex;
    gap: 3rem;
}

.stat {
    display: flex;
    flex-direction: column;
}

.stat .label {
    font-size: 0.75rem;
    text-transform: uppercase;
    color: #94a3b8;
    letter-spacing: 0.5px;
}

.stat .value {
    font-weight: 700;
    color: var(--azul-primario);
}

.stat .value-total {
    font-weight: 800;
    color: var(--azul-vibrante);
    font-size: 1.25rem;
}

.btn-detalle {
    background: var(--azul-primario);
    color: white;
    padding: 0.8rem 1.5rem;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: background 0.3s;
}

.btn-detalle:hover {
    background: var(--azul-oscuro);
}

.empty-state {
    text-align: center;
    padding: 5rem 2rem;
    background: white;
    border-radius: 20px;
    box-shadow: var(--sombra-suave);
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1.5rem;
}

@media (max-width: 640px) {
    .pedido-body {
        flex-direction: column;
        gap: 1.5rem;
        align-items: flex-start;
    }
    .pedido-stats {
        width: 100%;
        justify-content: space-between;
    }
}
</style>
