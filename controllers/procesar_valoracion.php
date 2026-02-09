<?php
session_start();
require_once '../config/iniciar_session.php';
require_once '../models/Rating.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Validar el token CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Error CSRF: Token inválido');
    }

    // 2. Validar que el usuario esté logueado
    if (!isset($_SESSION['id_usuario'])) {
        header('Location: ../index.php?userSession=login');
        exit();
    }

    // 3. Recoger y validar los datos del formulario
    $id_producto = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
    $puntuacion = filter_input(INPUT_POST, 'puntuacion', FILTER_VALIDATE_INT);
    $comentario = trim($_POST['comentario'] ?? '');
    $id_usuario = $_SESSION['id_usuario'];

    if ($id_producto && $puntuacion >= 1 && $puntuacion <= 5) {
        // 4. Guardar la valoración en la base de datos
        $ratingModel = new Rating($pdo);
        $ratingModel->create($id_producto, $id_usuario, $puntuacion, $comentario);

        // 5. Redirigir de vuelta a la página del producto
        header('Location: ../index.php?id_producto=' . $id_producto);
        exit();
    } else {
        // Si los datos no son válidos, redirigir con un mensaje de error (opcional)
        $_SESSION['error_message'] = "Error al procesar la valoración.";
        header('Location: ../index.php?id_producto=' . $id_producto);
        exit();
    }
} else {
    // Si no es una petición POST, simplemente redirigir al inicio
    header('Location: ../index.php');
    exit();
}
?>