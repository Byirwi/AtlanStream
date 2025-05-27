document.addEventListener('DOMContentLoaded', function() {
    const favoriteBtn = document.querySelector('.favorite-btn');
    
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', function() {
            const movieId = this.dataset.movieId;
            const isFavorite = this.classList.contains('is-favorite');
            const action = isFavorite ? 'remove' : 'add';
            
            fetch('../../assets/ajax/toggle_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `movie_id=${movieId}&action=${action}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (action === 'add') {
                        this.classList.add('is-favorite');
                        this.title = 'Retirer des favoris';
                    } else {
                        this.classList.remove('is-favorite');
                        this.title = 'Ajouter aux favoris';
                    }
                }
            })
            .catch(error => console.error('Erreur:', error));
        });
    }
});
