document.addEventListener('DOMContentLoaded', function() {
    let videojuegos = [];
    let categories = [];

    // Función para cargar los videojuegos
    function loadVideojuegos() {
        fetch('http://tu-api-url/obtener-productos')
            .then(response => response.json())
            .then(data => {
                videojuegos = data;
                categories = [...new Set(data.map(v => v.categoria))];
                renderCategories();
                renderVideojuegos(videojuegos);
                updateGameCount();
            })
            .catch(error => console.error('Error:', error));
    }

    // Función para renderizar las categorías
    function renderCategories() {
        const categoryGrid = document.querySelector('.category-grid');
        categories.forEach(category => {
            const categoryCard = document.createElement('div');
            categoryCard.className = 'category-card';
            categoryCard.dataset.category = category;
            categoryCard.innerHTML = `
                <i class="${getCategoryIcon(category)}"></i>
                <h3>${category}</h3>
                <span class="game-count">0 Juegos</span>
            `;
            categoryCard.addEventListener('click', () => filterVideojuegos(category));
            categoryGrid.appendChild(categoryCard);
        });
    }

    // Función para filtrar videojuegos
    function filterVideojuegos(category) {
        const categoryCards = document.querySelectorAll('.category-card');
        categoryCards.forEach(card => card.classList.remove('active'));
        event.target.closest('.category-card').classList.add('active');

        const filteredVideojuegos = category 
            ? videojuegos.filter(v => v.categoria === category)
            : videojuegos;
        renderVideojuegos(filteredVideojuegos);
    }

    // Función para renderizar los videojuegos
    function renderVideojuegos(videojuegosToRender) {
        const container = document.getElementById('videojuegos-container');
        container.innerHTML = '';
        
        // Añadir o quitar la clase 'single-card' según el número de juegos
        if (videojuegosToRender.length === 1) {
            container.classList.add('single-card');
        } else {
            container.classList.remove('single-card');
        }
        
        videojuegosToRender.forEach(videojuego => {
            const videojuegoElement = createVideojuegoElement(videojuego);
            container.appendChild(videojuegoElement);
        });
    }

    // Función para crear el elemento HTML de un videojuego
    function createVideojuegoElement(videojuego) {
        const element = document.createElement('div');
        element.className = 'game-card';
        element.innerHTML = `
            <div class="game-img">
                <img src="http://localhost/Angular/tiendaAngular/server/rest/imagenes/${videojuego.id}.jpg" alt="${videojuego.nombre}">
                ${videojuego.descuento > 0 ? `<span class="discount-badge">-${videojuego.descuento}%</span>` : ''}
            </div>
            <div class="game-info">
                <h3>${videojuego.nombre}</h3>
                <div class="game-meta">
                    <span><i class="fas fa-gamepad"></i> ${videojuego.categoria}</span>
                    <span class="${getStockClass(videojuego.stock)}">
                        <i class="fas fa-box"></i> ${getStockText(videojuego.stock)}
                    </span>
                </div>
                <p class="price">
                    ${videojuego.descuento > 0 ? `<span class="original-price">${videojuego.precio}€</span>` : ''}
                    <span class="discounted-price">${calculateDiscountedPrice(videojuego).toFixed(2)}€</span>
                </p>
                <button onclick="verDetalles(${videojuego.id})">
                    <i class="fas fa-info-circle"></i> Ver detalles
                </button>
            </div>
        `;
        return element;
    }

    // Funciones auxiliares (sin cambios)
    function getStockClass(stock) {
        if (stock > 10) return 'text-success';
        if (stock > 0) return 'text-warning';
        return 'text-danger';
    }

    function getStockText(stock) {
        if (stock > 10) return 'Disponible';
        if (stock > 0) return 'Stock Mínimo';
        return 'No Disponible';
    }

    function getCategoryIcon(category) {
        const icons = {
            'Acción': 'fas fa-fist-raised',
            'RPG': 'fas fa-hat-wizard',
            'Carreras': 'fas fa-car',
            'Deportes': 'fas fa-football-ball',
        };
        return icons[category] || 'fas fa-gamepad';
    }

    function calculateDiscountedPrice(videojuego) {
        return videojuego.precio * (1 - videojuego.descuento / 100);
    }

    function updateGameCount() {
        const allGamesCard = document.querySelector('.category-card[data-category=""]');
        allGamesCard.querySelector('.game-count').textContent = `${videojuegos.length} Juegos`;

        categories.forEach(category => {
            const count = videojuegos.filter(v => v.categoria === category).length;
            const categoryCard = document.querySelector(`.category-card[data-category="${category}"]`);
            categoryCard.querySelector('.game-count').textContent = `${count} Juegos`;
        });
    }

    // Función para ver detalles (deberás implementarla según tus necesidades)
    window.verDetalles = function(id) {
        console.log(`Ver detalles del juego con ID: ${id}`);
        // Implementa la navegación a la página de detalles
    }

    // Iniciar la carga de videojuegos
    loadVideojuegos();
});

