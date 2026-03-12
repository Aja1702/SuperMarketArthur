<?php

namespace App\Controllers;

class ProductoController
{
    public function index()
    {
        global $pdo, $productos_por_pagina_config;

        require_once __DIR__ . '/../../models/Product.php';
        require_once __DIR__ . '/../../models/Favorite.php';
        $productModel = new \Product($pdo);

        $id_categoria = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
        $data = [];

        if ($id_categoria > 0) {
            $pagina_actual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            
            // ¡NUEVO!: Asegurarse de que productos_por_pagina NUNCA sea 0 o inferior para evitar colapsar el Catálogo.
            $productos_por_pagina = (isset($productos_por_pagina_config) && $productos_por_pagina_config > 0) ? $productos_por_pagina_config : 12;

            $offset = ($pagina_actual - 1) * $productos_por_pagina;
            $total_productos = $productModel->getTotalProductsByCategory($id_categoria);
            $total_paginas = ceil($total_productos / $productos_por_pagina);
            $productos = $productModel->getProductsByCategory($id_categoria, $productos_por_pagina, $offset) ?: [];
            $categoria = $productModel->getCategoryById($id_categoria);
            $nombre_categoria = $categoria['nombre_categoria'] ?? 'Categoría no encontrada';

            if (isset($_SESSION['id_usuario']) && is_array($productos)) {
                $favoriteModel = new \Favorite($pdo);
                foreach ($productos as &$producto) {
                    $producto['is_favorite'] = $favoriteModel->isFavorite($_SESSION['id_usuario'], $producto['id_producto']);
                }
            }

            $data = [
                'id_categoria' => $id_categoria,
                'productos' => $productos,
                'nombre_categoria' => $nombre_categoria,
                'total_paginas' => $total_paginas,
                'pagina_actual' => $pagina_actual
            ];
        } else {
            $categorias = $productModel->getCategories();
            $data = [
                'id_categoria' => 0,
                'categorias' => $categorias
            ];
        }

        $this->view('productos/catalogo', $data);
    }

    public function show()
    {
        global $pdo, $valoraciones_habilitadas;
        require_once __DIR__ . '/../../models/Product.php';
        require_once __DIR__ . '/../../models/Rating.php';
        require_once __DIR__ . '/../../models/Favorite.php';

        $id_producto = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id_producto) {
            header("Location: /SuperMarketArthur/productos");
            exit();
        }

        $productModel = new \Product($pdo);
        $producto = $productModel->getProductById($id_producto);

        if (!$producto) {
            header("Location: /SuperMarketArthur/productos");
            exit();
        }

        if (isset($_SESSION['id_usuario'])) {
            $favoriteModel = new \Favorite($pdo);
            $producto['is_favorite'] = $favoriteModel->isFavorite($_SESSION['id_usuario'], $id_producto);
        }

        $data = [
            'producto' => $producto,
            'valoraciones_habilitadas' => $valoraciones_habilitadas
        ];

        if ($valoraciones_habilitadas) {
            $ratingModel = new \Rating($pdo);
            $data['valoraciones'] = $ratingModel->getByProduct($id_producto);
        }

        $this->view('productos/detalle', $data);
    }

    protected function view($view, $data = [])
    {
        global $nombre_sitio, $cache_version, $rutas, $tipo_usuario, $simbolo_moneda, $productos_por_pagina_config;
        $data = array_merge($data, [
            'nombre_sitio' => $nombre_sitio,
            'cache_version' => $cache_version,
            'rutas' => $rutas,
            'tipo_usuario' => $tipo_usuario,
            'simbolo_moneda' => $simbolo_moneda,
            'productos_por_pagina_config' => $productos_por_pagina_config
        ]);
        extract($data);
        ob_start();
        require_once __DIR__ . "/../../views/{$view}.php";
        $content = (string)ob_get_clean();
        require_once __DIR__ . '/../../views/layout.php';
    }
}
