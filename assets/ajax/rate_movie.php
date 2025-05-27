<?php
require_once '../../includes/session.php';
require_once '../../config/db_connect.php';
require_once '../../includes/film_functions.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    echo json_encode([
        'success' => false,
        'message' => 'Vous devez être connecté pour noter un film'
    ]);
    exit;
}

// Vérifier si la requête est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
    exit;
}

// Récupérer les données
$movieId = isset($_POST['movie_id']) ? (int)$_POST['movie_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$userId = $_SESSION['user_id'];

// Valider les données
if ($movieId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de film invalide'
    ]);
    exit;
}

if ($rating < 1 || $rating > 5) {
    echo json_encode([
        'success' => false,
        'message' => 'La note doit être entre 1 et 5'
    ]);
    exit;
}

// Enregistrer la note
if (rateMovie($movieId, $userId, $rating, $pdo)) {
    echo json_encode([
        'success' => true,
        'message' => 'Note enregistrée avec succès'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l\'enregistrement de la note'
    ]);
}
