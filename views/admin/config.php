<div class="admin-config-page">
    <h2 class="titulo-seccion-premium">Configuración General</h2>
    <p class="subtitulo-seccion">Ajusta aquí los parámetros principales de tu tienda.</p>

    <?php
    // Mostrar mensaje de éxito o error
    if (isset($_SESSION['success_message'])):
    ?>
        <div class="mensaje-exito"><?php echo $_SESSION['success_message']; ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php
    endif;
    if (isset($_SESSION['error_message'])):
    ?>
        <div class="mensaje-error"><?php echo $_SESSION['error_message']; ?></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <form method="POST" action="/SuperMarketArthur/admin/config/save" class="form-config">

        <!-- 1. Nombre del Sitio -->
        <div class="form-group-config">
            <label for="nombre_sitio">Nombre del Sitio</label>
            <input type="text" id="nombre_sitio" name="config[NOMBRE_SITIO]"
                   value="<?php echo htmlspecialchars($configuraciones['NOMBRE_SITIO'] ?? 'SuperMarketArthur'); ?>"
                   class="form-control-config">
            <small>El título que aparece en la pestaña del navegador y en las cabeceras.</small>
        </div>

        <!-- 2. Umbral de Stock Bajo -->
        <div class="form-group-config">
            <label for="stock_umbral">Umbral de Stock Bajo</label>
            <input type="number" id="stock_umbral" name="config[STOCK_BAJO_UMBRAL]"
                   value="<?php echo htmlspecialchars($configuraciones['STOCK_BAJO_UMBRAL'] ?? 5); ?>"
                   class="form-control-config" min="0">
            <small>Número de unidades para que un producto se considere con "stock bajo" en el dashboard.</small>
        </div>

        <!-- 3. Email de Contacto -->
        <div class="form-group-config">
            <label for="email_contacto">Email de Contacto</label>
            <input type="email" id="email_contacto" name="config[EMAIL_CONTACTO]"
                   value="<?php echo htmlspecialchars($configuraciones['EMAIL_CONTACTO'] ?? ''); ?>"
                   class="form-control-config">
            <small>El email que se mostrará en la sección de contacto y se usará para notificaciones.</small>
        </div>

        <!-- 4. Productos por Página -->
        <div class="form-group-config">
            <label for="productos_pagina">Productos por Página</label>
            <input type="number" id="productos_pagina" name="config[PRODUCTOS_POR_PAGINA]"
                   value="<?php echo htmlspecialchars($configuraciones['PRODUCTOS_POR_PAGINA'] ?? 12); ?>"
                   class="form-control-config" min="1">
            <small>Cuántos productos se muestran por página en las vistas de catálogo.</small>
        </div>

        <!-- 5. Símbolo de Moneda -->
        <div class="form-group-config">
            <label for="simbolo_moneda">Símbolo de Moneda</label>
            <input type="text" id="simbolo_moneda" name="config[SIMBOLO_MONEDA]"
                   value="<?php echo htmlspecialchars($configuraciones['SIMBOLO_MONEDA'] ?? '€'); ?>"
                   class="form-control-config">
            <small>El símbolo que se usará para los precios (ej: €, $, £).</small>
        </div>

        <!-- 6. Modo Mantenimiento -->
        <div class="form-group-config-toggle">
            <label>Modo Mantenimiento</label>
            <div class="toggle-switch">
                <input type="hidden" name="config[MODO_MANTENIMIENTO]" value="0">
                <input type="checkbox" id="modo_mantenimiento" name="config[MODO_MANTENIMIENTO]" value="1"
                    <?php echo (isset($configuraciones['MODO_MANTENIMIENTO']) && $configuraciones['MODO_MANTENIMIENTO'] == 1) ? 'checked' : ''; ?>>
                <label for="modo_mantenimiento" class="slider"></label>
            </div>
            <small>Si se activa, solo los administradores podrán ver la web.</small>
        </div>

        <!-- 7. Habilitar Valoraciones -->
        <div class="form-group-config-toggle">
            <label>Habilitar Valoraciones</label>
            <div class="toggle-switch">
                <input type="hidden" name="config[VALORACIONES_HABILITADAS]" value="0">
                <input type="checkbox" id="valoraciones_habilitadas" name="config[VALORACIONES_HABILITADAS]" value="1"
                    <?php echo (isset($configuraciones['VALORACIONES_HABILITADAS']) && $configuraciones['VALORACIONES_HABILITADAS'] == 1) ? 'checked' : ''; ?>>
                <label for="valoraciones_habilitadas" class="slider"></label>
            </div>
            <small>Permite a los usuarios dejar reseñas y valoraciones en los productos.</small>
        </div>

        <!-- 8. IP de Confianza -->
        <div class="form-group-config">
            <label for="ip_confianza">IP de Confianza</label>
            <input type="text" id="ip_confianza" name="config[IP_CONFIANZA]"
                   value="<?php echo htmlspecialchars($configuraciones['IP_CONFIANZA'] ?? '::1'); ?>"
                   class="form-control-config">
            <small>Esta IP siempre tendrá acceso a la web, incluso en modo mantenimiento. Tu IP actual es: <strong><?php echo $_SERVER['REMOTE_ADDR']; ?></strong></small>
        </div>

        <!-- Botón de Guardar -->
        <div class="form-actions-config">
            <button type="submit" class="btn-primary">Guardar Cambios</button>
        </div>

    </form>
</div>
