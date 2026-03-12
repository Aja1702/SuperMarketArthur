<div class="admin-productos">
    <!-- TABS: Lista / Añadir -->
    <div class="tabs-productos">
        <button id="tab-btn-lista" class="tab-btn active" onclick="mostrarTab(event, 'lista')">📋 Lista Productos</button>
        <button id="tab-btn-nuevo" class="tab-btn" onclick="mostrarTab(event, 'nuevo')">➕ Nuevo Producto</button>
    </div>

    <!-- TAB 1: LISTA PRODUCTOS -->
    <div id="lista-productos" class="tab-content active">
        <?php if (empty($productos)): ?>
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h3>No hay productos</h3>
                <p>Añade el primer producto del supermercado</p>
                <button class="btn-primary" onclick="mostrarTab(event, 'nuevo')">Añadir primero</button>
            </div>
        <?php else: ?>
            <div class="tabla-responsive">
                <table class="tabla-productos">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($productos as $producto): 
                            $stock_bajo = $producto['stock'] <= 5;
                            $sin_stock = $producto['stock'] == 0;
                        ?>
                        <tr class="<?php echo $sin_stock ? 'sin-stock' : ($stock_bajo ? 'stock-bajo' : ''); ?>">
                            <td>
                                <img src="assets/img/productos/<?php echo htmlspecialchars($producto['url_imagen']); ?>" 
                                     alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>" 
                                     onerror="this.onerror=null; this.src='assets/img/productos/sin-imagen.jpg';">
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($producto['nombre_producto']); ?></strong>
                                <?php if($producto['descripcion']): ?>
                                    <br><small><?php echo substr($producto['descripcion'], 0, 50); ?>...</small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($producto['nombre_categoria'] ?? 'Sin categoría'); ?></td>
                            <td><?php echo number_format($producto['precio'], 2); ?><?php echo htmlspecialchars($simbolo_moneda); ?></td>
                            <td>
                                <span class="stock-badge <?php echo $sin_stock ? 'rojo' : ($stock_bajo ? 'amarillo' : 'verde'); ?>">
                                    <?php echo $producto['stock']; ?>
                                </span>
                            </td>
                            <td>
                                <span class="estado <?php echo $sin_stock ? 'inactivo' : 'activo'; ?>">
                                    <?php echo $sin_stock ? 'Sin stock' : 'Disponible'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="acciones">
                                    <a href="?vistaMenu=admin_productos&editar=<?php echo $producto['id_producto']; ?>" 
                                       class="btn-sm editar" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" onclick="eliminarProducto(event, <?php echo $producto['id_producto']; ?>)"
                                       class="btn-sm eliminar" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
                    <a href="?vistaMenu=admin_productos&page=<?php echo max(1, $pagina_actual - 1); ?>"
                       class="btn-paginacion <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>">
                        &laquo; Anterior
                    </a>

                    <span class="info-paginacion">Página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?></span>

                    <a href="?vistaMenu=admin_productos&page=<?php echo min($total_paginas, $pagina_actual + 1); ?>"
                       class="btn-paginacion <?php echo ($pagina_actual >= $total_paginas) ? 'disabled' : ''; ?>">
                        Siguiente &raquo;
                    </a>
                <?php endif; ?>
            </div>

        <?php endif; ?>
    </div>

    <!-- TAB 2: NUEVO PRODUCTO -->
    <div id="nuevo-producto" class="tab-content">
        <form method="POST" action="controllers/admin_productos.php" enctype="multipart/form-data" class="form-producto">
            <input type="hidden" name="accion" value="nuevo">
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" name="nombre_producto" required maxlength="100">
                </div>
                
                <div class="form-group">
                    <label>Categoría *</label>
                    <select name="id_categoria" required>
                        <option value="">Selecciona categoría</option>
                        <?php foreach($categorias as $cat): ?>
                            <option value="<?php echo $cat['id_categoria']; ?>">
                                <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Precio (<?php echo htmlspecialchars($simbolo_moneda); ?>) *</label>
                    <input type="number" name="precio" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label>Stock *</label>
                    <input type="number" name="stock" min="0" required>
                </div>
                
                <div class="form-group full">
                    <label>Descripción</label>
                    <textarea name="descripcion" rows="3" maxlength="500"></textarea>
                </div>
                
                <div class="form-group full">
                    <label>Imagen (JPG/PNG)</label>
                    <input type="file" name="imagen" accept="image/jpeg,image/png">
                    <small>Máx 2MB. Se guardará en assets/img/productos/</small>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">➕ Añadir Producto</button>
                <button type="button" onclick="mostrarTab(event, 'lista')" class="btn-secundario">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function mostrarTab(event, tabName) {
    event.preventDefault(); // ¡LA CLAVE! Evita que los enlaces recarguen la página.

    // Ocultar todas las pestañas de contenido
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

    // Desactivar todos los botones de pestaña
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

    // Mostrar la pestaña de contenido correcta
    const contentId = (tabName === 'nuevo') ? 'nuevo-producto' : 'lista-productos';
    document.getElementById(contentId).classList.add('active');

    // Activar el botón de pestaña correcto
    const buttonId = 'tab-btn-' + tabName;
    if (document.getElementById(buttonId)) {
        document.getElementById(buttonId).classList.add('active');
    }
}

function eliminarProducto(event, id) {
    event.preventDefault(); // Evita que el enlace recargue la página.

    if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
        const data = new URLSearchParams();
        data.append('accion', 'eliminar');
        data.append('id', id);

        fetch('controllers/admin_productos.php', {
            method: 'POST',
            body: data
        })
        .then(response => response.json()) // Asume que el controlador devuelve JSON
        .then(data => {
            if (data.success) {
                location.reload(); // Recarga solo si se ha eliminado con éxito
            } else {
                alert('Error al eliminar el producto: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error en la petición:', error);
            alert('Ocurrió un error de conexión.');
        });
    }
}
</script>
