<div class="admin-pedidos">
    <h2 class="titulo-seccion-premium">Gestión de Pedidos</h2>

    <?php if (empty($pedidos)): ?>
        <div class="empty-state">
            <i class="fas fa-receipt"></i>
            <h3>No hay pedidos</h3>
            <p>Aún no se ha realizado ningún pedido en la tienda.</p>
        </div>
    <?php else: ?>
        <div class="tabla-responsive">
            <table class="tabla-pedidos">
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pedidos as $pedido): ?>
                        <tr>
                            <td><strong>#<?php echo htmlspecialchars($pedido['id_pedido']); ?></strong></td>
                            <td>
                                <?php echo htmlspecialchars($pedido['nombre'] . ' ' . $pedido['apellido1']); ?><br>
                                <small><?php echo htmlspecialchars($pedido['email']); ?></small>
                            </td>
                            <td><?php echo date("d/m/Y H:i", strtotime($pedido['fecha'])); ?></td>
                            <td><strong>€<?php echo number_format($pedido['total'], 2); ?></strong></td>
                            <td>
                                <span class="estado <?php echo htmlspecialchars($pedido['estado']); ?>">
                                    <?php echo ucfirst($pedido['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="acciones">
                                    <a href="#" class="btn-sm ver" title="Ver Detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn-sm editar" title="Cambiar Estado">
                                        <i class="fas fa-sync-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- PAGINACIÓN -->
        <div class="paginacion">
            <?php if ($total_paginas > 1): ?>
                <a href="?vistaMenu=admin_pedidos&page=<?php echo max(1, $pagina_actual - 1); ?>"
                   class="btn-paginacion <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>">
                    &laquo; Anterior
                </a>

                <span class="info-paginacion">Página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?></span>

                <a href="?vistaMenu=admin_pedidos&page=<?php echo min($total_paginas, $pagina_actual + 1); ?>"
                   class="btn-paginacion <?php echo ($pagina_actual >= $total_paginas) ? 'disabled' : ''; ?>">
                    Siguiente &raquo;
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
