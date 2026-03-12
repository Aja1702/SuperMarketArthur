<div class="container-info">
    <div class="card-info-premium" style="text-align: center;">
        <h2 class="titulo-seccion-premium" style="color: #4CAF50;">¡Pedido Realizado con Éxito!</h2>
        <p style="font-size: 1.2rem; color: #475569; margin-top: -1.5rem; margin-bottom: 2rem;">
            Gracias por tu compra. Hemos recibido tu pedido y lo estamos procesando.
        </p>

        <div class="order-confirmation-details" style="background: var(--gris-fondo); padding: 2rem; border-radius: 16px; max-width: 600px; margin: 2rem auto; text-align: left;">
            <h4 style="border-bottom: 2px solid var(--azul-claro); padding-bottom: 1rem; margin-bottom: 2rem;">Resumen del Pedido #<?php echo htmlspecialchars($order['id_pedido']); ?></h4>

            <?php foreach ($order['items'] as $item): ?>
                <div class="cart-item-summary" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <span><?php echo htmlspecialchars($item['nombre_producto']); ?> (x<?php echo $item['cantidad']; ?>)</span>
                    <strong><?php echo number_format($item['precio_unitario'] * $item['cantidad'], 2, ',', '.') . htmlspecialchars($simbolo_moneda); ?></strong>
                </div>
            <?php endforeach; ?>

            <div class="cart-total" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0; font-size: 1.2rem;">
                <span>TOTAL</span>
                <strong><?php echo number_format($order['total'], 2, ',', '.') . htmlspecialchars($simbolo_moneda); ?></strong>
            </div>
        </div>

        <p style="color: #64748b;">Recibirás un correo electrónico con los detalles de tu pedido en breve.</p>

        <div style="margin-top: 3rem;">
            <a href="/SuperMarketArthur/productos" class="btn-enviar-premium" style="display: inline-block; text-decoration: none; padding: 1rem 2rem;">Seguir Comprando</a>
        </div>
    </div>
</div>
