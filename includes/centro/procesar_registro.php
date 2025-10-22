<?php
session_start();
include '../sesion_bbdd/iniciar_session.php'; // tu conexión $conn

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir y limpiar datos
    $nombre      = trim($_POST['nombre'] ?? '');
    $apellido1   = trim($_POST['apellido1'] ?? '');
    $apellido2   = trim($_POST['apellido2'] ?? '');
    $password    = $_POST['password'] ?? '';
    $password2   = $_POST['confirm_password'] ?? '';
    $provincia   = trim($_POST['provincia'] ?? '');
    $localidad   = trim($_POST['localidad'] ?? '');
    $cp          = trim($_POST['cp'] ?? '');
    $calle       = trim($_POST['calle'] ?? '');
    $numero      = trim($_POST['numero'] ?? '');
    $telefono    = trim($_POST['tlfn'] ?? '');
    $email       = strtolower(trim($_POST['email'] ?? ''));
    $tipo_doc    = trim($_POST['tipo_doc'] ?? '');
    $num_doc     = trim($_POST['caja_dni_nie'] ?? '');
    $fecha_nac   = trim($_POST['fecha_nacer'] ?? '');
    

    $errores = [];

    // Validaciones mínimas (puedes añadir más)
    if (
        $nombre === '' || $apellido1 === '' || $provincia === '' || $localidad === '' ||
        $cp === '' || $calle === '' || $numero === '' || $email === '' || $tipo_doc === '' ||
        $num_doc === '' || $fecha_nac === '' || $password === '' || $password2 === ''
    ) {
        $errores[] = "Todos los campos obligatorios deben estar completos (32).";
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

    // Comprobar duplicados
    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email=? OR num_doc=?");
    $stmt->bind_param("ss", $email, $num_doc);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errores[] = "Ya existe un usuario con ese correo o documento. (53)";
    }
    $stmt->close();

    if (count($errores) === 0) {
        // Hash de la contraseña
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // INSERT seguro
        $stmt = $conn->prepare("INSERT INTO usuarios 
            (nombre, apellido1, apellido2, pass, provincia, localidad, cp, calle, numero, telefono, email, tipo_doc, num_doc, fecha_nacimiento, tipo_usu)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'u')");

        $stmt->bind_param("ssssssssssssss", $nombre, $apellido1, $apellido2, $hash, $provincia, $localidad, $cp, $calle, $numero, $telefono, $email, $tipo_doc, $num_doc, $fecha_nac);

        if ($stmt->execute()) {
            // Opcional, inicia sesión directamente
            $_SESSION['id_usuario'] = $stmt->insert_id;
            $_SESSION['tipo_usu'] = 'u';
            header("Location: /SuperMarketArthur/");
            exit();
        } else {
            $errores[] = "Error al registrar usuario.";
        }
        $stmt->close();
    }
    $conn->close();

    // Devuelve errores en el formulario
    if (!empty($errores)) {
        foreach ($errores as $e) {
            echo "<p style='color:red;'>" . htmlspecialchars($e) . "</p>";
        }
        echo "<a href='javascript:history.back()'>Volver</a>";
        exit();
    }
}