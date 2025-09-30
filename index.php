<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Supermarket Arthur - Encuentra los mejores productos al mejor precio.">
    <meta name="keywords" content="supermercado, compras, productos, ofertas">
    <title>Supermarket Arthur</title>
    <link rel="icon" href="./IMG/logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="./CSS/styles.css">
</head>

<body>
    <?php
    session_start();
    include('./sesion_bbdd/iniciar_session.php');

    // Funciones para devolver ruta de archivos según tipo de usuario
    function getCabeceraPath($tipo_usuario)
    {
        switch ($tipo_usuario) {
            case 'a':
                return './cabecera/cabecera_administrador.php';
            case 'u':
                return './cabecera/cabecera_logueado.php';
            default:
                return './cabecera/cabecera_invitado.php';
        }
    }

    function getMenuPath($tipo_usuario)
    {
        switch ($tipo_usuario) {
            case 'a':
                return './menu/menu_administrador.php';
            case 'u':
                return './menu/menu_logueado.php';
            default:
                return './menu/menu_invitado.php';
        }
    }

    function getCentroPath($tipo_usuario, $vista = null)
    {
        if ($tipo_usuario === 'a') {
            return './centro/centro_administrador.php';
        } elseif ($tipo_usuario === 'u') {
            return './centro/centro_logueado.php';
        } else {
            $vistas = [
                'login' => './centro/form_login.php',
                'registro' => './centro/form_registro.php',
                'productos' => './centro/categorias_productos.php',
                'ofertas' => './centro/ofertas.php',
                'sobre_nosotros' => './centro/sobre_nosotros.php',
                'soporte' => './centro/soporte.php',
                'contacto' => './centro/contacto.php'
            ];
            return $vistas[$vista] ?? './centro/centro_invitado.php';
        }
    }

    function getPiePath($tipo_usuario)
    {
        switch ($tipo_usuario) {
            case 'a':
                return './pie/pie_administrador.php';
            case 'u':
                return './pie/pie_logueado.php';
            default:
                return './pie/pie_invitado.php';
        }
    }

    // Obtener tipo usuario
    $tipo_usuario = $_SESSION['tipo_usu'] ?? 'i'; // 'i' invitado por defecto

    // Incluir cabecera
    include(getCabeceraPath($tipo_usuario));

    include(getMenuPath($tipo_usuario));

    include(getCentroPath($tipo_usuario, $_GET['vista'] ?? null));

    include(getPiePath($tipo_usuario));
    ?>

    <script type="module" src="./JS/funciones.js"></script>
</body>

</html>