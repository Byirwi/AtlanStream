document.addEventListener('DOMContentLoaded', function() {
    const ratingForm = document.querySelector('.rating-form');
    const ratingMessage = document.querySelector('.rating-message');
    
    if (ratingForm) {
        // Pré-remplir la note de l'utilisateur s'il en a déjà donné une
        fetch(`../../assets/ajax/get_user_rating.php?movie_id=${ratingForm.dataset.movieId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.rating) {
                    document.querySelector(`#star${data.rating}`).checked = true;
                }
            })
            .catch(error => console.error('Erreur:', error));
        
        // Soumettre la note
        ratingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('movie_id', this.dataset.movieId);
            
            fetch('../../assets/ajax/rate_movie.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    ratingMessage.textContent = data.message;
                    ratingMessage.className = 'rating-message success';
                    
                    // Rafraîchir la page après 1 seconde pour mettre à jour la note moyenne
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    ratingMessage.textContent = data.message;
                    ratingMessage.className = 'rating-message error';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                ratingMessage.textContent = 'Une erreur s\'est produite';
                ratingMessage.className = 'rating-message error';
            });
        });
    }
});
