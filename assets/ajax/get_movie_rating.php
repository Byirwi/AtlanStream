<?php
require_once '../../includes/session.php';
require_once '../../config/db_connect.php';
require_once '../../includes/film_functions.php';

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Récupérer l'ID du film
$movieId = isset($_GET['movie_id']) ? (int)$_GET['movie_id'] : 0;

if ($movieId <= 0) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'ID de film invalide'
    ]);
    exit;
}

// Récupérer la note moyenne du film
try {
    $rating = getMovieRating($movieId, $pdo);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'rating' => $rating
    ]);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
?>
