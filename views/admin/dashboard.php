<div class="admin-dashboard">
    <!-- HEADER STATS -->
    <div class="stats-grid">
        <div class="stat-card verde">
            <i class="fas fa-users"></i>
            <div>
                <h3>Total Usuarios: <?php echo $admin_stats['total_users']; ?></h3>
            </div>
        </div>

        <div class="stat-card azul">
            <i class="fas fa-box"></i>
            <div>
                <h3>Productos: <?php echo $admin_stats['total_products']; ?></h3>
            </div>
        </div>

        <div class="stat-card rojo">
            <i class="fas fa-shopping-cart"></i>
            <div>
                <h3>Pedidos Pendientes: <?php echo $admin_stats['pending_orders']; ?></h3>
            </div>
        </div>

        <div class="stat-card naranja">
            <i class="fas fa-euro-sign"></i>
            <div>
                <h3>Ventas totales: <?php echo number_format($admin_stats['ingresos_totales'], 2); ?><?php echo htmlspecialchars($simbolo_moneda); ?></h3>
            </div>
        </div>
    </div>

    <!-- ALERTAS RÁPIDAS -->
    <div class="alertas-row">
        <div class="alerta stock-bajo">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?php echo $admin_stats['low_stock_products']; ?> productos con stock bajo</span>
        </div>
        <div class="alerta pedidos-urgentes">
            <i class="fas fa-clock"></i>
            <span><?php echo $admin_stats['pedidos_retrasados']; ?> pedidos retrasados</span>
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
                    <?php foreach($ultimos_pedidos as $pedido): ?>
                    <tr>
                        <td>#<?php echo $pedido['id_pedido']; ?></td>
                        <td><?php echo htmlspecialchars($pedido['nombre']); ?></td>
                        <td><?php echo number_format($pedido['total'], 2); ?><?php echo htmlspecialchars($simbolo_moneda); ?></td>
                        <td>
                            <span class="estado <?php echo $pedido['estado']; ?>">
                                <?php echo ucfirst($pedido['estado']); ?>
                            </span>
                        </td>
                        <td><?php echo $pedido['fecha_fmt']; ?></td>
                        <td>
                            <a href="/SuperMarketArthur/admin/pedidos?ver=<?php echo $pedido['id_pedido']; ?>" class="btn-sm">Ver</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
