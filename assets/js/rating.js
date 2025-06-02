document.addEventListener('DOMContentLoaded', function() {
    const ratingForm = document.querySelector('.rating-form');
    const ratingMessage = document.querySelector('.rating-message');
    
    if (ratingForm) {
        console.log('Formulaire de notation trouvé');
        // Récupérer l'ID du film
        const movieId = ratingForm.dataset.movieId;
        console.log('ID du film:', movieId);
        
        // Précharger la note de l'utilisateur s'il en a déjà donné une
        fetch(`../../assets/ajax/get_user_rating.php?movie_id=${movieId}`)
            .then(response => {
                console.log('Réponse get_user_rating:', response);
                return response.json();
            })
            .then(data => {
                console.log('Données get_user_rating:', data);
                if (data.success && data.rating) {
                    document.querySelector(`#star${data.rating}`).checked = true;
                }
            })
            .catch(error => console.error('Erreur lors de la récupération de la note:', error));
        
        // Soumettre la note
        ratingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Soumission du formulaire de notation');
            
            // Récupérer la note sélectionnée
            const selectedRating = document.querySelector('input[name="rating"]:checked');
            if (!selectedRating) {
                ratingMessage.textContent = 'Veuillez sélectionner une note';
                ratingMessage.className = 'rating-message error';
                return;
            }
            
            const formData = new FormData();
            formData.append('movie_id', movieId);
            formData.append('rating', selectedRating.value);
            
            console.log('Envoi de la note:', selectedRating.value, 'pour le film:', movieId);
            
            fetch('../../assets/ajax/rate_movie.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Réponse rate_movie:', response);
                return response.json();
            })
            .then(data => {
                console.log('Données rate_movie:', data);
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
                console.error('Erreur lors de la notation:', error);
                ratingMessage.textContent = 'Une erreur s\'est produite';
                ratingMessage.className = 'rating-message error';
            });
        });
    }
});
