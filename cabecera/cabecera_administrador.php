<header class="cabecera-admin">
    <div class="logo-admin">
        <img src="./IMG/HTML.png" alt="logo html">
        <img src="./IMG/CSS.png" alt="logo css">
        <img src="./IMG/JS.png" alt="logo javascript">
        <img src="./IMG/PHP.png" alt="logo php">
    </div>
    <div class="info-admin">

        <span class="rol-admin">Administrador -> </span>
        <span class="nombre-admin">
            <?php echo $_SESSION['nombre'] ?? 'Root'; ?>
        </span>
        <span class="hora-admin">
            <?php echo date('d/m/Y H:i'); ?>
        </span>
        <form action="./sesion_bbdd/cerrar_session.php" method="post" class="cerrar-sesion-admin">
            <button type="submit" title="Cerrar sesión" class="btn-cerrar-sesion-admin">
                <img src="./IMG/apagar.png" alt="Cerrar sesión">
            </button>
        </form>
    </div>
</header>