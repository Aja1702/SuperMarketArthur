<?php
if (!isset($_SESSION['id_usuario']) || !isset($_GET['id'])) {
    header("Location: ./?vistaMenu=mis_pedidos");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$id_pedido = (int)$_GET['id'];

// Obtener info general del pedido y validar que pertenece al usuario
$stmt = $pdo->prepare("
    SELECT p.*, d.calle, d.numero, d.portal_piso, d.localidad, d.provincia, d.cp
    FROM pedidos p
    LEFT JOIN direcciones d ON p.id_direccion = d.id_direccion
    WHERE p.id_pedido = ? AND p.id_usuario = ?
");
$stmt->execute([$id_pedido, $id_usuario]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    echo "<div class='error-container'><h3>Pedido no encontrado</h3><a href='./?vistaMenu=mis_pedidos'>Volver</a></div>";
    return;
}

// Obtener items del pedido
$stmtItems = $pdo->prepare("
    SELECT pi.*, pr.nombre_producto, pr.url_imagen
    FROM pedido_items pi
    JOIN productos pr ON pi.id_producto = pr.id_producto
    WHERE pi.id_pedido = ?
");
$stmtItems->execute([$id_pedido]);
$items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="detalle-pedido-container">
    <div class="detalle-header">
        <a href="./?vistaMenu=mis_pedidos" class="btn-volver">← Volver a mis pedidos</a>
        <h2>Detalle del Pedido #<?php echo str_pad($pedido['id_pedido'], 5, '0', STR_PAD_LEFT); ?></h2>
        <div class="pedido-meta">
            <span>Realizado el <?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?></span>
            <span class="badge <?php echo $pedido['estado']; ?>"><?php echo ucfirst($pedido['estado']); ?></span>
        </div>
    </div>

    <div class="detalle-grid">
        <div class="productos-section">
            <h3>Productos comprados</h3>
            <div class="tabla-productos">
                <?php foreach ($items as $item): ?>
                    <div class="item-fila">
                        <img src="<?php echo $item['url_imagen'] ?: './assets/img/productos/default.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($item['nombre_producto']); ?>"
                             onerror="this.onerror=null;this.src='./assets/img/logo/logo_supermarket.png'">
                        <div class="item-info">
                            <h4><?php echo htmlspecialchars($item['nombre_producto']); ?></h4>
                            <p>Cantidad: <?php echo $item['cantidad']; ?></p>
                        </div>
                        <div class="item-precios">
                            <span class="precio-unit"><?php echo number_format($item['precio_unitario'], 2, ',', '.'); ?>€/ud</span>
                            <span class="subtotal"><?php echo number_format($item['precio_unitario'] * $item['cantidad'], 2, ',', '.'); ?>€</span>
                        </div>
                    </div>
                <?php
endforeach; ?>
            </div>
        </div>

        <div class="resumen-section">
            <div class="card-resumen">
                <h3>Resumen y Entrega</h3>
                
                <div class="resumen-fila">
                    <span>Estado del pago</span>
                    <span class="val-pago"><?php echo($pedido['estado'] == 'pendiente') ? 'Pendiente' : 'Completado'; ?></span>
                </div>

                <div class="direccion-envio">
                    <h4>Dirección de envío</h4>
                    <?php if ($pedido['calle']): ?>
                        <p><?php echo htmlspecialchars($pedido['calle'] . ", " . $pedido['numero']); ?></p>
                        <p><?php echo htmlspecialchars($pedido['portal_piso']); ?></p>
                        <p><?php echo htmlspecialchars($pedido['cp'] . " - " . $pedido['localidad']); ?></p>
                        <p><?php echo htmlspecialchars($pedido['provincia']); ?></p>
                    <?php
else: ?>
                        <p>Recogida en tienda / No especificada</p>
                    <?php
endif; ?>
                </div>

                <div class="total-caja">
                    <span class="label">Total del pedido</span>
                    <span class="total-valor"><?php echo number_format($pedido['total'], 2, ',', '.'); ?>€</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.detalle-pedido-container {
    max-width: 1100px;
    margin: 2rem auto;
    padding: 0 1.5rem;
}

.detalle-header {
    margin-bottom: 2rem;
}

.btn-volver {
    display: inline-block;
    color: var(--azul-vibrante);
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.detalle-header h2 {
    font-size: 2.2rem;
    color: var(--azul-primario);
    margin: 0;
}

.pedido-meta {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-top: 0.5rem;
    color: #64748b;
}

.badge {
    padding: 0.3rem 0.8rem;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
}

.badge.pendiente { background: #fef3c7; color: #92400e; }
.badge.pagado { background: #dcfce7; color: #166534; }
.badge.entregado { background: #dbeafe; color: #1e40af; }

.detalle-grid {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 2rem;
}

.productos-section, .resumen-section {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: var(--sombra-suave);
    border: 1px solid rgba(0,0,0,0.05);
}

.productos-section h3, .resumen-section h3 {
    margin-top: 0;
    margin-bottom: 1.5rem;
    font-size: 1.25rem;
    color: var(--azul-primario);
    border-bottom: 2px solid var(--gris-fondo);
    padding-bottom: 0.8rem;
}

.item-fila {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.item-fila:last-child { border-bottom: none; }

.item-fila img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 12px;
}

.item-info { flex: 1; }
.item-info h4 { margin: 0 0 0.3rem 0; font-size: 1rem; }
.item-info p { margin: 0; font-size: 0.85rem; color: #64748b; }

.item-precios { text-align: right; }
.precio-unit { display: block; font-size: 0.8rem; color: #94a3b8; }
.subtotal { display: block; font-weight: 700; color: var(--azul-primario); font-size: 1.1rem; }

.resumen-fila {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.direccion-envio {
    margin: 2rem 0;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 12px;
}

.direccion-envio h4 { margin-top: 0; font-size: 0.9rem; color: var(--azul-secundario); margin-bottom: 0.5rem; }
.direccion-envio p { margin: 0.2rem 0; font-size: 0.85rem; color: #475569; }

.total-caja {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 2px dashed #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.total-caja .label { font-weight: 700; color: var(--azul-primario); }
.total-valor { font-size: 1.8rem; font-weight: 800; color: var(--azul-vibrante); }

@media (max-width: 900px) {
    .detalle-grid { grid-template-columns: 1fr; }
}
</style>
