<?php
session_start();
require_once '../config/iniciar_session.php';
require_once '../models/User.php';
require_once '../models/Cart.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $errores = '';

    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Error CSRF: Token inválido');
    }

    if (empty($email) || empty($password)) {
        $errores = 'Campos obligatorios';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores = 'Debe insertar un correo válido';
    } else {
        $userModel = new User($pdo);
        $user = $userModel->login($email, $password);

        if ($user) {
            // Regenerar ID de sesión para prevenir fijación de sesión
            session_regenerate_id(true);

            // Guardar datos de usuario en la sesión
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['tipo_usu'] = $user['tipo_usu'];
            $_SESSION['usuario_nombre'] = $user['nombre'];

            // Transferir carrito de sesión a la base de datos
            $cart = new Cart($pdo, $user['id_usuario']);
            $cart->mergeSessionCartToDb();

            // Redirección inteligente
            if (isset($_SESSION['return_to']) && $_SESSION['return_to'] === 'checkout.php') {
                unset($_SESSION['return_to']); // Limpiar la variable de sesión
                header("Location: ../checkout.php");
            } else {
                header("Location: /SuperMarketArthur/");
            }
            exit();

        } else {
            $errores = 'Credenciales incorrectas. Verifique su email y contraseña.';
        }
    }

    if (!empty($errores)) {
        // Manejo de errores con redirección y mensaje en sesión
        $_SESSION['error_message'] = $errores;
        header('Location: ../login.php'); // Redirige de nuevo al login
        exit();
    }
}
?>