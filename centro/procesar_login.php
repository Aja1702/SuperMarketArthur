<?php
session_start();
include '../sesion_bbdd/iniciar_session.php'; // tu archivo de conexión

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $errores = '';

    // ✔ Validar que el email no esté vacío y que sea un email válido
    if ($email === '' || $password === '') {
        $errores = 'Campos obligatorios';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores = 'Debe insertar un correo válido';
    } else {
        $stmt = $conn->prepare("SELECT id_usuario, pass, tipo_usu FROM usuarios WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($usuario = $result->fetch_assoc()) {
            $passBD = $usuario['pass'];
            $esHash = (strpos($passBD, '$') === 0); // Si empieza por '$', probablemente ya es un hash

            if (
                // 1. Si la contraseña en la BD es texto plano y coincide
                !$esHash && $password === $passBD
            ) {
                // Migramos al hash seguro
                $nuevoHash = password_hash($password, PASSWORD_DEFAULT);
                $updStmt = $conn->prepare("UPDATE usuarios SET pass=? WHERE id_usuario=?");
                $updStmt->bind_param('si', $nuevoHash, $usuario['id_usuario']);
                $updStmt->execute();
                $updStmt->close();

                // Login exitoso
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['tipo_usu'] = $usuario['tipo_usu'];
                header("Location: /SuperMarketArthur/");
                exit();

                // 2. Si la contraseña ya es un hash y verifica correctamente
            } elseif ($esHash && password_verify($password, $passBD)) {
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['tipo_usu'] = $usuario['tipo_usu'];
                header("Location: /SuperMarketArthur/");
                exit();
            } else {
                $errores = 'Contraseña incorrecta';
            }
        } else {
            $errores = 'Usuario no encontrado';
        }
        $stmt->close();
    }
    $conn->close();

    // Muestra mensaje de error aquí si lo hay
    if ($errores !== '') {
        echo "
            <script>
                alert('$errores'); 
                window.history.back();
            </script>
            ";
        exit();
    }
}
