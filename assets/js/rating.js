/**
 * --- LÓGICA PARA LAS ESTRELLAS DE VALORACIÓN ---
 */
document.addEventListener('DOMContentLoaded', function () {
    const ratingForm = document.querySelector('.valoracion-form-container');

    if (ratingForm) {
        const stars = ratingForm.querySelectorAll('.star-rating label');

        stars.forEach(star => {
            star.addEventListener('click', function() {
                // Log to the console to confirm a rating was selected via click.
                const ratingValue = ratingForm.querySelector('input[name="puntuacion"]:checked').value;
                console.log('Rating selected: ' + ratingValue);
            });
        });

        console.log('Star rating system initialized.');
    }
});
