/**
 * --- LÓGICA DEL BUSCADOR INTELIGENTE ---
 */
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    if (!searchInput || !searchResults) return;

    let timeout = null;

    searchInput.addEventListener('input', function () {
        clearTimeout(timeout);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        timeout = setTimeout(async () => {
            try {
                const response = await fetch(`./controllers/buscar_ajax.php?q=${encodeURIComponent(query)}`);
                const data = await response.json();

                if (data.success && data.results.length > 0) {
                    renderSearchResults(data.results);
                } else {
                    searchResults.style.display = 'none';
                }
            } catch (error) {
                console.error('Error en búsqueda:', error);
            }
        }, 300);
    });

    function renderSearchResults(results) {
        searchResults.innerHTML = '';
        results.forEach(item => {
            const div = document.createElement('a');
            div.href = `./?vistaMenu=categorias_productos&cat=${item.id_categoria}&id=${item.id_producto}`;
            div.className = 'search-result-item';
            div.innerHTML = `
                <img src="${item.url_imagen || './assets/img/productos/default.jpg'}" alt="${item.nombre_producto}">
                <div class="search-result-info">
                    <h4>${item.nombre_producto}</h4>
                    <p>${item.precio_formatted}</p>
                </div>
            `;
            searchResults.appendChild(div);
        });
        searchResults.style.display = 'block';
    }

    // Cerrar resultados al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
});
