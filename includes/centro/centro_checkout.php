<?php
if (!isset($_SESSION['id_usuario'])) {
    header("Location: /SuperMarketArthur/?userSession=login");
    exit();
}

include './config/iniciar_session.php';
include './models/Cart.php';
include './models/User.php';

$userId = $_SESSION['id_usuario'];
$userModel = new User($pdo);
$cartModel = new Cart($pdo, $userId);

// Obtener carrito del usuario
$cartItems = $cartModel->getItems();
$user = $userModel->getUserById($userId);

if (empty($cartItems)) {
    header("Location: /SuperMarketArthur/?vistaMenu=categorias_productos");
    exit();
}

// Calcular total
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['precio'] * $item['cantidad'];
}
?>

<div class="checkout-container">
    <h1>Finalizar Compra</h1>

    <div class="checkout-grid">
        <!-- Resumen del pedido -->
        <div class="order-summary">
            <h2>Resumen del Pedido</h2>
            <div class="cart-items">
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item">
                        <img src="./assets/img/productos/<?php echo htmlspecialchars($item['url_imagen'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($item['nombre_producto']); ?>">
                        <div class="item-details">
                            <h4><?php echo htmlspecialchars($item['nombre_producto']); ?></h4>
                            <p>Cantidad: <?php echo $item['cantidad']; ?></p>
                            <p>Precio: <?php echo number_format($item['precio'], 2); ?><?php echo htmlspecialchars($simbolo_moneda); ?></p>
                        </div>
                        <div class="item-total">
                            <?php echo number_format($item['precio'] * $item['cantidad'], 2); ?><?php echo htmlspecialchars($simbolo_moneda); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="order-total">
                <h3>Total: <?php echo number_format($total, 2); ?><?php echo htmlspecialchars($simbolo_moneda); ?></h3>
            </div>
        </div>

        <!-- Información de envío -->
        <div class="shipping-info">
            <h2>Información de Envío</h2>
            <div class="address-display">
                <p><strong><?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido1']); ?></strong></p>
                <p><?php echo htmlspecialchars($user['calle'] . ' ' . $user['numero']); ?></p>
                <p><?php echo htmlspecialchars($user['cp'] . ' ' . $user['localidad'] . ', ' . $user['provincia']); ?></p>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
                <p><?php echo htmlspecialchars($user['telefono']); ?></p>
            </div>
        </div>

        <!-- Método de pago -->
        <div class="payment-method">
            <h2>Método de Pago</h2>
            <form id="paymentForm" method="post" action="./controllers/procesar_pago.php">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                <div class="payment-options">
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="tarjeta" checked>
                        <span>Tarjeta de Crédito/Débito</span>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="paypal">
                        <span>PayPal</span>
                    </label>
                </div>

                <div id="cardDetails" class="card-details">
                    <div class="form-group">
                        <label for="cardNumber">Número de Tarjeta</label>
                        <input type="text" id="cardNumber" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="expiryDate">Fecha de Expiración</label>
                            <input type="text" id="expiryDate" name="expiry_date" placeholder="MM/AA" maxlength="5">
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="4">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cardName">Nombre en la Tarjeta</label>
                        <input type="text" id="cardName" name="card_name" placeholder="Juan Pérez">
                    </div>
                </div>

                <div id="paypalDetails" class="paypal-details" style="display: none;">
                    <p>Serás redirigido a PayPal para completar el pago de forma segura.</p>
                </div>

                <button type="submit" class="btn-pagar">Pagar <?php echo number_format($total, 2); ?><?php echo htmlspecialchars($simbolo_moneda); ?></button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const cardDetails = document.getElementById('cardDetails');
    const paypalDetails = document.getElementById('paypalDetails');

    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            if (this.value === 'tarjeta') {
                cardDetails.style.display = 'block';
                paypalDetails.style.display = 'none';
            } else {
                cardDetails.style.display = 'none';
                paypalDetails.style.display = 'block';
            }
        });
    });

    // Formatear número de tarjeta
    document.getElementById('cardNumber').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        let formatted = value.match(/.{1,4}/g)?.join(' ') || '';
        e.target.value = formatted;
    });

    // Formatear fecha de expiración
    document.getElementById('expiryDate').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        e.target.value = value;
    });
});
</script>
