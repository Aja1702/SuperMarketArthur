<?php

namespace App\Services\Payment;

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../src/Utilities/EnvLoader.php';

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class StripePayment
{
    private $secretKey;
    private $publishableKey;
    private $isTestMode;

    public function __construct()
    {
        // Cargar variables de entorno
        \App\Utilities\EnvLoader::load();
        
        // Obtener claves de Stripe del .env
        $this->secretKey = $_ENV['STRIPE_SECRET_KEY'] ?? '';
        $this->publishableKey = $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '';
        
        // Verificar si estamos en modo prueba
        $this->isTestMode = (strpos($this->secretKey, 'sk_test_') === 0);
        
        if ($this->secretKey) {
            Stripe::setApiKey($this->secretKey);
            Stripe::setApiVersion('2023-10-16');
        }
    }

    /**
     * Obtiene la clave pública
     */
    public function getPublishableKey(): string
    {
        return $this->publishableKey;
    }

    /**
     * Verifica si está en modo prueba
     */
    public function isTestMode(): bool
    {
        return $this->isTestMode;
    }

    /**
     * Crea una sesión de pago de Stripe
     * @throws ApiErrorException
     */
    public function createCheckoutSession(array $cartItems, float $total, string $currency = 'eur'): Session
    {
        $lineItems = [];

        foreach ($cartItems as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => $item['nombre_producto'] ?? 'Producto',
                        'description' => $item['descripcion'] ?? '',
                        'images' => !empty($item['url_imagen']) 
                            ? [($_ENV['BASE_URL'] ?? 'http://localhost/SuperMarketArthur') . $item['url_imagen']]
                            : [],
                    ],
                    'unit_amount' => (int)($item['precio'] * 100),
                ],
                'quantity' => (int)($item['cantidad'] ?? 1),
            ];
        }

        // URL base
        $baseUrl = $_ENV['BASE_URL'] ?? 'http://localhost/SuperMarketArthur';

        // Crear sesión de checkout
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $baseUrl . '/checkout/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $baseUrl . '/checkout?canceled=1',
            'metadata' => [
                'user_id' => $_SESSION['id_usuario'] ?? 'guest',
            ],
        ]);

        return $session;
    }

    /**
     * Recupera una sesión de checkout
     * @throws ApiErrorException
     */
    public function retrieveSession(string $sessionId): Session
    {
        return Session::retrieve($sessionId);
    }

    /**
     * Verifica el estado del pago
     */
    public function verifyPayment(string $sessionId): bool
    {
        try {
            $session = $this->retrieveSession($sessionId);
            return $session->payment_status === 'paid';
        } catch (ApiErrorException $e) {
            error_log('Stripe Payment Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea un cliente en Stripe (opcional)
     */
    public function createCustomer(string $email, string $name): \Stripe\Customer
    {
        return \Stripe\Customer::create([
            'email' => $email,
            'name' => $name,
        ]);
    }
}
