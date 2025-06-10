
class ModelliManager {
    constructor() {
        this.currentPage = 1;
        this.isLoading = false;
        this.init();
    }

    init() {
        console.log('🚀 Inizializzazione ModelliManager...');
        this.setupFilterButtons();
        this.loadModelli();
    }

    setupFilterButtons() {
        const popularButton = document.querySelector('.popular');
        const allButton = document.querySelector('.all');

        if (popularButton) {
            popularButton.addEventListener('click', () => {
                this.loadModelli('popular');
            });
        }

        if (allButton) {
            allButton.addEventListener('click', () => {
                this.loadModelli('all');
            });
        }
    }

    loadModelli(filter = 'all') {
        if (this.isLoading) return;

        this.isLoading = true;
        this.showLoading();

        console.log('Caricamento modelli...');

        const params = new URLSearchParams({
            action: 'get_all',
            page: this.currentPage,
            limit: 12,
            filter: filter
        });

        const url = `modelli.php?${params}`;
        console.log(' URL:', url);

        fetch(url)
            .then(response => {
                console.log(' Response status:', response.status);
                console.log(' Response headers:', response.headers.get('content-type'));
                
                return response.text();
            })
            .then(text => {
                console.log(' RISPOSTA COMPLETA DAL SERVER:');
                console.log('='.repeat(50));
                console.log(text);
                console.log('='.repeat(50));
                
                const jsonStart = text.indexOf('{');
                if (jsonStart === -1) {
                    throw new Error('Nessun JSON trovato nella risposta');
                }
                
                if (jsonStart > 0) {
                    console.log(' ERRORI PRIMA DEL JSON:');
                    console.log(text.substring(0, jsonStart));
                    console.log(' JSON ESTRATTO:');
                    const jsonText = text.substring(jsonStart);
                    console.log(jsonText);
                    
                    const data = JSON.parse(jsonText);
                    this.handleData(data);
                } else {
                    const data = JSON.parse(text);
                    this.handleData(data);
                }
            })
            .catch(error => {
                console.error('ERRORE COMPLETO:', error);
                this.showError('Errore: ' + error.message);
            })
            .finally(() => {
                this.isLoading = false;
                this.hideLoading();
            });
    }

    handleData(data) {
        console.log('JSON parsato:', data);
        
        if (data.success) {
            this.renderModelli(data.modelli || []);
        } else {
            this.showError(data.message || 'Errore dal server');
        }
    }

    renderModelli(modelli) {
        console.log('🎨 Rendering', modelli.length, 'modelli');
        
        const container = document.querySelector('.item-container');
        if (!container) {
            console.error(' Container non trovato!');
            return;
        }

        container.innerHTML = '';

        if (modelli.length === 0) {
            this.showEmptyState();
            return;
        }

        modelli.forEach(modello => {
            const itemElement = this.createModelItem(modello);
            container.appendChild(itemElement);
        });

        this.setupLikeButtons();
    }

    createModelItem(modello) {
        const item = document.createElement('div');
        item.className = 'item';
        item.setAttribute('data-model-id', modello.id_modello);

        const dataFormatted = this.formatDate(new Date(modello.data_pubblicazione));

        item.innerHTML = `
            <div class="item-image-container">
                <img src="${modello.immagine || 'https://via.placeholder.com/300x200/CCCCCC/FFFFFF?text=No+Image'}" 
                     alt="${modello.nome_modello}" 
                     class="item-image"
                     onerror="this.src='https://via.placeholder.com/300x200/CCCCCC/FFFFFF?text=No+Image'">
                
                <div class="like-container">
                    <button class="like-btn" 
                            data-like-btn="true" 
                            data-model-id="${modello.id_modello}"
                            title="Aggiungi ai preferiti">
                        <span class="heart-icon">${modello.user_liked ? '❤️' : '🤍'}</span>
                        <span class="like-count">${modello.quantita_like}</span>
                    </button>
                </div>
            </div>

            <div class="item-content">
                <h3 class="item-title">${modello.nome_modello}</h3>
                <div class="item-author">
                    <span>da ${modello.nome_utente}</span>
                </div>
                <div class="item-date">${dataFormatted}</div>
                <div class="item-stats">
                    <span class="likes">❤️ ${modello.quantita_like}</span>
                </div>
            </div>
        `;

        return item;
    }

    setupLikeButtons() {
        const likeButtons = document.querySelectorAll('[data-like-btn]');
        
        likeButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const modelId = button.getAttribute('data-model-id');
                this.toggleLike(modelId, button);
            });
        });
    }

    toggleLike(modelId, button) {
        const formData = new FormData();
        formData.append('action', 'toggle_like');
        formData.append('id_modello', modelId);

        fetch('modelli.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            console.log('📄 Risposta toggle like:', text);
            
            // Estrai JSON se necessario
            const jsonStart = text.indexOf('{');
            const jsonText = jsonStart > 0 ? text.substring(jsonStart) : text;
            const data = JSON.parse(jsonText);
            
            if (data.success) {
                const heartIcon = button.querySelector('.heart-icon');
                const countElement = button.querySelector('.like-count');
                
                heartIcon.innerHTML = data.user_liked ? '❤️' : '🤍';
                countElement.textContent = data.new_count;
                
                this.showNotification(
                    data.action === 'added' ? 'Like aggiunto!' : 'Like rimosso!'
                );
            } else {
                this.showNotification('Errore: ' + data.message, 'error');
            }
        })
        
    }

    formatDate(date) {
        const now = new Date();
        const diffDays = Math.floor((now - date) / (1000 * 60 * 60 * 24));

        if (diffDays === 0) return 'Oggi';
        if (diffDays === 1) return 'Ieri';
        if (diffDays < 7) return `${diffDays} giorni fa`;
        return date.toLocaleDateString('it-IT');
    }

    showLoading() {
        const container = document.querySelector('.item-container');
        if (container) {
            container.innerHTML = `
                <div class="loading-container">
                    <div class="loading-text">Caricamento modelli...</div>
                </div>
            `;
        }
    }

    hideLoading() {
        const loading = document.querySelector('.loading-container');
        if (loading) {
            loading.remove();
        }
    }

    showEmptyState() {
        const container = document.querySelector('.item-container');
        if (container) {
            container.innerHTML = `
                <div class="empty-state">
                    <h3>📭 Nessun modello trovato</h3>
                    <button onclick="modelliManager.loadModelli()">Ricarica</button>
                </div>
            `;
        }
    }

    showError(message) {
        const container = document.querySelector('.item-container');
        if (container) {
            container.innerHTML = `
                <div class="error-state">
                    <h3>❌Errore nel caricamento</h3>
                    <p>${message}</p>
                    <button onclick="modelliManager.loadModelli()">Riprova</button>
                </div>
            `;
        }
    }

    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#28a745' : '#dc3545'};
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            z-index: 9999;
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

let modelliManager;
document.addEventListener('DOMContentLoaded', function() {
    console.log('🌟 DOM caricato, inizializzazione...');
    modelliManager = new ModelliManager();
});