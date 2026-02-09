/**
 * --- LÓGICA DEL CARRITO LATERAL (SIDE CART) ---
 */
document.addEventListener('DOMContentLoaded', function () {
    const cartOverlay = document.getElementById('cartOverlay');
    const cartPanel = document.getElementById('cartPanel');
    const openCartBtn = document.getElementById('openCart');
    const closeCartBtn = document.getElementById('closeCart');
    const cartContent = document.getElementById('cartContent');
    const cartTotalAmount = document.getElementById('cartTotalAmount');

    // Función Abrir Carrito
    function openCart() {
        if (!cartOverlay || !cartPanel) return;
        cartOverlay.style.display = 'block';
        setTimeout(() => cartPanel.classList.add('active'), 10);
        updateCartUI(); // Carga los datos actuales
    }

    // Función Cerrar Carrito
    function closeCart() {
        if (!cartPanel || !cartOverlay) return;
        cartPanel.classList.remove('active');
        setTimeout(() => cartOverlay.style.display = 'none', 400);
    }

    if (openCartBtn) openCartBtn.addEventListener('click', (e) => { e.preventDefault(); openCart(); });
    if (closeCartBtn) closeCartBtn.addEventListener('click', closeCart);
    if (cartOverlay) cartOverlay.addEventListener('click', closeCart);

    // Función para actualizar la UI del carrito (AJAX)
    async function updateCartUI() {
        if (!cartContent) return;
        try {
            const response = await fetch('./controllers/carrito_ajax.php?action=get_items');
            const data = await response.json();

            if (data.success) {
                renderCartItems(data.items);
                if(cartTotalAmount) {
                    cartTotalAmount.textContent = data.total_formatted;
                }
            }
        } catch (error) {
            console.error('Error cargando el carrito:', error);
        }
    }

    function renderCartItems(items) {
        if (!items || items.length === 0) {
            cartContent.innerHTML = `
                <div class="empty-cart-message" style="text-align: center; padding: 2rem; opacity: 0.6;">
                    <p>Tu carrito está vacío</p>
                </div>
            `;
            return;
        }

        let html = '';
        items.forEach(item => {
            html += `
                <div class="cart-item" data-id="${item.id_producto}">
                    <img src="${item.url_imagen || './assets/img/productos/default.jpg'}" alt="${item.nombre_producto}">
                    <div class="item-info">
                        <h4>${item.nombre_producto}</h4>
                        <p class="item-price">${item.precio_formatted}</p>
                        <div class="item-controls">
                            <button class="btn-qty decrease" data-id="${item.id_producto}">-</button>
                            <span class="qty">${item.cantidad}</span>
                            <button class="btn-qty increase" data-id="${item.id_producto}">+</button>
                        </div>
                    </div>
                </div>
            `;
        });
        cartContent.innerHTML = html;

        // Re-asignar eventos a los nuevos botones de cantidad
        document.querySelectorAll('.btn-qty.increase').forEach(btn => {
            btn.onclick = () => updateQuantity(btn.dataset.id, 1);
        });
        document.querySelectorAll('.btn-qty.decrease').forEach(btn => {
            btn.onclick = () => updateQuantity(btn.dataset.id, -1);
        });
    }

    // Función para añadir al carrito (se llamará desde los botones de la tienda)
    window.addToCart = async function (id_producto) {
        const formData = new FormData();
        formData.append('id_producto', id_producto);
        formData.append('action', 'add');

        try {
            const response = await fetch('./controllers/carrito_ajax.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                openCart(); // Mostramos el carrito al añadir
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error al añadir:', error);
        }
    }

    async function updateQuantity(id_producto, delta) {
        const formData = new FormData();
        formData.append('id_producto', id_producto);
        formData.append('delta', delta);
        formData.append('action', 'update_qty');

        try {
            const response = await fetch('./controllers/carrito_ajax.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) updateCartUI();
        } catch (error) {
            console.error('Error updating qty:', error);
        }
    }
});