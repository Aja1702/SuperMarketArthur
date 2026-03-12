<?php

namespace App\Controllers\Admin;

class AdminUsuariosController
{
    public function __construct()
    {
        // Proteger todo el controlador para que solo los administradores puedan acceder
        if (!isset($_SESSION['tipo_usu']) || $_SESSION['tipo_usu'] !== 'a') {
            header('Location: /SuperMarketArthur/login');
            exit();
        }
    }

    /**
     * Muestra la lista de todos los usuarios.
     */
    public function index()
    {
        // Lógica para mostrar la lista de usuarios
        $this->view('admin/usuarios/listado_usuarios');
    }

    /**
     * Carga una vista con su plantilla principal.
     */
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
