<div class="admin-stock">
    <h2 class="titulo-seccion-premium">Gestión de Stock Bajo</h2>
    <p class="subtitulo-seccion">Productos con 5 unidades o menos.</p>

    <?php if (empty($productos_stock_bajo)): ?>
        <div class="empty-state">
            <i class="fas fa-check-circle"></i>
            <h3>¡Todo en orden!</h3>
            <p>No hay productos con stock bajo actualmente.</p>
        </div>
    <?php else: ?>
        <div class="tabla-responsive">
            <table class="tabla-stock">
                <thead>
                    <tr>
                        <th>ID Producto</th>
                        <th>Producto</th>
                        <th>Stock Actual</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($productos_stock_bajo as $producto): ?>
                        <tr>
                            <td><strong>#<?php echo htmlspecialchars($producto['id_producto']); ?></strong></td>
                            <td><?php echo htmlspecialchars($producto['nombre_producto']); ?></td>
                            <td>
                                <span class="stock-badge rojo">
                                    <?php echo $producto['stock']; ?>
                                </span>
                            </td>
                            <td>
                                <div class="acciones">
                                    <a href="?vistaMenu=admin_productos&editar=<?php echo $producto['id_producto']; ?>"
                                       class="btn-sm editar" title="Editar Producto">
                                        <i class="fas fa-edit"></i>
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
                <a href="?vistaMenu=admin_stock&page=<?php echo max(1, $pagina_actual - 1); ?>"
                   class="btn-paginacion <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>">
                    &laquo; Anterior
                </a>

                <span class="info-paginacion">Página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?></span>

                <a href="?vistaMenu=admin_stock&page=<?php echo min($total_paginas, $pagina_actual + 1); ?>"
                   class="btn-paginacion <?php echo ($pagina_actual >= $total_paginas) ? 'disabled' : ''; ?>">
                    Siguiente &raquo;
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
