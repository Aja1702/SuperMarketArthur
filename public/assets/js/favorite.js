document.addEventListener('DOMContentLoaded', function () {
    document.body.addEventListener('click', async function (e) {
        if (e.target.closest('.js-toggle-favorite')) {
            e.preventDefault();
            const button = e.target.closest('.js-toggle-favorite');
            const id_producto = button.dataset.id;
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrf_token = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

            const formData = new FormData();
            formData.append('id_producto', id_producto);
            formData.append('csrf_token', csrf_token);

            try {
                const response = await fetch(BASE_URL + 'api/favorite/toggle', {
                    method: 'POST',
                    body: formData
                });

                // Si el usuario no está logueado, mostramos el modal de login
                if (response.status === 401) {
                    const loginModal = document.getElementById('loginModal');
                    if (loginModal) {
                        loginModal.style.display = 'flex'; // Corregido: usamos flex para que el CSS lo centre
                    }
                    return;
                }

                const data = await response.json();

                if (data.success) {
                    const icon = button.querySelector('i');
                    const isFavoritesPage = document.getElementById('favorites-page-container');

                    if (data.status === 'added') {
                        icon.classList.remove('far');
                        icon.classList.add('fas'); // Corazón relleno
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far'); // Corazón vacío

                        // Si estamos en la página de favoritos, eliminamos la tarjeta
                        if (isFavoritesPage) {
                            const card = button.closest('.card-producto');
                            if (card) {
                                card.style.transition = 'opacity 0.3s ease';
                                card.style.opacity = '0';
                                setTimeout(() => card.remove(), 300);
                            }
                        }
                    }
                }
            } catch (error) {
                console.error('Error al gestionar favorito:', error);
            }
        }
    });
});
