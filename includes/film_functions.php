<?php
/**
 * Fonctions utilitaires pour les films
 */

/**
 * Génère une page HTML statique pour un film donné
 * 
 * @param int $filmId ID du film
 * @param PDO $pdo Connexion PDO à la base de données
 * @return bool Succès de la génération
 */
function generateFilmPage($filmId, $pdo) {
    try {
        // Récupérer les informations du film
        $stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
        $stmt->execute([$filmId]);
        $film = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$film) {
            return false;
        }
        
        // Récupérer les catégories du film
        $stmtCategories = $pdo->prepare("
            SELECT c.name 
            FROM categories c
            JOIN movie_categories mc ON c.id = mc.category_id
            WHERE mc.movie_id = ?
            ORDER BY c.name
        ");
        $stmtCategories->execute([$filmId]);
        $categories = $stmtCategories->fetchAll(PDO::FETCH_COLUMN);
        
        // Créer le dossier des pages de films s'il n'existe pas
        $pagesDir = __DIR__ . '/../public/films/';
        if (!file_exists($pagesDir)) {
            mkdir($pagesDir, 0777, true);
        }
        
        // Déterminer le chemin de l'affiche
        $posterPath = '../../public/images/' . ($film['poster_url'] ?: 'default.jpg');
        if (!file_exists(__DIR__ . '/../public/images/' . $film['poster_url']) && $film['poster_url']) {
            $posterPath = '../../public/images/default.jpg';
        }
        
        // Générer le contenu HTML
        ob_start();
        include __DIR__ . '/../templates/film_template.php';
        $htmlContent = ob_get_clean();
        
        // Écrire le fichier HTML
        $filename = $pagesDir . 'film_' . $filmId . '.html';
        file_put_contents($filename, $htmlContent);
        
        return true;
    } catch (Exception $e) {
        error_log("Erreur lors de la génération de la page du film: " . $e->getMessage());
        return false;
    }
}

/**
 * Supprime la page HTML d'un film
 * 
 * @param int $filmId ID du film
 * @return bool Succès de la suppression
 */
function deleteFilmPage($filmId) {
    $filename = __DIR__ . '/../public/films/film_' . $filmId . '.html';
    if (file_exists($filename)) {
        return unlink($filename);
    }
    return true; // Le fichier n'existe pas, donc considéré comme succès
}

/**
 * Récupère la note moyenne d'un film
 * 
 * @param int $filmId ID du film
 * @param PDO $pdo Connexion PDO à la base de données
 * @return array Note moyenne et nombre de votes
 */
function getMovieRating($filmId, $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT AVG(rating) as average, COUNT(*) as count FROM movie_ratings WHERE movie_id = ?");
        $stmt->execute([$filmId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'average' => round($result['average'] ?? 0, 1),
            'count' => (int)$result['count']
        ];
    } catch (Exception $e) {
        error_log("Erreur lors de la récupération de la note du film: " . $e->getMessage());
        return ['average' => 0, 'count' => 0];
    }
}

/**
 * Ajoute ou met à jour la note d'un utilisateur pour un film
 * 
 * @param int $filmId ID du film
 * @param int $userId ID de l'utilisateur
 * @param int $rating Note (1-5)
 * @param PDO $pdo Connexion PDO à la base de données
 * @return bool Succès de l'opération
 */
function rateMovie($filmId, $userId, $rating, $pdo) {
    try {
        // Vérifier si l'utilisateur a déjà noté ce film
        $stmt = $pdo->prepare("SELECT id FROM movie_ratings WHERE movie_id = ? AND user_id = ?");
        $stmt->execute([$filmId, $userId]);
        $exists = $stmt->fetch();
        
        if ($exists) {
            // Mettre à jour la note existante
            $stmt = $pdo->prepare("UPDATE movie_ratings SET rating = ? WHERE movie_id = ? AND user_id = ?");
            return $stmt->execute([$rating, $filmId, $userId]);
        } else {
            // Créer une nouvelle note
            $stmt = $pdo->prepare("INSERT INTO movie_ratings (movie_id, user_id, rating) VALUES (?, ?, ?)");
            return $stmt->execute([$filmId, $userId, $rating]);
        }
    } catch (Exception $e) {
        error_log("Erreur lors de la notation du film: " . $e->getMessage());
        return false;
    }
}

/**
 * Récupère la note donnée par un utilisateur à un film
 * 
 * @param int $filmId ID du film
 * @param int $userId ID de l'utilisateur
 * @param PDO $pdo Connexion PDO à la base de données
 * @return int|null Note de l'utilisateur ou null si pas de note
 */
function getUserRating($filmId, $userId, $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT rating FROM movie_ratings WHERE movie_id = ? AND user_id = ?");
        $stmt->execute([$filmId, $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? (int)$result['rating'] : null;
    } catch (Exception $e) {
        error_log("Erreur lors de la récupération de la note utilisateur: " . $e->getMessage());
        return null;
    }
}

/**
 * Récupère tous les films favoris d'un utilisateur
 * 
 * @param int $userId ID de l'utilisateur
 * @param PDO $pdo Connexion PDO à la base de données
 * @return array Liste des films favoris
 */
function getFavoriteMovies($userId, $pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT m.*
            FROM movies m
            JOIN favorites f ON m.id = f.movie_id
            WHERE f.user_id = ?
            ORDER BY f.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Erreur lors de la récupération des favoris: " . $e->getMessage());
        return [];
    }
}

/**
 * Ajoute un film aux favoris d'un utilisateur
 * 
 * @param int $filmId ID du film
 * @param int $userId ID de l'utilisateur
 * @param PDO $pdo Connexion PDO à la base de données
 * @return bool Succès de l'opération
 */
function addToFavorites($filmId, $userId, $pdo) {
    try {
        // Vérifier si le film est déjà dans les favoris
        $stmt = $pdo->prepare("SELECT id FROM favorites WHERE movie_id = ? AND user_id = ?");
        $stmt->execute([$filmId, $userId]);
        if ($stmt->fetch()) {
            // Déjà dans les favoris
            return true;
        }
        
        // Ajouter aux favoris
        $stmt = $pdo->prepare("INSERT INTO favorites (movie_id, user_id, created_at) VALUES (?, ?, NOW())");
        return $stmt->execute([$filmId, $userId]);
    } catch (Exception $e) {
        error_log("Erreur lors de l'ajout aux favoris: " . $e->getMessage());
        return false;
    }
}

/**
 * Retire un film des favoris d'un utilisateur
 * 
 * @param int $filmId ID du film
 * @param int $userId ID de l'utilisateur
 * @param PDO $pdo Connexion PDO à la base de données
 * @return bool Succès de l'opération
 */
function removeFromFavorites($filmId, $userId, $pdo) {
    try {
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE movie_id = ? AND user_id = ?");
        return $stmt->execute([$filmId, $userId]);
    } catch (Exception $e) {
        error_log("Erreur lors de la suppression des favoris: " . $e->getMessage());
        return false;
    }
}

/**
 * Vérifie si un film est dans les favoris d'un utilisateur
 * 
 * @param int $filmId ID du film
 * @param int $userId ID de l'utilisateur
 * @param PDO $pdo Connexion PDO à la base de données
 * @return bool Le film est dans les favoris
 */
function isInFavorites($filmId, $userId, $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM favorites WHERE movie_id = ? AND user_id = ?");
        $stmt->execute([$filmId, $userId]);
        return $stmt->fetch() !== false;
    } catch (Exception $e) {
        error_log("Erreur lors de la vérification des favoris: " . $e->getMessage());
        return false;
    }
}
?>
