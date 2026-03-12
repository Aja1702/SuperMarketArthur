/**
 * --- LÓGICA DEL CARRITO LATERAL (SIDE CART) ---
 */
document.addEventListener('DOMContentLoaded', function () {
    const cartOverlay = document.getElementById('cartOverlay');
    const cartPanel = document.getElementById('cartPanel');
    const openCartButtons = document.querySelectorAll('.js-open-cart'); // Seleccionamos TODOS los botones que abren el carrito
    const closeCartBtn = document.getElementById('closeCart');
    const cartContent = document.getElementById('cartContent');
    const cartTotalAmount = document.getElementById('cartTotalAmount');

    function openCart() {
        if (!cartOverlay || !cartPanel) return;
        cartOverlay.style.display = 'block';
        setTimeout(() => cartPanel.classList.add('active'), 10);
        updateCartUI();
    }

    function closeCart() {
        if (!cartPanel || !cartOverlay) return;
        cartPanel.classList.remove('active');
        setTimeout(() => cartOverlay.style.display = 'none', 400);
    }

    // Asignamos el evento a todos los botones de abrir carrito
    openCartButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            openCart();
        });
    });

    if (closeCartBtn) closeCartBtn.addEventListener('click', closeCart);
    if (cartOverlay) cartOverlay.addEventListener('click', closeCart);

    async function updateCartUI() {
        if (!cartContent) return;
        try {
            const response = await fetch(BASE_URL + 'api/cart/items');
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
            cartContent.innerHTML = `<div class="empty-cart-message" style="text-align: center; padding: 2rem; opacity: 0.6;"><p>Tu carrito está vacío</p></div>`;
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

        document.querySelectorAll('.btn-qty.increase').forEach(btn => {
            btn.onclick = () => updateQuantity(btn.dataset.id, 1);
        });
        document.querySelectorAll('.btn-qty.decrease').forEach(btn => {
            btn.onclick = () => updateQuantity(btn.dataset.id, -1);
        });
    }

    window.addToCart = async function (id_producto) {
        const formData = new FormData();
        formData.append('id_producto', id_producto);

        try {
            const response = await fetch(BASE_URL + 'api/cart/add', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                openCart();
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

        try {
            const response = await fetch(BASE_URL + 'api/cart/update', {
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
