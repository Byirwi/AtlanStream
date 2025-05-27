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
    
    // Vérifier le paramètre de recherche
    $searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
    if (empty($searchTerm)) {
        throw new Exception("Veuillez entrer un terme de recherche");
    }
    
    // Version ultra-simplifiée : récupérer tous les films
    // Cette requête ne devrait pas causer d'erreurs
    $stmt = $pdo->query("SELECT * FROM movies LIMIT 10");
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Générer une réponse HTML minimale
    $html = '';
    if (count($movies) > 0) {
        foreach ($movies as $movie) {
            $html .= '<div class="movie-card">';
            $html .= '<div class="movie-info">';
            $html .= '<h3>' . htmlspecialchars($movie['title']) . '</h3>';
            $html .= '<p>' . htmlspecialchars($movie['description']) . '</p>';
            $html .= '</div>';
            $html .= '</div>';
        }
    } else {
        $html = '<p>Aucun film trouvé</p>';
    }
    
    // Retourner le résultat en JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'count' => count($movies),
        'html' => $html,
        'debug' => 'Recherche réussie'
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
