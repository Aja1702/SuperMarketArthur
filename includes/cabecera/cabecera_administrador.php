<header class="cabecera-admin">
    <div class="logo-admin">
        <img src="./img/administrador/HTML.png" alt="logo html">
        <img src="./img/administrador/CSS.png" alt="logo css">
        <img src="./img/administrador/JS.png" alt="logo javascript">
        <img src="./img/administrador/PHP.png" alt="logo php">
    </div>
    <div class="info-admin">
        <span class="rol-admin">Administrador -> </span>
        <span class="nombre-admin">
            <?php echo $_SESSION['nombre'] ?? 'root'; ?>
        </span>
        <span class="hora-admin">
            <?php echo date('d/m/Y H:i'); ?>
        </span>
        <form action="./config/cerrar_session.php" method="post" class="cerrar-sesion-admin">
            <button type="submit" title="Cerrar sesión" class="btn-cerrar-sesion-admin">
                <img src="./img/cerrar_session/apagar.png" alt="Cerrar sesión">
            </button>
        </form>
    </div>
</header>