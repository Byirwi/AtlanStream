document.addEventListener('DOMContentLoaded', function() {
    const favoriteBtn = document.querySelector('.favorite-btn');
    
    if (favoriteBtn) {
        console.log('Bouton favoris trouvé');
        
        favoriteBtn.addEventListener('click', function() {
            const movieId = this.dataset.movieId;
            const isFavorite = this.classList.contains('is-favorite');
            const action = isFavorite ? 'remove' : 'add';
            
            console.log('Toggle favoris:', action, 'pour le film:', movieId);
            
            const formData = new FormData();
            formData.append('movie_id', movieId);
            formData.append('action', action);
            
            fetch('../../assets/ajax/toggle_favorite.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Réponse toggle_favorite:', response);
                return response.json();
            })
            .then(data => {
                console.log('Données toggle_favorite:', data);
                if (data.success) {
                    if (action === 'add') {
                        this.classList.add('is-favorite');
                        this.title = 'Retirer des favoris';
                    } else {
                        this.classList.remove('is-favorite');
                        this.title = 'Ajouter aux favoris';
                    }
                    
                    // Afficher un message de confirmation temporaire
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erreur lors de la gestion des favoris:', error);
                showNotification('Une erreur s\'est produite lors de la gestion des favoris', 'error');
            });
        });
    }
    
    // Fonction pour afficher des notifications temporaires
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 6px;
            color: white;
            font-weight: 500;
            z-index: 1000;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
            ${type === 'success' ? 'background-color: #48bb78;' : 'background-color: #e53e3e;'}
        `;
        
        document.body.appendChild(notification);
        
        // Animer l'entrée
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Supprimer après 3 secondes
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
});
