<div class="admin-dashboard">
    <!-- HEADER STATS -->
    <div class="stats-grid">
        <div class="stat-card verde">
            <i class="fas fa-users"></i>
            <div>
                <h3>Total Usuarios: <?php echo $pdo->query("SELECT COUNT(*) FROM usuarios WHERE tipo_usu='u'")->fetchColumn(); ?></h3>
            </div>
        </div>
        
        <div class="stat-card azul">
            <i class="fas fa-box"></i>
            <div>
                <h3>Productos: <?php echo $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn(); ?></h3>
            </div>
        </div>
        
        <div class="stat-card rojo">
            <i class="fas fa-shopping-cart"></i>
            <div>
                <h3>Pedidos Pendientes: <?php echo $pdo->query("SELECT COUNT(*) FROM pedidos WHERE estado='pendiente'")->fetchColumn(); ?></h3>
            </div>
        </div>
        
        <div class="stat-card naranja">
            <i class="fas fa-euro-sign"></i>
            <div>
                <h3> Ventas totales: €<?php echo number_format($pdo->query("SELECT COALESCE(SUM(total),0) FROM pedidos WHERE estado='pagado'")->fetchColumn(), 2);?></h3>
            </div>
        </div>
    </div>

    <!-- ALERTAS RÁPIDAS -->
    <div class="alertas-row">
        <div class="alerta stock-bajo">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?php echo $pdo->query("SELECT COUNT(*) FROM productos WHERE stock <= 5")->fetchColumn(); ?> productos con stock bajo</span>
        </div>
        <div class="alerta pedidos-urgentes">
            <i class="fas fa-clock"></i>
            <span><?php echo $pdo->query("SELECT COUNT(*) FROM pedidos WHERE estado='pendiente' AND fecha < DATE_SUB(NOW(), INTERVAL 2 HOUR)")->fetchColumn(); ?> pedidos retrasados</span>
        </div>
    </div>

    <!-- ÚLTIMOS PEDIDOS -->
    <div class="ultimos-pedidos">
        <h3>📦 Últimos Pedidos</h3>
        <div class="tabla-responsive">
            <table class="tabla-pedidos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("
                        SELECT p.id_pedido, u.nombre, p.total, p.estado, p.fecha,
                               DATE_FORMAT(p.fecha, '%d/%m %H:%i') as fecha_fmt
                        FROM pedidos p 
                        JOIN usuarios u ON p.id_usuario = u.id_usuario 
                        ORDER BY p.id_pedido DESC LIMIT 5
                    ");
                    while($pedido = $stmt->fetch()):
                    ?>
                    <tr>
                        <td>#<?php echo $pedido['id_pedido']; ?></td>
                        <td><?php echo htmlspecialchars($pedido['nombre']); ?></td>
                        <td>€<?php echo number_format($pedido['total'],2); ?></td>
                        <td>
                            <span class="estado <?php echo $pedido['estado']; ?>">
                                <?php echo ucfirst($pedido['estado']); ?>
                            </span>
                        </td>
                        <td><?php echo $pedido['fecha_fmt']; ?></td>
                        <td>
                            <a href="?vistaMenu=admin_pedidos&ver=<?php echo $pedido['id_pedido']; ?>" class="btn-sm">Ver</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
