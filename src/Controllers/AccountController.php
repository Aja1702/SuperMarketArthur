<?php

namespace App\Controllers;

require_once __DIR__ . '/BaseController.php';

class AccountController extends BaseController
{
    /**
     * Muestra el panel de cuenta del usuario
     */
    public function index()
    {
        // Verificar que el usuario está autenticado
        if (!isset($_SESSION['id_usuario'])) {
            header('Location: /SuperMarketArthur/login');
            exit();
        }

        global $pdo;
        
        require_once __DIR__ . '/../Models/User.php';
        require_once __DIR__ . '/../Models/Order.php';
        
        $userModel = new \User($pdo);
        $orderModel = new \Order($pdo);
        
        $userId = $_SESSION['id_usuario'];
        
        // Obtener datos del usuario
        $user = $userModel->getUserById($userId);
        
        // Obtener pedidos recientes
        $orders = $orderModel->getOrdersByUserId($userId);
        
        $data = [
            'user' => $user,
            'orders' => $orders,
            'nombre_usuario' => $_SESSION['usuario_nombre'] ?? 'Usuario'
        ];
        
        $this->view('account/index', $data);
    }

    /**
     * Actualiza el perfil del usuario
     */
    public function updateProfile()
    {
        if (!isset($_SESSION['id_usuario'])) {
            header('Location: /SuperMarketArthur/login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /SuperMarketArthur/mi-cuenta');
            exit();
        }

        // Verificar CSRF token
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die('Error CSRF: Token inválido');
        }

        global $pdo;
        
        require_once __DIR__ . '/../Models/User.php';
        
        $userModel = new \User($pdo);
        
        $userId = $_SESSION['id_usuario'];
        
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido1' => trim($_POST['apellido1'] ?? ''),
            'apellido2' => trim($_POST['apellido2'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? '')
        ];
        
        if ($userModel->updateProfile($userId, $data)) {
            $_SESSION['success_message'] = 'Perfil actualizado correctamente';
        } else {
            $_SESSION['error_message'] = 'Error al actualizar el perfil';
        }
        
        header('Location: /SuperMarketArthur/mi-cuenta');
        exit();
    }

    /**
     * Cambia la contraseña del usuario
     */
    public function changePassword()
    {
        if (!isset($_SESSION['id_usuario'])) {
            header('Location: /SuperMarketArthur/login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /SuperMarketArthur/mi-cuenta');
            exit();
        }

        // Verificar CSRF token
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die('Error CSRF: Token inválido');
        }

        global $pdo;
        
        require_once __DIR__ . '/../Models/User.php';
        
        $userModel = new \User($pdo);
        
        $userId = $_SESSION['id_usuario'];
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['error_message'] = 'Todos los campos son obligatorios';
            header('Location: /SuperMarketArthur/mi-cuenta');
            exit();
        }
        
        if ($newPassword !== $confirmPassword) {
            $_SESSION['error_message'] = 'Las contraseñas no coinciden';
            header('Location: /SuperMarketArthur/mi-cuenta');
            exit();
        }
        
        if (strlen($newPassword) < 6) {
            $_SESSION['error_message'] = 'La contraseña debe tener al menos 6 caracteres';
            header('Location: /SuperMarketArthur/mi-cuenta');
            exit();
        }
        
        if ($userModel->changePassword($userId, $newPassword)) {
            $_SESSION['success_message'] = 'Contraseña actualizada correctamente';
        } else {
            $_SESSION['error_message'] = 'Error al cambiar la contraseña';
        }
        
        header('Location: /SuperMarketArthur/mi-cuenta');
        exit();
    }
}
