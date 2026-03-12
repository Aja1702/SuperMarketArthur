<?php

namespace App\Controllers\Shop;

require_once __DIR__ . '/../../Models/Favorite.php';
use Favorite;

class FavoriteController
{
    private function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    public function toggle()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id_usuario'])) {
            return $this->jsonResponse(['success' => false, 'message' => 'Debes iniciar sesión para añadir favoritos.'], 401);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Método no permitido.'], 405);
        }

        // Validación de token CSRF
        if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            return $this->jsonResponse(['success' => false, 'message' => 'Error de seguridad CSRF. Acción no autorizada.'], 403);
        }

        $id_producto = $_POST['id_producto'] ?? null;
        if (!$id_producto) {
            return $this->jsonResponse(['success' => false, 'message' => 'ID de producto no proporcionado.'], 400);
        }

        global $pdo;
        $id_usuario = $_SESSION['id_usuario'];
        $favoriteModel = new Favorite($pdo);

        if ($favoriteModel->isFavorite($id_usuario, $id_producto)) {
            // Si ya es favorito, lo eliminamos
            $favoriteModel->remove($id_usuario, $id_producto);
            $this->jsonResponse(['success' => true, 'status' => 'removed']);
        } else {
            // Si no es favorito, lo añadimos
            $favoriteModel->add($id_usuario, $id_producto);
            $this->jsonResponse(['success' => true, 'status' => 'added']);
        }
    }
}
