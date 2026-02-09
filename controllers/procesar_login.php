<?php
session_start();
require_once '../config/iniciar_session.php';
require_once '../models/User.php';
require_once '../models/Cart.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $errores = '';
    $ipAddress = $_SERVER['REMOTE_ADDR']; // Obtener la IP del usuario

    $userModel = new User($pdo);

    // 1. Comprobar si la IP está bloqueada ANTES de hacer nada más
    if ($userModel->checkLoginAttempts($ipAddress)) {
        $_SESSION['error_message'] = 'Has excedido el número de intentos de inicio de sesión. Por favor, inténtalo de nuevo en ' . User::LOGIN_ATTEMPT_TIME_WINDOW . ' minutos.';
        header('Location: ../index.php?userSession=login');
        exit();
    }

    // Validación del token CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Error CSRF: Token inválido');
    }

    if (empty($email) || empty($password)) {
        $errores = 'Campos obligatorios';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores = 'Debe insertar un correo válido';
    } else {
        $user = $userModel->login($email, $password);

        if ($user) {
            // 3. Si el login es exitoso, limpiar los intentos
            $userModel->clearLoginAttempts($ipAddress);

            session_regenerate_id(true);
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['tipo_usu'] = $user['tipo_usu'];
            $_SESSION['usuario_nombre'] = $user['nombre'];

            $cart = new Cart($pdo, $user['id_usuario']);
            $cart->mergeSessionCartToDb();

            if (isset($_SESSION['return_to']) && $_SESSION['return_to'] === 'checkout.php') {
                unset($_SESSION['return_to']);
                header("Location: ../checkout.php");
            } else {
                header("Location: /SuperMarketArthur/");
            }
            exit();

        } else {
            // 2. Si el login falla, registrar el intento
            $userModel->addLoginAttempt($ipAddress);
            $errores = 'Credenciales incorrectas. Verifique su email y contraseña.';
        }
    }

    if (!empty($errores)) {
        $_SESSION['error_message'] = $errores;
        header('Location: ../index.php?userSession=login');
        exit();
    }
}
?>