<footer class="footer-contenido" role="contentinfo" aria-label="Pie de página del Panel de Administración">
    <div class="footer-grid">
        <div class="footer-info">
            <h4 class="footer-logo">SuperMarketArthur</h4>
            <p>Panel de Administración del Sistema.</p>
        </div>

        <div class="footer-col">
            <h5>Navegación Principal</h5>
            <div class="footer-enlaces-moderno">
                <a href="<?php echo BASE_URL; ?>admin">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>admin/productos">Productos</a>
                <a href="<?php echo BASE_URL; ?>admin/pedidos">Pedidos</a>
                <a href="<?php echo BASE_URL; ?>admin/usuarios">Usuarios</a>
            </div>
        </div>

        <div class="footer-col">
            <h5>Configuración</h5>
            <div class="footer-enlaces-moderno">
                <a href="<?php echo BASE_URL; ?>admin/categorias">Categorías</a>
                <a href="<?php echo BASE_URL; ?>admin/config">Ajustes del Sistema</a>
            </div>
        </div>

        <div class="footer-contacto-mini" style="text-align: left;">
            <h5>Estado del Sistema</h5>
            <p style="display:flex; align-items:center; gap: 0.5rem;"><span style="color: #48bb78; font-size: 1.5rem;">●</span> Todos los sistemas operativos.</p>
        </div>
    </div>
    <div class="copyright-v2">
        <p>&copy; <?php echo date("Y"); ?> SuperMarketArthur&trade; — Panel de Control.</p>
    </div>
</footer>
