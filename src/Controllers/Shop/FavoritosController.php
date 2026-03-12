<?php

namespace App\Controllers\Shop;

require_once __DIR__ . '/../../Models/Favorite.php';
use Favorite;

class FavoritosController
{
    public function index()
    {
        if (!isset($_SESSION['id_usuario'])) {
            header('Location: /SuperMarketArthur/login');
            exit();
        }

        global $pdo;
        $id_usuario = $_SESSION['id_usuario'];

        $favoriteModel = new Favorite($pdo);
        $favoritos = $favoriteModel->getByUser($id_usuario);

        $this->view('favoritos', ['favoritos' => $favoritos]);
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
