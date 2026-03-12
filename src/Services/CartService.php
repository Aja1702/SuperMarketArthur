<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;

/**
 * Servicio de Carrito de Compras
 * 
 * Encapsula toda la lógica de negocio relacionada con el carrito
 * Separa la lógica del controlador CartController
 */
class CartService
{
    private $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * Agregar producto al carrito
     */
    public function addItem($productId, $quantity = 1, $userId = null)
    {
        $product = new Product();
        $product = $product->find($productId);

        if (!$product) {
            throw new \Exception('Producto no encontrado');
        }

        if ($product['stock'] < $quantity) {
            throw new \Exception('Stock insuficiente');
        }

        return $this->cart->addItem($productId, $quantity, $userId);
    }

    /**
     * Obtener total del carrito
     */
    public function getTotal($userId = null)
    {
        $items = $this->cart->getItems($userId);
        $total = 0;

        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }

    /**
     * Vaciar carrito
     */
    public function clear($userId = null)
    {
        return $this->cart->clear($userId);
    }

    /**
     * Aplicar descuento
     */
    public function applyDiscount($code, $userId = null)
    {
        // Lógica para validar y aplicar cupones
        $discount = $this->validateCoupon($code);
        if ($discount) {
            return $this->cart->setDiscount($discount, $userId);
        }
        throw new \Exception('Cupón inválido');
    }

    private function validateCoupon($code)
    {
        // Implementar validación
        return null;
    }
}
