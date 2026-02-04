<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Supermarket Arthur - Encuentra los mejores productos al mejor precio." />
    <meta name="keywords" content="supermercado, compras, productos, ofertas" />
    <title>Supermarket Arthur</title>
    <link rel="icon" href="./assets/img/logo/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="./assets/css/styles.css" />
</head>

<body>
    <?php
    session_start();
    include('./config/iniciar_session.php');

    // Validar el tipo de usuario con control de errores
    $tipo_usu = $_SESSION['tipo_usu'] ?? 'invitado';

    $tipos_validos = ['a', 'u', 'i'];
    if (!in_array($tipo_usu, $tipos_validos)) {
        $tipo_usu = 'invitado'; // Valor por defecto si hay dato inválido
    }

    $tipo_usuario = $tipo_usu === 'a' ? 'administrador' : ($tipo_usu === 'u' ? 'usuario' : 'invitado');

    //Rutas a incluir según el tipo de usuario
    $rutas = [
        'administrador' => [
            'cabecera' => './includes/cabecera/cabecera_administrador.php',
            'menu' => './includes/menu/menu_administrador.php',
            'centro' => './includes/centro/centro_administrador.php',
            'pie' => './includes/pie/pie_administrador.php'
        ],
        'usuario' => [
            'cabecera' => './includes/cabecera/cabecera_logueado.php',
            'menu' => './includes/menu/menu_logueado.php',
            'centro' => './includes/centro/centro_logueado.php',
            'pie' => './includes/pie/pie_logueado.php'
        ],
        'invitado' => [
            'cabecera' => './includes/cabecera/cabecera_invitado.php',
            'menu' => './includes/menu/menu_invitado.php',
            'centro' => './includes/centro/centro_invitado.php',
            'pie' => './includes/pie/pie_invitado.php'
        ]
    ];

    // Incluir cabecera, menú, pie protegidos y centrados en arrays
    include($rutas[$tipo_usuario]['cabecera']);
    include($rutas[$tipo_usuario]['menu']);

    // Mostrar contenido basado en parámetros GET con validación segura
    $vistaValidaUser = ['login', 'registro', 'perfil', 'recuperar'];
    $vistaValidaMenu = ['categorias_productos', 'ofertas', 'sobre_nosotros', 'soporte', 'contacto'];

    if (isset($_GET['userSession']) && in_array($_GET['userSession'], $vistaValidaUser)) {
        $vista = $_GET['userSession'];
        include("./includes/centro/form_{$vista}.php");
    } elseif (isset($_GET['vistaMenu']) && in_array($_GET['vistaMenu'], $vistaValidaMenu)) {
        $vista = $_GET['vistaMenu'];
        include("./includes/centro/centro_{$vista}.php");
    } else {
        // Incluye la vista por defecto segun el rol
        include($rutas[$tipo_usuario]['centro']);
    }

    include($rutas[$tipo_usuario]['pie']);
    ?>
    <script type="module" src="./js/funciones.js"></script>
</body>

</html>