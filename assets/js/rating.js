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
                    const starElement = document.querySelector(`#star${data.rating}`);
                    if (starElement) {
                        starElement.checked = true;
                    }
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
                    
                    // Mettre à jour l'affichage de la note moyenne immédiatement
                    updateRatingDisplay(movieId);
                    
                    // Effacer le message après 3 secondes
                    setTimeout(() => {
                        ratingMessage.textContent = '';
                        ratingMessage.className = 'rating-message';
                    }, 3000);
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
    
    // Fonction pour mettre à jour l'affichage de la note moyenne
    function updateRatingDisplay(movieId) {
        fetch(`../../assets/ajax/get_movie_rating.php?movie_id=${movieId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const ratingDisplay = document.querySelector('.rating-display');
                    if (ratingDisplay) {
                        const stars = ratingDisplay.querySelectorAll('.star');
                        const ratingText = ratingDisplay.querySelector('.rating-text');
                        const ratingCount = ratingDisplay.querySelector('.rating-count');
                        
                        // Mettre à jour les étoiles
                        stars.forEach((star, index) => {
                            if (index < Math.round(data.rating.average)) {
                                star.classList.add('filled');
                            } else {
                                star.classList.remove('filled');
                            }
                        });
                        
                        // Mettre à jour le texte de la note
                        if (ratingText) {
                            ratingText.textContent = `${data.rating.average}/5`;
                        }
                        
                        if (ratingCount) {
                            ratingCount.textContent = `(${data.rating.count} vote${data.rating.count > 1 ? 's' : ''})`;
                        }
                        
                        // Animation de mise à jour
                        ratingDisplay.style.transform = 'scale(1.1)';
                        setTimeout(() => {
                            ratingDisplay.style.transform = 'scale(1)';
                        }, 300);
                    }
                }
            })
            .catch(error => {
                console.error('Erreur lors de la mise à jour de l\'affichage:', error);
            });
    }
});
