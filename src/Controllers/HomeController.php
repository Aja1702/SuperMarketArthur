<?php

namespace App\Controllers;

class HomeController
{
    public function index()
    {
        // 1. Hacemos accesibles las variables globales que necesitamos
        global $pdo, $simbolo_moneda, $nombre_sitio, $cache_version, $rutas, $tipo_usuario;

        // 2. Cargar los modelos necesarios
        require_once __DIR__ . '/../Models/Product.php';
        require_once __DIR__ . '/../Models/Favorite.php'; // Incluimos el nuevo modelo

        // 3. Preparar los datos específicos de esta página
        $productModel = new \Product($pdo);
        $productosDestacados = $productModel->getFeaturedProducts(10);

        // 4. Comprobar el estado de "favorito" para cada producto si el usuario está logueado
        if (isset($_SESSION['id_usuario']) && is_array($productosDestacados)) {
            $favoriteModel = new \Favorite($pdo);
            $id_usuario = $_SESSION['id_usuario'];

            foreach ($productosDestacados as &$producto) { // Usamos la referencia (&) para modificar el array directamente
                $producto['is_favorite'] = $favoriteModel->isFavorite($id_usuario, $producto['id_producto']);
            }
            unset($producto); // Rompemos la referencia al final del bucle
        }

        // 5. Preparar los datos para la vista
        $data = [
            'productosDestacados' => $productosDestacados,
            'nombre_sitio' => $nombre_sitio,
            'cache_version' => $cache_version,
            'rutas' => $rutas,
            'tipo_usuario' => $tipo_usuario,
            'simbolo_moneda' => $simbolo_moneda
        ];

        // 6. Cargar la vista y pasarle los datos
        $this->view('home', $data);
    }

    /**
     * Carga una vista con su plantilla principal.
     */
    protected function view($view, $data = [])
    {
        extract($data);

        ob_start();
        require_once __DIR__ . "/../../views/{$view}.php";
        $content = (string)ob_get_clean();

        require_once __DIR__ . '/../../views/layout.php';
    }
}
