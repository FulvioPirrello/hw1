class ModelliManager {
    constructor() {
        this.currentPage = 1;
        this.isLoading = false;
        this.hasInitialized = false;
        this.loadTimeout = null;
        this.init();
    }

    init() {
        if (this.hasInitialized) return;
        this.hasInitialized = true;
        this.setupFilterButtons();
        setTimeout(() => {
            this.loadModelli();
        }, 100);
    }

    setupFilterButtons() {
        const popularButton = document.querySelector('.popular');
        const allButton = document.querySelector('.all');

        if (popularButton && !popularButton.hasAttribute('data-listener')) {
            popularButton.setAttribute('data-listener', 'true');
            popularButton.addEventListener('click', () => {
                this.loadModelliWithDebounce('popular');
            });
        }

        if (allButton && !allButton.hasAttribute('data-listener')) {
            allButton.setAttribute('data-listener', 'true');
            allButton.addEventListener('click', () => {
                this.loadModelliWithDebounce('all');
            });
        }
    }

    loadModelliWithDebounce(filter = 'all') {
        if (this.loadTimeout) {
            clearTimeout(this.loadTimeout);
        }
        this.loadTimeout = setTimeout(() => {
            this.loadModelli(filter);
        }, 300);
    }

    loadModelli(filter = 'all') {
        if (this.isLoading) return;
        this.isLoading = true;
        this.showLoading();

        const params = new URLSearchParams({
            action: 'get_all',
            page: this.currentPage,
            limit: 12,
            filter: filter
        });

        const url = `modelli.php?${params}`;

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                let jsonData;
                const jsonStart = text.indexOf('{');
                if (jsonStart === -1) {
                    throw new Error('Nessun JSON trovato nella risposta');
                }
                const jsonText = jsonStart > 0 ? text.substring(jsonStart) : text;
                jsonData = JSON.parse(jsonText);
                this.handleLoadResponse(jsonData);
            })
            .catch(error => {
                this.showError(`Errore: ${error.message}`);
            })
            .finally(() => {
                this.isLoading = false;
                this.hideLoading();
            });
    }

    handleLoadResponse(data) {
        if (!data.success) {
            this.showError(data.message || 'Errore dal server');
            return;
        }
        const modelli = data.modelli || [];
        if (modelli.length === 0) {
            this.showEmptyState();
            return;
        }
        this.renderModelli(modelli);
    }

    renderModelli(modelli) {
        const container = document.querySelector('.item-container');
        if (!container) return;
        container.innerHTML = '';
        const fragment = document.createDocumentFragment();
        modelli.forEach(modello => {
            const itemElement = this.createModelItem(modello);
            fragment.appendChild(itemElement);
        });
        container.appendChild(fragment);
        setTimeout(() => {
            this.setupLikeButtons();
        }, 50);
    }

    createModelItem(modello) {
        const item = document.createElement('div');
        item.className = 'item';
        item.setAttribute('data-model-id', modello.id_modello);

        const isLiked = Boolean(modello.user_liked);
        const likeCount = parseInt(modello.quantita_like) || 0;
        const dataFormatted = this.formatDate(new Date(modello.data_pubblicazione));

        const randomImg = `https://source.unsplash.com/300x200/?3d,print,object,random&sig=${Math.floor(Math.random()*10000)}`;
        const imgSrc = modello.immagine && modello.immagine.trim() !== '' 
            ? modello.immagine 
            : randomImg;

        item.innerHTML = `
            <div class="item-image-container">
                <img src="${imgSrc}" 
                    class="item-image"
                    loading="lazy">
                <div class="like-container">
                    <button class="like-btn" 
                            data-like-btn="true" 
                            data-model-id="${modello.id_modello}"
                            data-liked="${isLiked}"
                            type="button">
                        <span class="heart-icon">${isLiked ? '❤️' : '🤍'}</span>
                        <span class="like-count">${likeCount}</span>
                    </button>
                </div>
            </div>
            <div class="item-content">
                <div class="item-author">da ${this.escapeHtml(modello.nome_utente || 'Utente sconosciuto')}</div>
                <div class="item-date">${dataFormatted}</div>
                <h3 class="item-title">${this.escapeHtml(modello.nome_modello || 'Nome non disponibile')}</h3>
                <div class="item-stats">
                    <span class="likes">❤️ ${likeCount} Mi piace</span>
                </div>
            </div>
        `;
        return item;
    }

    setupLikeButtons() {
        const oldButtons = document.querySelectorAll('[data-like-btn][data-listener="true"]');
        oldButtons.forEach(btn => {
            btn.removeAttribute('data-listener');
        });

        const likeButtons = document.querySelectorAll('[data-like-btn]:not([data-listener])');
        likeButtons.forEach(button => {
            button.setAttribute('data-listener', 'true');
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (button.disabled) return;
                const modelId = button.getAttribute('data-model-id');
                this.toggleLike(modelId, button);
            });
        });
    }

    toggleLike(modelId, button) {
        button.disabled = true;
        button.style.opacity = '0.7';

        const formData = new FormData();
        formData.append('action', 'toggle_like');
        formData.append('id_modello', modelId);

        fetch('modelli.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            const jsonStart = text.indexOf('{');
            const jsonText = jsonStart > 0 ? text.substring(jsonStart) : text;
            const data = JSON.parse(jsonText);
            if (data.success) {
                this.updateLikeButton(button, data);
                this.showNotification(
                    data.action === 'added' ? 'Like aggiunto!' : 'Like rimosso!'
                );
            } else {
                this.showNotification('Errore: ' + data.message, 'error');
            }
        })
        .catch(() => {
            this.showNotification('Errore di connessione', 'error');
        })
        .finally(() => {
            button.disabled = false;
            button.style.opacity = '1';
        });
    }

    updateLikeButton(button, data) {
        const heartIcon = button.querySelector('.heart-icon');
        const countElement = button.querySelector('.like-count');
        if (!heartIcon || !countElement) return;
        const newLiked = data.user_liked;
        const newCount = data.new_count;
        heartIcon.textContent = newLiked ? '❤️' : '🤍';
        countElement.textContent = newCount;
        button.setAttribute('data-liked', newLiked);
        const statsElement = button.closest('.item').querySelector('.likes');
        if (statsElement) {
            statsElement.innerHTML = `❤️ ${newCount} Mi piace`;
        }
        heartIcon.style.transform = 'scale(1.2)';
        setTimeout(() => {
            heartIcon.style.transform = 'scale(1)';
        }, 200);
    }

    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
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
                    <h3>Nessun modello trovato</h3>
                    <p>Non ci sono modelli da visualizzare al momento.</p>
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
                    <h3>Errore</h3>
                    <p>${message}</p>
                    <button onclick="modelliManager.loadModelli()">Riprova</button>
                </div>
            `;
        }
    }

    showNotification(message, type = 'success') {
        document.querySelectorAll('.notification').forEach(n => n.remove());
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

let modelliManager = null;

function initModelliManager() {
    if (modelliManager) return;
    modelliManager = new ModelliManager();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initModelliManager);
} else {
    initModelliManager();
}