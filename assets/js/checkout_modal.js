document.addEventListener('DOMContentLoaded', function () {
    // Esta función se encarga EXCLUSIVAMENTE de manejar el modal de login para el checkout

    const loginModal = document.getElementById('loginModal');

    // Se usa delegación de eventos en el 'body' para asegurar que funcione
    // incluso si el botón del carrito es recreado dinámicamente por otro script.
    document.body.addEventListener('click', function(event) {

        // 1. ABRIR EL MODAL
        // Si el elemento clickeado es el botón con id 'checkoutBtn'
        if (event.target && event.target.id === 'checkoutBtn') {

            // Nos aseguramos de que sea el botón para invitados (y no el enlace para usuarios logueados)
            if (event.target.tagName === 'BUTTON') {
                event.preventDefault(); // Prevenimos cualquier acción por defecto del botón

                if (loginModal) {
                    loginModal.style.display = 'flex'; // Mostramos el modal
                }
            }
        }

        // 2. CERRAR EL MODAL
        if (loginModal) {
            // Si se hace clic en el botón de cerrar (la 'X')
            if (event.target.id === 'closeLoginModal') {
                loginModal.style.display = 'none';
            }
            // Si se hace clic en el fondo oscuro (el overlay)
            if (event.target === loginModal) {
                loginModal.style.display = 'none';
            }
        }
    });
});
