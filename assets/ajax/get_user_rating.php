<?php
require_once '../../includes/session.php';
require_once '../../config/db_connect.php';
require_once '../../includes/film_functions.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Utilisateur non connecté'
    ]);
    exit;
}

// Récupérer l'ID du film
$movieId = isset($_GET['movie_id']) ? (int)$_GET['movie_id'] : 0;
$userId = $_SESSION['user_id'];

if ($movieId <= 0) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'ID de film invalide'
    ]);
    exit;
}

// Récupérer la note de l'utilisateur
$rating = getUserRating($movieId, $userId, $pdo);

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'rating' => $rating
]);
