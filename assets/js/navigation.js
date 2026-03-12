document.addEventListener('DOMContentLoaded', function () {
    // Lógica para los botones de "Volver"
    const backButtons = document.querySelectorAll('.js-back-button');
    backButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            window.history.back();
        });
    });
});
