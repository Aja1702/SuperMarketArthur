<?php

namespace App\Services\Payment;

require_once __DIR__ . '/../../../vendor/autoload.php';

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
        $this->secretKey = getenv('STRIPE_SECRET_KEY') ?: ($_ENV['STRIPE_SECRET_KEY'] ?? '');
        $this->publishableKey = getenv('STRIPE_PUBLISHABLE_KEY') ?: ($_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '');
        
        $this->isTestMode = (strpos($this->secretKey, 'sk_test_') === 0);
        
        if ($this->secretKey) {
            Stripe::setApiKey($this->secretKey);
            Stripe::setApiVersion('2023-10-16');
        }
    }

    public function getPublishableKey(): string
    {
        return $this->publishableKey;
    }

    public function isTestMode(): bool
    {
        return $this->isTestMode;
    }

    public function createCheckoutSession(array $cartItems, float $total, string $currency = 'eur'): Session
    {
        $lineItems = [];

        foreach ($cartItems as $item) {
            $productData = [
                'name' => $item['nombre_producto'] ?? 'Producto'
            ];
            
            $description = $item['descripcion'] ?? '';
            if (!empty($description)) {
                $productData['description'] = $description;
            }
            
            if (!empty($item['url_imagen'])) {
                $productData['images'] = [($_ENV['BASE_URL'] ?? 'http://localhost/SuperMarketArthur') . $item['url_imagen']];
            }
            
            $lineItems[] = [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => $productData,
                    'unit_amount' => (int)($item['precio'] * 100),
                ],
                'quantity' => (int)($item['cantidad'] ?? 1),
            ];
        }

        $baseUrl = $_ENV['BASE_URL'] ?? 'http://localhost/SuperMarketArthur';

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

    public function retrieveSession(string $sessionId): Session
    {
        return Session::retrieve($sessionId);
    }

    public function verifyPayment(string $sessionId): bool
    {
        try {
            $session = $this->retrieveSession($sessionId);
            
            // Debug info
            error_log('Stripe session status: payment_status=' . ($session->payment_status ?? 'null') . ', status=' . ($session->status ?? 'null'));
            
            return $session->payment_status === 'paid';
        } catch (ApiErrorException $e) {
            error_log('Stripe Payment Error: ' . $e->getMessage());
            return false;
        }
    }

    public function createCustomer(string $email, string $name): \Stripe\Customer
    {
        return \Stripe\Customer::create([
            'email' => $email,
            'name' => $name,
        ]);
    }
}
