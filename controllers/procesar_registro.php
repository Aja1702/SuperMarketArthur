
<?php

session_start();
include '../config/iniciar_session.php'; // tu conexión $conn


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Error CSRF: Token inválido');
    }

    // Recibir y limpiar datos
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido1 = trim($_POST['apellido1'] ?? '');
    $apellido2 = trim($_POST['apellido2'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['confirm_password'] ?? '';
    $provincia = trim($_POST['provincia'] ?? '');
    $localidad = trim($_POST['localidad'] ?? '');
    $cp = trim($_POST['cp'] ?? '');
    $calle = trim($_POST['calle'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $telefono = trim($_POST['tlfn'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $tipo_doc = trim($_POST['tipo_doc'] ?? '');
    $num_doc = trim($_POST['caja_dni_nie'] ?? '');
    $fecha_nac = trim($_POST['fecha_nacer'] ?? '');

    $errores = [];

    // Validaciones mínimas
    if (
    $nombre === '' || $apellido1 === '' || $provincia === '' || $localidad === '' ||
    $cp === '' || $calle === '' || $numero === '' || $email === '' || $tipo_doc === '' ||
    $num_doc === '' || $fecha_nac === '' || $password === '' || $password2 === ''
    ) {
        $errores[] = "Todos los campos obligatorios deben estar completos.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Email no válido.";
    }

    if ($password !== $password2) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    if (strlen($password) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres.";
    }

    // ✅ PDO: Comprobar duplicados
    $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ? OR num_doc = ?");
    $stmt->execute([$email, $num_doc]);
    if ($stmt->rowCount() > 0) {
        $errores[] = "Ya existe un usuario con ese correo o documento.";
    }

    if (empty($errores)) {
        // Hash de la contraseña
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // ✅ PDO: INSERT seguro
        $stmt = $pdo->prepare("INSERT INTO usuarios 
            (nombre, apellido1, apellido2, pass, provincia, localidad, cp, calle, numero, telefono, email, tipo_doc, num_doc, fecha_nacimiento, tipo_usu)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'u')");

        $stmt->execute([$nombre, $apellido1, $apellido2, $hash, $provincia, $localidad, $cp, $calle, $numero, $telefono, $email, $tipo_doc, $num_doc, $fecha_nac]);

        if ($pdo->lastInsertId()) {
            $id_usuario_nuevo = $pdo->lastInsertId();

            // --- TRANSFERIR CARRITO DE SESIÓN A LA BASE DE DATOS ---
            if (!empty($_SESSION['carrito'])) {
                try {
                    $pdo->beginTransaction();

                    // Crear carrito_temp
                    $stmtCart = $pdo->prepare("INSERT INTO carrito_temp (id_usuario) VALUES (?)");
                    $stmtCart->execute([$id_usuario_nuevo]);
                    $id_carrito = $pdo->lastInsertId();

                    // Insertar items
                    $stmtItem = $pdo->prepare("INSERT INTO carrito_items (id_carrito, id_producto, cantidad) VALUES (?, ?, ?)");
                    foreach ($_SESSION['carrito'] as $id_prod => $cant) {
                        $stmtItem->execute([$id_carrito, $id_prod, $cant]);
                    }

                    $pdo->commit();
                    unset($_SESSION['carrito']); // Limpiar sesión tras transferir
                }
                catch (Exception $e) {
                    if ($pdo->inTransaction())
                        $pdo->rollBack();
                }
            }

            // Inicia sesión directamente
            $_SESSION['id_usuario'] = $id_usuario_nuevo;
            $_SESSION['tipo_usu'] = 'u';
            $_SESSION['usuario_nombre'] = $nombre;

            header("Location: /SuperMarketArthur/");
            exit();
        }
        else {
            $errores[] = "Error al registrar usuario. Inténtalo de nuevo.";
        }
    }

    // Devolver errores al formulario
    if (!empty($errores)) {
        $_SESSION['registro_errores'] = $errores;
        header("Location: ../registro.php");
        exit();
    }
}
?>
