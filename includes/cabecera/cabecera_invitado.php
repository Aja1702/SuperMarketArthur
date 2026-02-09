<header class="header-invitado" role="banner" aria-label="Cabecera de SuperMarketArthur para usuarios invitados">
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

    <nav class="nav-invitado" role="navigation" aria-label="Navegación principal para invitados">
        <ul class="menu-invitado">
            <li><a href="checkout.php" id="openCart" class="btn-login" aria-label="Ver carrito de compras"><i class="fas fa-shopping-cart"></i>Carrito</a></li>
            <li><a href="./?userSession=login" class="btn-login">Iniciar sesión</a></li>
            <li><a href="./?userSession=registro" class="btn-registrarse destacado">Registrarse</a></li>
        </ul>
    </nav>
</header>