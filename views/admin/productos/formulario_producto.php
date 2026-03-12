<?php
// Determinamos los textos y la acción según el modo (crear o editar)
$esModoEditar = ($modo === 'editar');
$titulo = $esModoEditar ? 'Editar Producto' : 'Añadir Nuevo Producto';
$accion = $esModoEditar ? '/SuperMarketArthur/admin/productos/actualizar' : '/SuperMarketArthur/admin/productos/crear';
$textoBoton = $esModoEditar ? 'Actualizar Producto' : 'Guardar Producto';
?>

<div class="admin-page-container">
    <div class="admin-page-header">
        <h2 class="titulo-seccion-premium"><?php echo $titulo; ?></h2>
        <a href="/SuperMarketArthur/admin/productos" class="btn-volver">⬅️ Volver al listado</a>
    </div>

    <form method="POST" action="<?php echo $accion; ?>" class="form-config" enctype="multipart/form-data">

        <?php // Si estamos editando, necesitamos enviar el ID del producto de forma oculta ?>
        <?php if ($esModoEditar): ?>
            <input type="hidden" name="producto[id_producto]" value="<?php echo htmlspecialchars($producto['id_producto']); ?>">
        <?php endif; ?>

        <div class="form-group-config">
            <label for="nombre_producto">Nombre del Producto</label>
            <input type="text" id="nombre_producto" name="producto[nombre_producto]" class="form-control-config" required value="<?php echo htmlspecialchars($producto['nombre_producto'] ?? ''); ?>">
        </div>

        <div class="form-group-config">
            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="producto[descripcion]" class="form-control-config" rows="5"><?php echo htmlspecialchars($producto['descripcion'] ?? ''); ?></textarea>
        </div>

        <div class="form-grid">
            <div class="form-group-config">
                <label for="precio">Precio</label>
                <input type="number" id="precio" name="producto[precio]" class="form-control-config" step="0.01" required value="<?php echo htmlspecialchars($producto['precio'] ?? ''); ?>">
            </div>

            <div class="form-group-config">
                <label for="stock">Stock</label>
                <input type="number" id="stock" name="producto[stock]" class="form-control-config" required value="<?php echo htmlspecialchars($producto['stock'] ?? ''); ?>">
            </div>

            <div class="form-group-config">
                <label for="id_categoria">Categoría</label>
                <select id="id_categoria" name="producto[id_categoria]" class="form-control-config">
                    <option value="">Selecciona una categoría</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option
                            value="<?php echo $categoria['id_categoria']; ?>"
                            <?php echo (($producto['id_categoria'] ?? '') == $categoria['id_categoria']) ? 'selected' : ''; ?>
                        >
                            <?php echo htmlspecialchars($categoria['nombre_categoria']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group-config">
            <label for="url_imagen">Imagen del Producto</label>
            <?php // Mostramos la imagen actual si existe ?>
            <?php if ($esModoEditar && !empty($producto['url_imagen'])): ?>
                <div style="margin-bottom: 1rem;">
                    <img src="<?php echo BASE_URL . $producto['url_imagen']; ?>" alt="Imagen actual" style="max-width: 100px; max-height: 100px; border-radius: 8px;">
                    <small style="display: block; margin-top: 0.5rem;">Imagen actual. Sube una nueva para reemplazarla.</small>
                </div>
            <?php endif; ?>
            <input type="file" id="url_imagen" name="imagen_producto" class="form-control-config">
        </div>

        <div class="form-actions-config">
            <button type="submit" class="btn-primary"><?php echo $textoBoton; ?></button>
        </div>

    </form>
</div>
