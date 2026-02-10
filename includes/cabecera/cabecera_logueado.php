<?php
// Supón que ya se inició sesión y tienes nombre y tipo de usuario en sesión
$nombre_usuario = $_SESSION['usuario_nombre'] ?? 'Usuario';
?>
<header class="cabecera-usuario-logueado">
    <div class="header-left">
        <div class="logo">
            <a href="./" aria-label="Ir a la página principal">
                <img src="./assets/img/logo/logo_supermarket.png" alt="Logo SupermarketArthur" />
            </a>
        </div>
        <h1 class="titulo-web-invitado">SuperMarketArthur</h1>
    </div>

    <div class="search-container">
        <div class="search-bar-wrapper">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Busca productos, frutas, categorías..." autocomplete="off">
        </div>
        <div id="searchResults" class="search-results"></div>
    </div>

    <div class="user-actions">
        <a href="#" id="openCart" class="btn-profile" style="text-decoration: none; color: white;">🛒 Carrito</a>
        
        <a href="./?userSession=perfil" class="user-profile-link">
            <span class="user-name">Hola, <?php echo htmlspecialchars($nombre_usuario); ?></span>
        </a>

        <form action="./config/cerrar_session.php" method="post" class="cerrar-sesion-usuario" style="margin:0;">
            <?php
// Generar token CSRF para el cierre de sesión si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" title="Cerrar sesión" class="btn-logout">
                <img src="./assets/img/cerrar_session/apagar.png" alt="Cerrar sesión" onerror="this.onerror=null;this.src='./assets/img/logo/favicon.ico'">
            </button>
        </form>
    </div>
</header>
