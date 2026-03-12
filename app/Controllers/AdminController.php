<?php

namespace App\Controllers;

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/Order.php';

class AdminController extends BaseController
{
    private $userModel;
    private $productModel;
    private $orderModel;

    public function __construct()
    {
        if (!isset($_SESSION['tipo_usu']) || $_SESSION['tipo_usu'] !== 'a') {
            header('Location: /SuperMarketArthur/login');
            exit();
        }

        global $pdo;
        $this->userModel = new \User($pdo);
        $this->productModel = new \Product($pdo);
        $this->orderModel = new \Order($pdo);
    }

    /**
     * Muestra el dashboard principal del administrador.
     */
    public function index()
    {
        // El BaseController prepara el "plato base" para el menú.
        // Aquí solo cocinamos los "ingredientes extra" para la vista del dashboard.
        $basicStats = $this->userModel->getAdminDashboardStats();

        $data = [
            // Pasamos solo los datos que el BaseController NO conoce.
            'admin_stats_extra' => [
                'ingresos_totales'   => $this->orderModel->getTotalRevenue(),
                'pedidos_retrasados'   => $basicStats['delayed_orders'] ?? 0,
            ],
            // También pasamos la lista de últimos pedidos, que es específica de esta página.
            'ultimos_pedidos' => $this->orderModel->getRecentOrders(5)
        ];

        // La función view() en BaseController fusionará estos 'extras' con el plato base.
        $this->view('admin/dashboard', $data);
    }
}
