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
?>
