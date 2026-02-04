<header class="cabecera-admin">
    <div class="logo-admin">
        <img src="./assets/img/administrador/HTML.png" alt="logo html">
        <img src="./assets/img/administrador/CSS.png" alt="logo css">
        <img src="./assets/img/administrador/JS.png" alt="logo javascript">
        <img src="./assets/img/administrador/PHP.png" alt="logo php">
    </div>
    <div class="info-admin">
        <span class="rol-admin">Administrador -> </span>
        <span class="nombre-admin">
            <?php echo $_SESSION['nombre'] ?? 'Arturo'; ?>
        </span>
        <span class="hora-admin" id="hora-admin">
            <?php echo date('d/m/Y H:i'); ?>
        </span>
        <form action="./config/cerrar_session.php" method="post" class="cerrar-sesion-admin">
            <button type="submit" title="Cerrar sesión" class="btn-cerrar-sesion-admin">
                <img src="./assets/img/cerrar_session/apagar.png" alt="Cerrar sesión">
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
        // Actualizar la hora cada minuto
        setInterval(actualizarHora, 1000);
        // Actualizar la hora al cargar la página
        actualizarHora();    
    </script>
</header>