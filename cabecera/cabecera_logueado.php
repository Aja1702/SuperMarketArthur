<?php
// Supón que ya se inició sesión y tienes nombre y tipo de usuario en sesión
$nombre_usuario = $_SESSION['nombre'] ?? 'Usuario';
?>

<header class="cabecera-usuario-logueado">
    <div class="logo">
        <a href="/SuperMarketArthur/">
            <img src="./IMG/logo_supermarket.png" alt="Logo S-M-A">
        </a>
    </div>
    <form action="./sesion_bbdd/cerrar_session.php" method="post" class="cerrar-sesion-usuario">
        <button type="submit" title="Cerrar sesión" class="btn-cerrar-sesion-usuario">
            <img src="./IMG/apagar.png" alt="Cerrar sesión">
        </button>
    </form>
</header>