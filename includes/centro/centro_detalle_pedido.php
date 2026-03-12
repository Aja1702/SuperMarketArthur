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
                            <span class="precio-unit"><?php echo number_format($item['precio_unitario'], 2, ',', '.'); ?><?php echo htmlspecialchars($simbolo_moneda); ?>/ud</span>
                            <span class="subtotal"><?php echo number_format($item['precio_unitario'] * $item['cantidad'], 2, ',', '.'); ?><?php echo htmlspecialchars($simbolo_moneda); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
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
                    <?php else: ?>
                        <p>Recogida en tienda / No especificada</p>
                    <?php endif; ?>
                </div>

                <div class="total-caja">
                    <span class="label">Total del pedido</span>
                    <span class="total-valor"><?php echo number_format($pedido['total'], 2, ',', '.'); ?><?php echo htmlspecialchars($simbolo_moneda); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
