<div class="container-info">
    <div class="card-info-premium">
        <h2 class="titulo-seccion-premium">Finalizar Compra</h2>

        <div class="checkout-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 3rem; margin-top: 2rem;">

            <!-- Columna Izquierda: Formulario de Envío -->
            <div class="checkout-form">
                <h3 style="border-bottom: 2px solid var(--azul-claro); padding-bottom: 1rem; margin-bottom: 2rem;">Información de Envío</h3>

                <form action="/SuperMarketArthur/checkout" method="POST" class="form-contacto-premium">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="input-group">
                        <label for="nombre_completo">Nombre Completo</label>
                        <input type="text" id="nombre_completo" name="nombre_completo" required>
                    </div>

                    <div class="input-group">
                        <label for="direccion">Dirección</label>
                        <input type="text" id="direccion" name="direccion" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="input-group">
                            <label for="ciudad">Ciudad</label>
                            <input type="text" id="ciudad" name="ciudad" required>
                        </div>
                        <div class="input-group">
                            <label for="codigo_postal">Código Postal</label>
                            <input type="text" id="codigo_postal" name="codigo_postal" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" required>
                    </div>

                    <button type="submit" class="btn-enviar-premium">Confirmar y Pagar</button>
                </form>
            </div>

            <!-- Columna Derecha: Resumen del Pedido -->
            <div class="order-summary" style="background: var(--gris-fondo); padding: 2rem; border-radius: 16px;">
                <h3 style="border-bottom: 2px solid var(--azul-claro); padding-bottom: 1rem; margin-bottom: 2rem;">Resumen del Pedido</h3>

                <?php if (empty($cartItems)): ?>
                    <div class="empty-cart-message">
                        <p>Tu carrito está vacío. Añade productos para poder finalizar la compra.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item-summary" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <span><?php echo htmlspecialchars($item['nombre_producto']); ?> (x<?php echo $item['cantidad']; ?>)</span>
                            <strong><?php echo number_format($item['precio'] * $item['cantidad'], 2, ',', '.') . htmlspecialchars($simbolo_moneda); ?></strong>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php 
                $base_imponible = $cartTotal / 1.10; // Asumiendo un 10% de IVA como promedio
                $iva_calculado = $cartTotal - $base_imponible;
                ?>

                <div class="checkout-totals" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: #64748b;">
                        <span>Subtotal (Base Imponible)</span>
                        <span><?php echo number_format($base_imponible, 2, ',', '.') . htmlspecialchars($simbolo_moneda); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: #64748b;">
                        <span>IVA (10%)</span>
                        <span><?php echo number_format($iva_calculado, 2, ',', '.') . htmlspecialchars($simbolo_moneda); ?></span>
                    </div>
                    <div class="cart-total" style="display: flex; flex-direction: column; align-items: flex-end; border-top: 2px solid var(--azul-primario); padding-top: 1rem;">
                        <div style="display: flex; justify-content: space-between; width: 100%; font-size: 1.5rem; font-weight: 700; color: var(--negro-titulos);">
                            <span>TOTAL</span>
                            <strong><?php echo $cartTotalFormatted; ?></strong>
                        </div>
                        <small style="color: var(--azul-primario); font-weight: 600; font-size: 0.85rem; margin-top: 0.25rem;">IVA INCLUIDO</small>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
