<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Journaliser l'appel pour déboguer
error_log("Appel à search_movies.php avec q=" . (isset($_GET['q']) ? $_GET['q'] : 'non défini'));

try {
    // Inclure les fichiers nécessaires
    require_once '../../includes/session.php';
    require_once '../../config/db_connect.php';
    require_once '../../includes/admin-auth.php';
    
    // Vérifier la connexion
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("Utilisateur non connecté");
    }
    
    // Récupérer le terme de recherche
    $searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
    
    // Récupérer les catégories sélectionnées
    $selectedCategories = [];
    if (isset($_GET['categories'])) {
        $selectedCategories = array_map('intval', explode(',', $_GET['categories']));
    }
    
    // Construire la requête SQL en fonction des filtres
    if (!empty($selectedCategories)) {
        // Recherche avec filtres de catégorie
        $sql = "SELECT DISTINCT m.* FROM movies m 
                JOIN movie_categories mc ON m.id = mc.movie_id 
                WHERE m.title LIKE ? AND mc.category_id IN (" . implode(',', array_fill(0, count($selectedCategories), '?')) . ") 
                ORDER BY m.id DESC LIMIT 50";
        
        $params = array_merge(['%' . $searchTerm . '%'], $selectedCategories);
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    } else {
        // Recherche simple sans filtres de catégorie
        $stmt = $pdo->prepare("SELECT * FROM movies WHERE title LIKE ? ORDER BY id DESC LIMIT 20");
        $stmt->execute(['%' . $searchTerm . '%']);
    }
    
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Pour chaque film, récupérer les catégories associées
    foreach ($movies as &$movie) {
        $stmtCategories = $pdo->prepare("
            SELECT c.name, c.id
            FROM categories c
            JOIN movie_categories mc ON c.id = mc.category_id
            WHERE mc.movie_id = ?
            ORDER BY c.name
        ");
        $stmtCategories->execute([$movie['id']]);
        $categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);
        $movie['categories'] = $categories;
    }
    unset($movie);
    
    // Générer une réponse HTML
    $html = '';
    if (count($movies) > 0) {
        foreach ($movies as $movie) {
            $html .= '<div class="movie-card">';
            
            // Admin controls
            if (isAdmin()) {
                $html .= '<div class="admin-controls">';
                $html .= '<a href="../../pages/admin/admin_edit-film.php?id=' . $movie['id'] . '" class="edit-btn" title="Modifier">✏️</a>';
                $html .= '<a href="../../pages/admin/admin_films.php?delete=' . $movie['id'] . '" class="delete-btn" title="Supprimer" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer ce film?\')">🗑️</a>';
                $html .= '</div>';
            }
            
            // Poster
            $poster = !empty($movie['poster_url']) && file_exists(__DIR__ . '/../../public/images/' . $movie['poster_url']) 
                ? '../../public/images/' . $movie['poster_url'] 
                : '../../public/images/default.jpg';
            
            // Vérifier si la page de détail existe
            $detailPage = '../../public/films/film_' . $movie['id'] . '.html';
            $detailExists = file_exists(__DIR__ . '/../' . $detailPage);
            
            $html .= '<div class="movie-poster">';
            $html .= '<img src="' . $poster . '" alt="' . htmlspecialchars($movie['title']) . '">';
            
            if ($detailExists) {
                $html .= '<a href="' . $detailPage . '" class="view-details">Voir détails</a>';
            }
            
            $html .= '</div>';
            
            // Movie info
            $html .= '<div class="movie-info">';
            $html .= '<h3>' . htmlspecialchars($movie['title']) . '</h3>';
            
            if (isAdmin()) {
                $html .= '<small style="color: #999;">[ID: ' . $movie['id'] . ']</small>';
            }
            
            if (!empty($movie['year'])) {
                $html .= '<div class="movie-year">' . htmlspecialchars($movie['year']) . '</div>';
            }
            
            $html .= '<p>' . htmlspecialchars($movie['description']) . '</p>';
            
            // Afficher les catégories
            if (!empty($movie['categories'])) {
                $html .= '<div class="movie-categories">';
                foreach ($movie['categories'] as $category) {
                    $html .= '<span class="movie-category">' . htmlspecialchars($category['name']) . '</span>';
                }
                $html .= '</div>';
            } else {
                $html .= '<span class="movie-category">Non catégorisé</span>';
            }
            
            $html .= '</div>'; // End movie-info
            $html .= '</div>'; // End movie-card
        }
    } else {
        $html = '<p class="no-results">Aucun film ne correspond à votre recherche "' . htmlspecialchars($searchTerm) . '"</p>';
    }
    
    // Retourner le résultat en JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'count' => count($movies),
        'html' => $html,
        'query' => $searchTerm
    ]);
    
} catch (Exception $e) {
    // Journaliser l'erreur
    error_log("Erreur dans search_movies.php: " . $e->getMessage());
    
    // Retourner l'erreur en JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => 'Exception attrapée dans le fichier search_movies.php'
    ]);
}
