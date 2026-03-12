<?php

namespace App\Controllers;

class OfertasController
{
    public function index()
    {
        global $pdo, $simbolo_moneda;

        require_once __DIR__ . '/../../models/Product.php';

        $productModel = new \Product($pdo);
        // CORREGIDO: Usamos el método que obtiene los productos más recientes, que es la lógica original.
        $ofertas = $productModel->getFeaturedProducts(8);

        $this->view('ofertas', [
            'ofertas' => $ofertas,
            'simbolo_moneda' => $simbolo_moneda
        ]);
    }

    protected function view($view, $data = [])
    {
        global $nombre_sitio, $cache_version, $rutas, $tipo_usuario;

        $data = array_merge($data, [
            'nombre_sitio' => $nombre_sitio,
            'cache_version' => $cache_version,
            'rutas' => $rutas,
            'tipo_usuario' => $tipo_usuario
        ]);

        extract($data);

        ob_start();
        require_once __DIR__ . "/../../views/{$view}.php";
        $content = (string)ob_get_clean();

        require_once __DIR__ . '/../../views/layout.php';
    }
}
