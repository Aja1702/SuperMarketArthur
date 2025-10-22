<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Supermarket Arthur - Encuentra los mejores productos al mejor precio.">
    <meta name="keywords" content="supermercado, compras, productos, ofertas">
    <title>Supermarket Arthur</title>
    <link rel="icon" href="./img/logo/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="./css/styles.css">

</head>

<body>
    <?php
    session_start();
    // Inicio y configuración de la sesión
    include('./config/iniciar_session.php');
    // Definir rutas y tipo de usuario
    $tipo_usu = $_SESSION['tipo_usu'] ?? null;

    $tipo_usuario = $tipo_usu === null ? 'invitado' : ($tipo_usu === 'a' ? 'administrador' : 'usuario');

    // Puedes definir arrays de rutas si necesitas mostrar variantes por tipo de usuario
    $cabeceras = [
        'administrador' => './includes/cabecera/cabecera_administrador.php',
        'usuario'       => './includes/cabecera/cabecera_logueado.php',
        'invitado'      => './includes/cabecera/cabecera_invitado.php'
    ];

    $menus = [
        'administrador' => './includes/menu/menu_administrador.php',
        'usuario'       => './includes/menu/menu_logueado.php',
        'invitado'      => './includes/menu/menu_invitado.php'
    ];

    $centros = [
        'administrador' => './includes/centro/centro_administrador.php',
        'usuario'       => './includes/centro/centro_logueado.php',
        'invitado'      => './includes/centro/centro_invitado.php'
    ];

    $pies = [
        'administrador' => './includes/pie/pie_administrador.php',
        'usuario'       => './includes/pie/pie_logueado.php',
        'invitado'      => './includes/pie/pie_invitado.php'
    ];

    // Incluimos cada parte según el tipo de usuario
    // Aprovecha la estructura DRY llamando archivos genéricos/reutilizables
    include($cabeceras[$tipo_usuario]);
    include($menus[$tipo_usuario]);

    // Vistas permitidas para gestión de usuario 
    // ( login, registro, perfil(_logueado), recuperar(_administrar) )
    $vistasUserSession_permitidas = ['login', 'registro', 'perfil', 'recuperar'];

    // Vistas permitidas para menú principal
    $vistasMenu_permitidas = ['categorias_productos', 'ofertas', 'sobre_nosotros', 'soporte', 'contacto'];

    //Comprobar si exite la variable 'userSession'en la URL y si su valor es una vista valida para gestión de usuario
    if (isset($_GET['userSession']) && in_array($_GET['userSession'], $vistasUserSession_permitidas)) {
        $vistaSession = $_GET['userSession'];
        include("./includes/centro/form_{$vistaSession}.php");
        // Si no hay una 'userSession' válida, comprobamos vistas del menú principal
    } else {
        // Obtenemos el parámetro 'vistaMenu' de la URL o cadena vacía si no existe
        $vista = isset($_GET['vistaMenu']) ? $_GET['vistaMenu'] : '';
        // Verificamos si la vista solicitada está en las permitidas en el menú principal
        if (in_array($vista, $vistasMenu_permitidas)) {
            $archivo = "./includes/centro/centro_{$vista}.php";
        } else {
            include($centros[$tipo_usuario]);
            // si no es válida, cargamos el centro por defecto según el tipo de usuario
        }
    }

    // Vistas permitidas para invitados y usuarios no autenticados
    if (isset($_GET['vistaMenu']) && in_array($_GET['vistaMenu'], $vistasMenu_permitidas)) {
        $vista = $_GET['vistaMenu'];
        include("./includes/centro/centro_{$vista}.php");
    }

    include($pies[$tipo_usuario]);
    ?>
    <script type="module" src="./js/funciones.js"></script>
</body>

</html>