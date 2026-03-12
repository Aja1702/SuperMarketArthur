<?php

namespace App\Controllers\Shop;

use App\Controllers\BaseController;
use App\Services\CartService;

/**
 * Ejemplo de Controlador Refactorizado
 * 
 * La diferencia principal es que ahora delegamos la lógica de negocio
 * a CartService, manteniendo el controlador limpio y enfocado en
 * manejar solicitudes HTTP.
 */
class CartControllerExample extends BaseController
{
    private $cartService;

    public function __construct()
    {
        parent::__construct();
        $this->cartService = new CartService(new \App\Models\Cart());
    }

    /**
     * Agregar producto al carrito
     */
    public function add()
    {
        try {
            $productId = $_POST['product_id'] ?? null;
            $quantity = (int)($_POST['quantity'] ?? 1);
            $userId = $_SESSION['user_id'] ?? null;

            // Delegar a CartService - mejor testing, reusabilidad
            $this->cartService->addItem($productId, $quantity, $userId);

            return $this->json(['success' => true, 'message' => 'Producto agregado']);
        } catch (\Exception $e) {
            http_response_code(400);
            return $this->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Obtener total del carrito
     */
    public function getTotal()
    {
        $userId = $_SESSION['user_id'] ?? null;
        $total = $this->cartService->getTotal($userId);

        return $this->json(['total' => $total]);
    }
}
