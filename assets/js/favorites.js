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
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Erreur lors de la gestion des favoris:', error);
                alert('Une erreur s\'est produite lors de la gestion des favoris');
            });
        });
    }
});
