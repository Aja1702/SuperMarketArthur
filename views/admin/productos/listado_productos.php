<div class="admin-page-container">
    <div class="admin-page-header">
        <h2 class="titulo-seccion-premium">Gestión de Productos</h2>
        <a href="/SuperMarketArthur/admin/productos/nuevo" class="btn-primary">➕ Añadir Nuevo Producto</a>
    </div>

    <?php if (empty($productos)): ?>
        <div class="empty-state">
            <p>Aún no hay productos en la tienda. ¡Añade el primero!</p>
        </div>
    <?php else: ?>
        <div class="tabla-responsive">
            <table class="tabla-pedidos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($producto['id_producto']); ?></td>
                            <td><?php echo htmlspecialchars($producto['nombre_producto']); ?></td>
                            <td><?php echo htmlspecialchars($producto['nombre_categoria'] ?? 'N/A'); ?></td>
                            <td><?php echo number_format($producto['precio'], 2, ',', '.'); ?><?php echo htmlspecialchars($simbolo_moneda); ?></td>
                            <td><?php echo htmlspecialchars($producto['stock']); ?></td>
                            <td class="acciones">
                                <a href="/SuperMarketArthur/admin/productos/editar/<?php echo $producto['id_producto']; ?>" class="btn-sm editar">Editar</a>
                                <a href="/SuperMarketArthur/admin/productos/eliminar/<?php echo $producto['id_producto']; ?>" class="btn-sm eliminar">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php // --- BLOQUE DE PAGINACIÓN --- ?>
        <?php if ($paginacion['total_paginas'] > 1): ?>
            <nav class="paginacion-admin" aria-label="Paginación de productos">
                <ul class="paginacion-lista">

                    <?php // Botón "Anterior" ?>
                    <?php if ($paginacion['pagina_actual'] > 1): ?>
                        <li class="paginacion-item">
                            <a href="/SuperMarketArthur/admin/productos/<?php echo $paginacion['pagina_actual'] - 1; ?>" class="paginacion-enlace">« Anterior</a>
                        </li>
                    <?php endif; ?>

                    <?php // Números de página ?>
                    <?php for ($i = 1; $i <= $paginacion['total_paginas']; $i++): ?>
                        <li class="paginacion-item <?php echo ($i == $paginacion['pagina_actual']) ? 'active' : ''; ?>">
                            <a href="/SuperMarketArthur/admin/productos/<?php echo $i; ?>" class="paginacion-enlace"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php // Botón "Siguiente" ?>
                    <?php if ($paginacion['pagina_actual'] < $paginacion['total_paginas']): ?>
                        <li class="paginacion-item">
                            <a href="/SuperMarketArthur/admin/productos/<?php echo $paginacion['pagina_actual'] + 1; ?>" class="paginacion-enlace">Siguiente »</a>
                        </li>
                    <?php endif; ?>

                </ul>
            </nav>
        <?php endif; ?>
        <?php // --- FIN BLOQUE DE PAGINACIÓN --- ?>

    <?php endif; ?>
</div>
