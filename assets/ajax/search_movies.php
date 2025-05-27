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
    if (empty($searchTerm)) {
        throw new Exception("Veuillez entrer un terme de recherche");
    }
    
    // Requête de recherche simplifiée
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE title LIKE ? ORDER BY id DESC LIMIT 20");
    $stmt->execute(['%' . $searchTerm . '%']);
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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
            $html .= '<span class="movie-category">Film</span>';
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
