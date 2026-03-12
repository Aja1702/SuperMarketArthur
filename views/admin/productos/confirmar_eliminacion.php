<?php
// Verificación de seguridad: si no hay un producto, no mostramos nada y volvemos.
if (empty($producto)) {
    header('Location: /SuperMarketArthur/admin/productos');
    exit();
}
?>

<div class="admin-page-container">
    <div class="admin-page-header">
        <h2 class="titulo-seccion-premium">Confirmar Eliminación</h2>
    </div>

    <div class="form-config" style="max-width: 600px; margin-top: 2rem; border-left: 5px solid #f56565;">
        <p style="font-size: 1.1rem; margin-bottom: 2rem;">
            ¿Estás seguro de que quieres eliminar permanentemente el producto
            <strong style="color: #c53030;">"<?php echo htmlspecialchars($producto['nombre_producto']); ?>"</strong>?
        </p>
        <p style="color: #718096;">Esta acción no se puede deshacer.</p>

        <form method="POST" action="/SuperMarketArthur/admin/productos/eliminar">

            <?php // Enviamos el ID del producto de forma oculta ?>
            <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($producto['id_producto']); ?>">

            <div class="form-actions-config" style="display: flex; justify-content: flex-end; gap: 1rem;">
                <a href="/SuperMarketArthur/admin/productos" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-danger">Sí, eliminar para siempre</button>
            </div>

        </form>
    </div>
</div>
