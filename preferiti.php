<?php
$page_title = "I Miei Preferiti - Modelli 3D";
$current_page = "Favorites";

include 'navbar.php';
?>

<main>
    <div class="favorites-container">
        <div class="favorites-header">
            <h1>I Miei Preferiti</h1>
            <div class="favorites-count">
                <span class="count">0</span> modelli
            </div>
        </div>
        
        <div class="favorites-grid" id="favorites-grid">
            <div class="loading-message">Caricamento preferiti...</div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadFavoriteModels();
});

function loadFavoriteModels() {
    document.getElementById('favorites-grid').innerHTML = 
        '<div class="loading-message">Caricamento preferiti...</div>';
    fetch('modelli.php?action=get_favorites')
    .then(response => response.text())
    .then(text => {
        const jsonStart = text.indexOf('{');
        const jsonText = jsonStart > 0 ? text.substring(jsonStart) : text;
        const data = JSON.parse(jsonText);
        if (data.success) {
            displayFavoriteModels(data);
        } else {
            document.getElementById('favorites-grid').innerHTML = 
                '<div class="error-message">Errore: ' + (data.message || 'Errore nel caricamento') + '</div>';
        }
    })
    .catch(error => {
        document.getElementById('favorites-grid').innerHTML = 
            '<div class="error-message">Errore nel caricamento dei preferiti</div>';
    });
}

function displayFavoriteModels(data) {
    const grid = document.getElementById('favorites-grid');
    const countElement = document.querySelector('.count');
    const models = data.modelli || [];
    const totalCount = data.total || models.length;
    countElement.textContent = totalCount;
    if (models.length === 0) {
        grid.innerHTML = `
            <div class="empty-favorites">
                <div class="empty-icon"></div>
                <h3>Nessun modello nei preferiti</h3>
                <p>Aggiungi alcuni modelli ai tuoi preferiti per vederli qui!</p>
                <a href="index.php" class="browse-models-btn">Esplora Modelli</a>
            </div>
        `;
        return;
    }
    let html = '';
    models.forEach(model => {
        const dataFormatted = formatDate(new Date(model.data_pubblicazione));
        const likeCount = parseInt(model.quantita_like) || 0;
        html += `
            <div class="favorite-item" data-model-id="${model.id_modello}">
                <div class="item-image">
                    <img src="${model.immagine || 'https://via.placeholder.com/300x200/CCCCCC/FFFFFF?text=Modello+3D'}" 
                         alt="${escapeHtml(model.nome_modello)}" 
                         onerror="this.src='https://via.placeholder.com/300x200/CCCCCC/FFFFFF?text=Modello+3D'">
                    <div class="model-badges">
                        <span class="favorite-badge">Preferito</span>
                    </div>
                </div>
                <div class="item-info">
                    <h3 class="model-title">${escapeHtml(model.nome_modello)}</h3>
                    <div class="model-meta">
                        <div class="model-author">
                            <span class="author-icon"></span>
                            <span>da ${escapeHtml(model.nome_utente || 'Sconosciuto')}</span>
                        </div>
                        <div class="model-date">
                            <span class="date-icon"></span>
                            <span>${dataFormatted}</span>
                        </div>
                    </div>
                    <div class="model-stats">
                        <div class="stat-item">
                            <span class="stat-icon"></span>
                            <span class="stat-value">${likeCount}</span>
                            <span class="stat-label">Mi piace</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-icon"></span>
                            <span class="stat-value">${model.downloads || 0}</span>
                            <span class="stat-label">Download</span>
                        </div>
                    </div>
                    <div class="item-actions">
                        <a href="dettaglio_modello.php?id=${model.id_modello}" class="view-model-btn">
                            Visualizza Modello
                        </a>
                        <button class="download-model-btn" onclick="downloadModel(${model.id_modello})">
                            Scarica
                        </button>
                        <button class="remove-favorite-btn" onclick="removeFromFavorites(${model.id_modello})">
                            Rimuovi dai Preferiti
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    grid.innerHTML = html;
}

function downloadModel(modelId) {
    fetch(`modelli.php?action=download&id=${modelId}`)
    .then(response => {
        if (response.ok) {
            const contentType = response.headers.get('content-type');
            if (contentType && (contentType.includes('application/') || contentType.includes('model/'))) {
                return response.blob();
            } else {
                return response.text();
            }
        }
        throw new Error('Errore nel download');
    })
    .then(data => {
        if (data instanceof Blob) {
            const url = window.URL.createObjectURL(data);
            const a = document.createElement('a');
            a.href = url;
            a.download = `modello_${modelId}.zip`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            showNotification('Download completato!', 'success');
        } else {
            const jsonData = JSON.parse(data);
            showNotification((jsonData.message || 'Errore nel download'), 'error');
        }
    })
    .catch(error => {
        showNotification('Errore nel download del modello', 'error');
    });
}

function removeFromFavorites(modelId) {
    if (!confirm('Sei sicuro di voler rimuovere questo modello dai preferiti?')) {
        return;
    }
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
        if (data.success && data.action === 'removed') {
            showNotification('Modello rimosso dai preferiti!', 'success');
            setTimeout(() => {
                loadFavoriteModels();
            }, 1000);
        } else {
            showNotification('Errore nella rimozione: ' + (data.message || 'Errore sconosciuto'), 'error');
        }
    })
    .catch(error => {
        showNotification('Errore nella rimozione dai preferiti', 'error');
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(date) {
    const now = new Date();
    const diffDays = Math.floor((now - date) / (1000 * 60 * 60 * 24));
    if (diffDays === 0) return 'Oggi';
    if (diffDays === 1) return 'Ieri';
    if (diffDays < 7) return `${diffDays} giorni fa`;
    if (diffDays < 30) {
        const weeks = Math.floor(diffDays / 7);
        return `${weeks} settimana${weeks > 1 ? 'e' : ''} fa`;
    }
    return date.toLocaleDateString('it-IT');
}

function showNotification(message, type = 'success') {
    document.querySelectorAll('.notification').forEach(n => n.remove());
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        z-index: 9999;
        max-width: 350px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideInRight 0.3s ease;
        background: ${type === 'success' ? '#28a745' : '#dc3545'};
    `;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.remove();
    }, 4000);
}
</script>

<script src="profilo_utente.js" defer></script>

<style>
.favorites-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
    margin-top: 100px;
}

.favorites-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #e0e0e0;
    padding-bottom: 20px;
}

.favorites-header h1 {
    font-size: 2.5rem;
    color: #333;
    font-weight: 700;
}

.favorites-count {
    font-size: 1.2rem;
    color: #666;
    background: #f8f9fa;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
}

.favorites-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 24px;
}

.favorite-item {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(0,0,0,0.04);
}

.favorite-item:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.15);
}

.item-image {
    position: relative;
    height: 220px;
    overflow: hidden;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.favorite-item:hover .item-image img {
    transform: scale(1.05);
}

.model-badges {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 5;
}

.favorite-badge {
    background: rgba(220, 53, 69, 0.95);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

.item-info {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.model-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #333;
    margin: 0;
    line-height: 1.3;
    overflow: hidden;
}

.model-meta {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.model-author, .model-date {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #666;
}

.author-icon, .date-icon {
    font-size: 16px;
}

.model-stats {
    display: flex;
    gap: 20px;
    padding: 12px 0;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 6px;
    flex: 1;
}

.stat-icon {
    font-size: 16px;
}

.stat-value {
    font-weight: 700;
    color: #333;
}

.stat-label {
    font-size: 12px;
    color: #999;
}

.item-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.view-model-btn, .download-model-btn, .remove-favorite-btn {
    padding: 12px 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.view-model-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.view-model-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.download-model-btn {
    background: #28a745;
    color: white;
    border: 2px solid #28a745;
}

.download-model-btn:hover {
    background: #218838;
    border-color: #218838;
}

.remove-favorite-btn {
    background: transparent;
    color: #dc3545;
    border: 2px solid #dc3545;
}

.remove-favorite-btn:hover {
    background: #dc3545;
    color: white;
}

.empty-favorites {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 20px;
}

.empty-favorites h3 {
    font-size: 1.8rem;
    color: #333;
    margin-bottom: 10px;
}

.empty-favorites p {
    color: #666;
    font-size: 1.1rem;
    margin-bottom: 30px;
}

.browse-models-btn {
    display: inline-block;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: transform 0.3s ease;
}

.browse-models-btn:hover {
    transform: translateY(-2px);
}

.loading-message, .error-message {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    font-size: 1.2rem;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.error-message {
    color: #dc3545;
}

.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 25px;
    border-radius: 8px;
    color: white;
    font-weight: 600;
    z-index: 9999;
    max-width: 350px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@media (max-width: 768px) {
    .favorites-grid {
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
    }
    .favorites-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    .item-actions {
        gap: 8px;
    }
    .view-model-btn, .download-model-btn, .remove-favorite-btn {
        padding: 10px 14px;
        font-size: 13px;
    }
}

@media (max-width: 480px) {
    .favorites-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    .favorites-container {
        padding: 15px;
    }
}
</style>