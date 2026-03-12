<?php

namespace App\Controllers\Auth;

class AuthController
{
    public function showLoginForm()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $this->view('auth/login', [
            'token' => $_SESSION['csrf_token'],
            'error_message' => $_SESSION['error_message'] ?? null
        ]);
        unset($_SESSION['error_message']);
    }

    public function processLogin()
    {
        global $pdo;
        require_once __DIR__ . '/../../Models/User.php';
        require_once __DIR__ . '/../../Models/Cart.php';

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $userModel = new \User($pdo);

        if ($userModel->checkLoginAttempts($ipAddress)) {
            $_SESSION['error_message'] = 'Has excedido el número de intentos. Inténtalo de nuevo en ' . \User::LOGIN_ATTEMPT_TIME_WINDOW . ' minutos.';
            header('Location: /SuperMarketArthur/login');
            exit();
        }

        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die('Error CSRF: Token inválido');
        }

        if (empty($email) || empty($password)) {
            $_SESSION['error_message'] = 'Campos obligatorios.';
        } else {
            $user = $userModel->login($email, $password);

            if ($user) {
                $userModel->clearLoginAttempts($ipAddress);
                session_regenerate_id(true);
                $_SESSION['id_usuario'] = $user['id_usuario'];
                $_SESSION['tipo_usu'] = $user['tipo_usu'];
                $_SESSION['usuario_nombre'] = $user['nombre'];

                $cart = new \Cart($pdo, $user['id_usuario']);
                $cart->mergeSessionCartToDb();

                header("Location: /SuperMarketArthur/");
                exit();
            } else {
                $userModel->addLoginAttempt($ipAddress);
                $_SESSION['error_message'] = 'Credenciales incorrectas.';
            }
        }

        header('Location: /SuperMarketArthur/login');
        exit();
    }

    public function showRegisterForm()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $data = [
            'token' => $_SESSION['csrf_token'],
            'errors' => $_SESSION['registro_errores'] ?? []
        ];
        unset($_SESSION['registro_errores']);

        $this->view('auth/register', $data);
    }

    public function processRegister()
    {
        global $pdo;
        require_once __DIR__ . '/../../Models/User.php';
        require_once __DIR__ . '/../../Models/Cart.php';

        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die('Error CSRF: Token inválido');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $apellido1 = trim($_POST['apellido1'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = trim($_POST['password'] ?? '');
        $password2 = trim($_POST['confirm_password'] ?? '');

        $errores = [];

        if (empty($nombre) || empty($apellido1) || empty($email) || empty($password)) {
            $errores[] = "Los campos nombre, apellido y email son obligatorios.";
        }
        if ($password !== $password2) {
            $errores[] = "Las contraseñas no coinciden.";
        }

        if (empty($errores)) {
            $userModel = new \User($pdo);
            $user = $userModel->createUser($nombre, $apellido1, $email, $password); // Asumiendo que tienes un método createUser

            if ($user) {
                session_regenerate_id(true);
                $_SESSION['id_usuario'] = $user['id_usuario'];
                $_SESSION['tipo_usu'] = $user['tipo_usu'];
                $_SESSION['usuario_nombre'] = $user['nombre'];

                header("Location: /SuperMarketArthur/");
                exit();
            } else {
                $errores[] = "El email ya está en uso.";
            }
        }

        $_SESSION['registro_errores'] = $errores;
        header('Location: /SuperMarketArthur/registro');
        exit();
    }

    public function logout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /SuperMarketArthur/');
            exit();
        }

        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die('Error CSRF: Token inválido');
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        header('Location: /SuperMarketArthur/');
        exit();
    }

    protected function view($view, $data = [])
    {
        global $nombre_sitio, $cache_version, $rutas, $tipo_usuario, $simbolo_moneda;

        $data = array_merge($data, [
            'nombre_sitio' => $nombre_sitio,
            'cache_version' => $cache_version,
            'rutas' => $rutas,
            'tipo_usuario' => $tipo_usuario,
            'simbolo_moneda' => $simbolo_moneda
        ]);

        extract($data);

        ob_start();
        require_once __DIR__ . "/../../views/{$view}.php";
        $content = (string)ob_get_clean();

        require_once __DIR__ . '/../../views/layout.php';
    }
}
