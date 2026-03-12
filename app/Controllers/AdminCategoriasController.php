<?php

namespace App\Controllers;

class AdminCategoriasController
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
     * Muestra la lista de todas las categorías.
     */
    public function index()
    {
        // Lógica para mostrar la lista de categorías
        $this->view('admin/categorias/listado_categorias');
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
        $content = ob_get_clean();

        require_once __DIR__ . '/../../views/layout.php';
    }
}
