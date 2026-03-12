<?php

namespace App\Controllers;

require_once __DIR__ . '/../../models/Config.php';
use Config;

class AdminConfigController
{
    public function index()
    {
        global $pdo;

        if (!isset($_SESSION['tipo_usu']) || $_SESSION['tipo_usu'] !== 'a') {
            header('Location: /SuperMarketArthur/login');
            exit();
        }

        $configModel = new Config($pdo);
        $configuraciones = $configModel->getAll();

        $this->view('admin/config', ['configuraciones' => $configuraciones]);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['tipo_usu']) || $_SESSION['tipo_usu'] !== 'a') {
            http_response_code(403);
            die('Acceso denegado.');
        }

        global $pdo;

        if (isset($_POST['config']) && is_array($_POST['config'])) {
            $configData = $_POST['config'];
            $sanitizedData = [];
            foreach ($configData as $key => $value) {
                if (preg_match('/^[A-Z0-9_]+$/', $key)) {
                    $sanitizedData[$key] = trim(htmlspecialchars($value));
                }
            }

            $configModel = new Config($pdo);
            $success = $configModel->update($sanitizedData);

            if ($success) {
                $_SESSION['success_message'] = '¡Configuración guardada con éxito!';
            } else {
                $_SESSION['error_message'] = 'Error al guardar la configuración.';
            }
        } else {
            $_SESSION['error_message'] = 'No se recibieron datos para actualizar.';
        }

        header('Location: /SuperMarketArthur/admin/config');
        exit;
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
