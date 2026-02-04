<div class="admin-productos">
    <!-- TABS: Lista / Añadir -->
    <div class="tabs-productos">
        <button class="tab-btn active" onclick="mostrarTab('lista')">📋 Lista Productos</button>
        <button class="tab-btn" onclick="mostrarTab('nuevo')">➕ Nuevo Producto</button>
    </div>

    <!-- TAB 1: LISTA PRODUCTOS -->
    <div id="lista-productos" class="tab-content active">
        <?php
        $stmt = $pdo->query("
            SELECT p.*, c.nombre_categoria 
            FROM productos p 
            LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
            ORDER BY p.nombre_producto
        ");
        $productos = $stmt->fetchAll();
        ?>
        
        <?php if (empty($productos)): ?>
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h3>No hay productos</h3>
                <p>Añade el primer producto del supermercado</p>
                <button class="btn-primary" onclick="mostrarTab('nuevo')">Añadir primero</button>
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
                                     onerror="this.src='assets/img/productos/sin-imagen.jpg'">
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($producto['nombre_producto']); ?></strong>
                                <?php if($producto['descripcion']): ?>
                                    <br><small><?php echo substr($producto['descripcion'], 0, 50); ?>...</small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($producto['nombre_categoria'] ?? 'Sin categoría'); ?></td>
                            <td>€<?php echo number_format($producto['precio'], 2); ?></td>
                            <td>
                                <span class="stock-badge <?php echo $sin_stock ? 'rojo' : ($stock_bajo ? 'amarillo' : 'verde'); ?>">
                                    <?php echo $producto['stock']; ?> und
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
                                    <a href="#" onclick="eliminarProducto(<?php echo $producto['id_producto']; ?>)" 
                                       class="btn-sm eliminar" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
                        <?php
                        $cats = $pdo->query("SELECT * FROM categorias ORDER BY nombre_categoria")->fetchAll();
                        foreach($cats as $cat): ?>
                            <option value="<?php echo $cat['id_categoria']; ?>">
                                <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Precio (€) *</label>
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
                <a href="#" onclick="mostrarTab('lista')" class="btn-secundario">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
function mostrarTab(tab) {
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(tab + '-productos').classList.add('active');
    event.target.classList.add('active');
}

function eliminarProducto(id) {
    if(confirm('¿Eliminar este producto?')) {
        fetch('controllers/admin_productos.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'accion=eliminar&id=' + id
        }).then(() => location.reload());
    }
}
</script>
