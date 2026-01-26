// Cargar y mostrar los juegos del catálogo
document.addEventListener('DOMContentLoaded', async function() {
    const gamesContainer = document.getElementById('gamesContainer');
    
    // Si no estamos en la página del catálogo, no hacer nada
    if (!gamesContainer) {
        console.log('gamesContainer no encontrado');
        return;
    }
    
    console.log('Iniciando carga de juegos...');
    
    try {
        // Cargar datos de juegos
        console.log('Intentando fetch a data/get_games.php');
        const response = await fetch('data/get_games.php');
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Datos cargados:', data);
        
        const games = data.games;
        console.log('Número de juegos:', games.length);
        
        // Generar HTML para cada juego
        games.forEach(game => {
            const gameCard = createGameCard(game);
            gamesContainer.appendChild(gameCard);
        });
        
        console.log('Juegos cargados exitosamente');
    } catch (error) {
        console.error('Error al cargar los juegos:', error);
        gamesContainer.innerHTML = '<p style="color: white; text-align: center;">Error al cargar los juegos: ' + error.message + '</p>';
    }
});

/**
 * Crea una tarjeta de juego con HTML
 * @param {Object} game - Objeto del juego
 * @returns {HTMLElement} - Elemento de la tarjeta
 */
function createGameCard(game) {
    const card = document.createElement('div');
    card.className = 'game-card';
    card.setAttribute('data-game-id', game.id);
    
    // Procesar géneros (todos en ambas vistas)
    const genreHTML = game.genre.map(g => `<span class="genre-tag">${escapeHtml(g)}</span>`).join('');
    const allGenresHTML = game.genre.map(g => `<span class="genre-tag">${escapeHtml(g)}</span>`).join('');
    
    // Procesar todas las plataformas
    const platformsHTML = game.platform.map(p => `<span class="platform-tag">${escapeHtml(p)}</span>`).join('');
    
    // Procesar características
    let featuresHTML = '';
    let playtimeHTML = '';
    
    if (game.multiplayer) {
        featuresHTML += '<span class="feature-badge" title="Multijugador">👥 Multijugador</span>';
    }
    if (game.achievements) {
        featuresHTML += '<span class="feature-badge" title="Logros">🏆 Logros</span>';
    }
    playtimeHTML = `<span class="playtime" title="Tiempo de juego">⏱️ ${escapeHtml(game.playtime)} horas</span>`;
    
    // Formatear fecha
    const releaseDate = new Date(game.releaseDate);
    const formattedDate = releaseDate.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
    
    // Descripción resumida (primeros 100 caracteres)
    const shortDescription = game.description.substring(0, 100) + '...';
    
    // HTML de la tarjeta
    card.innerHTML = `
        <!-- Vista compacta (mostrada por defecto) -->
        <div class="game-compact-view">
            <div class="game-image">
                <img src="img/games/${escapeHtml(game.image)}" 
                     alt="${escapeHtml(game.title)}" 
                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22280%22 height=%22200%22%3E%3Crect fill=%22%231a1a1a%22 width=%22280%22 height=%22200%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2220%22 fill=%22%2300d4ff%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3EImagen no disponible%3C/text%3E%3C/svg%3E'">
                <div class="game-overlay">
                    <span class="game-rating">⭐ ${game.rating.toFixed(1)}</span>
                </div>
            </div>
            
            <div class="game-content">
                <h3 class="game-title">${escapeHtml(game.title)}</h3>
                
                <div class="game-info">
                    <p class="game-developer">Por: <strong>${escapeHtml(game.developer)}</strong></p>
                    <p class="game-date">Lanzamiento: ${formattedDate}</p>
                </div>
                
                <div class="game-genres">
                    ${genreHTML}
                </div>
                
                <p class="game-description">${escapeHtml(shortDescription)}</p>
                
                <div class="game-features">
                    ${featuresHTML}
                </div>
                
                <div class="game-playtime">
                    ${playtimeHTML}
                </div>
                
                <div class="game-footer">
                    <div class="game-price-and-cart">
                        <span class="game-price">${game.price.toFixed(2)}€</span>
                        <form method="POST" action="add_to_cart.php" class="add-to-cart-form">
                            <input type="hidden" name="game_id" value="${game.id}">
                            <button type="submit" name="add_to_cart" class="btn btn-primary btn-small">
                                🛒 Carrito
                            </button>
                        </form>
                    </div>
                    <div class="game-action-buttons">
                        <button type="button" class="btn btn-expand btn-small" title="Más información">
                            📖 Saber más
                        </button>
                        <a href="${escapeHtml(game.link)}" target="_blank" rel="noopener noreferrer" class="btn btn-steam btn-small" title="Abrir en Steam">
                            🎮 Steam
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Vista expandida (oculta por defecto) -->
        <div class="game-expanded-view">
            <div class="expanded-header">
                <div class="expanded-image-small">
                    <img src="img/games/${escapeHtml(game.image)}" 
                         alt="${escapeHtml(game.title)}" 
                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22150%22 height=%22150%22%3E%3Crect fill=%22%231a1a1a%22 width=%22150%22 height=%22150%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2215%22 fill=%22%2300d4ff%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3EN/A%3C/text%3E%3C/svg%3E'">
                </div>
                <div class="expanded-header-content">
                    <h2 class="expanded-title">${escapeHtml(game.title)}</h2>
                    <p class="expanded-developer"><strong>${escapeHtml(game.developer)}</strong></p>
                    <div class="expanded-rating-badge">⭐ ${game.rating.toFixed(1)}/10</div>
                </div>
            </div>
            
            <div class="expanded-content-wrapper">
                <div class="expanded-section">
                    <h4>Lanzamiento</h4>
                    <p>${formattedDate}</p>
                </div>
                
                <div class="expanded-section">
                    <h4>Descripción</h4>
                    <p>${escapeHtml(game.description)}</p>
                </div>
                
                <div class="expanded-section">
                    <h4>Géneros</h4>
                    <div class="game-genres-expanded">
                        ${allGenresHTML}
                    </div>
                </div>
                
                <div class="expanded-section">
                    <h4>Características</h4>
                    <div class="expanded-features">
                        ${game.multiplayer ? '<div class="expanded-feature">👥 <b>Multijugador</b></div>' : ''}
                        ${game.achievements ? '<div class="expanded-feature">🏆 <b>Logros</b></div>' : ''}
                        <div class="expanded-feature">⏱️ Tiempo de juego potencial: <b>${escapeHtml(game.playtime)} horas.</b></div>
                    </div>
                </div>
                
                <div class="expanded-section">
                    <h4>Plataformas</h4>
                    <div class="platforms-list">
                        ${platformsHTML}
                    </div>
                </div>
                
                <div class="expanded-section">
                    <h4>Requisitos del Sistema</h4>
                    <div class="system-requirements">
                        <p><strong>Sistema Operativo:</strong> ${escapeHtml(game.systemRequirements.os)}</p>
                        <p><strong>Procesador:</strong> ${escapeHtml(game.systemRequirements.processor)}</p>
                        <p><strong>Memoria RAM:</strong> ${escapeHtml(game.systemRequirements.memory)}</p>
                        <p><strong>Almacenamiento:</strong> ${escapeHtml(game.systemRequirements.storage)}</p>
                    </div>
                </div>
                
                <div class="expanded-section">
                    <h4>Información Técnica</h4>
                    <div class="game-tech-info">
                        <p><strong>Desarrollador:</strong> ${escapeHtml(game.developer)}</p>
                        <p><strong>Editor:</strong> ${escapeHtml(game.publisher)}</p>
                        <p><strong>Reseñas:</strong> ${game.reviews.toLocaleString('es-ES')}</p>
                        <p><strong>Valoración:</strong> ${game.rating.toFixed(1)}/10</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer para vista expandida -->
        <div class="game-footer game-footer-expanded">
            <div class="game-price-and-cart">
                <span class="game-price">$${game.price.toFixed(2)}</span>
                <form method="POST" action="add_to_cart.php" class="add-to-cart-form">
                    <input type="hidden" name="game_id" value="${game.id}">
                    <button type="submit" name="add_to_cart" class="btn btn-primary btn-small">
                        🛒 Carrito
                    </button>
                </form>
            </div>
            <div class="game-action-buttons">
                <button type="button" class="btn btn-expand btn-small" title="Más información">
                    📖 Reducir
                </button>
                <a href="${escapeHtml(game.link)}" target="_blank" rel="noopener noreferrer" class="btn btn-steam btn-small" title="Abrir en Steam">
                    🎮 Steam
                </a>
            </div>
        </div>
    `;
    
    // Agregar event listener a TODOS los botones de expandir (hay uno en vista compacta y otro en expandida)
    const expandBtns = card.querySelectorAll('.btn-expand');
    expandBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            card.classList.toggle('expanded');
            // Actualizar el texto de ambos botones
            expandBtns.forEach(b => {
                b.textContent = card.classList.contains('expanded') ? '📖 Reducir' : '📖 Saber más';
            });
        });
    });
    
    return card;
}

/**
 * Escapa caracteres HTML para evitar XSS
 * @param {string} text - Texto a escapar
 * @returns {string} - Texto escapado
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
