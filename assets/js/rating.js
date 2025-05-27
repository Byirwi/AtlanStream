document.addEventListener('DOMContentLoaded', function() {
    const ratingForm = document.querySelector('.rating-form');
    const ratingMessage = document.querySelector('.rating-message');
    
    if (ratingForm) {
        // Récupérer l'ID du film depuis l'attribut data-movie-id
        const movieId = ratingForm.dataset.movieId;
        
        // Précharger la note de l'utilisateur s'il en a déjà donné une
        fetch(`../assets/ajax/get_user_rating.php?movie_id=${movieId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.rating) {
                    document.querySelector(`#star${data.rating}`).checked = true;
                }
            })
            .catch(error => console.error('Erreur:', error));
        
        // Gérer la soumission du formulaire
        ratingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Récupérer la note sélectionnée
            const selectedRating = document.querySelector('input[name="rating"]:checked');
            if (!selectedRating) {
                ratingMessage.textContent = 'Veuillez sélectionner une note';
                ratingMessage.className = 'rating-message error';
                return;
            }
            
            // Créer les données du formulaire
            const formData = new FormData();
            formData.append('movie_id', movieId);
            formData.append('rating', selectedRating.value);
            
            // Envoyer la note
            fetch('../assets/ajax/rate_movie.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    ratingMessage.textContent = data.message;
                    ratingMessage.className = 'rating-message success';
                    
                    // Rafraîchir la page après 1.5 secondes
                    setTimeout(() => window.location.reload(), 1500);
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
