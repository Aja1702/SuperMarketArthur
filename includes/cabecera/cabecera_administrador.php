<header class="cabecera-admin">
    <div class="logo-admin">
        <a href="/SuperMarketArthur/admin" aria-label="Ir al dashboard de administración">
            <img src="<?php echo BASE_URL; ?>assets/img/logo/logo_supermarket.png" alt="Logo SupermarketArthur" />
        </a>
    </div>
    <div class="info-admin">
        <span class="rol-admin">Administrador</span>
        <span class="nombre-admin">
            <?php echo $_SESSION['nombre'] ?? 'Arturo'; ?>
        </span>
        <span class="hora-admin" id="hora-admin">
            <?php echo date('d/m/Y H:i'); ?>
        </span>

        <form action="/SuperMarketArthur/logout" method="post" class="cerrar-sesion-admin">
            <?php
// Generar token CSRF para el cierre de sesión si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" title="Cerrar sesión" class="btn-cerrar-sesion-admin">
                <img src="<?php echo BASE_URL; ?>assets/img/cerrar_session/apagar.png" alt="Cerrar sesión">
            </button>
        </form>
    </div>

    <script>
        function actualizarHora() {
            const ahora = new Date();
            const fecha = ahora.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            const hora = ahora.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit'
            });
            document.getElementById('hora-admin').textContent = `${fecha} ${hora}`;
        }
        setInterval(actualizarHora, 1000);
        actualizarHora();
    </script>
</header>
