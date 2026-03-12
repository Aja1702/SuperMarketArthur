<?php

namespace App\Controllers;

require_once __DIR__ . '/../../models/Rating.php';
use Rating;

class RatingController
{
    public function submitRating()
    {
        global $pdo;

        // 1. Validar el método y la sesión de usuario
        if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION['id_usuario'])) {
            header('Location: /SuperMarketArthur/');
            exit();
        }

        // 2. Validar el token CSRF
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die('Error CSRF: Token inválido');
        }

        // 3. Recoger y validar los datos del formulario
        $id_producto = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
        $puntuacion = filter_input(INPUT_POST, 'puntuacion', FILTER_VALIDATE_INT);
        $comentario = trim($_POST['comentario'] ?? '');
        $id_usuario = $_SESSION['id_usuario'];

        $redirect_url = '/SuperMarketArthur/producto?id_producto=' . $id_producto;

        if ($id_producto && $puntuacion >= 1 && $puntuacion <= 5) {
            $ratingModel = new Rating($pdo);
            $ratingModel->create($id_producto, $id_usuario, $puntuacion, $comentario);
        } else {
            $_SESSION['error_message'] = "Error al procesar la valoración.";
        }

        // 5. Redirigir de vuelta a la página del producto
        header('Location: ' . $redirect_url);
        exit();
    }
}
