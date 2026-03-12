<?php

namespace App\Controllers;

class BaseController
{
    protected function view($view, $data = [])
    {
        global $pdo, $nombre_sitio, $cache_version, $rutas, $tipo_usuario, $simbolo_moneda, $stock_bajo_umbral;

        // --- ARQUITECTURA "BUFFET LIBRE" ---
        // ¡LA CORRECCIÓN DEFINITIVA! Comprobamos contra el nombre completo del rol.
        if ($tipo_usuario === 'administrador') {
            // 1. Preparamos SIEMPRE el "plato base" de estadísticas para el menú.
            require_once __DIR__ . '/../../models/User.php';
            require_once __DIR__ . '/../../models/Product.php';
            require_once __DIR__ . '/../../models/Order.php';

            $userModel = new \User($pdo);
            $productModel = new \Product($pdo);
            $orderModel = new \Order($pdo);

            $base_admin_stats = [
                'total_products'     => $productModel->getTotalProducts(),
                'pending_orders'     => $orderModel->getOrdersCountByStatus('pendiente'),
                'total_users'        => $userModel->countUsers(),
                'low_stock_products' => $productModel->getLowStockCount($stock_bajo_umbral)
            ];

            // 2. Si el controlador específico (el "Chef") trae "ingredientes extra", los añadimos al plato.
            $extras = $data['admin_stats_extra'] ?? [];
            $data['admin_stats'] = array_merge($base_admin_stats, $extras);
        }

        // Fusionamos los datos globales para que estén disponibles en todas las vistas.
        $data = array_merge($data, [
            'nombre_sitio'    => $nombre_sitio,
            'cache_version'   => $cache_version,
            'rutas'           => $rutas,
            'tipo_usuario'    => $tipo_usuario,
            'simbolo_moneda'  => $simbolo_moneda
        ]);

        // Extraemos el array a variables individuales para que la vista pueda usarlas (ej: $productos)
        extract($data);

        // Capturamos el contenido de la vista específica
        ob_start();
        require __DIR__ . "/../../views/{$view}.php";
        $content = (string)ob_get_clean();

        // Y finalmente, cargamos la plantilla principal que envuelve todo
        require __DIR__ . '/../../views/layout.php';
    }
}
