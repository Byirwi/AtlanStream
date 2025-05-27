<?php
require_once '../../includes/session.php';
require_once '../../config/db_connect.php';
require_once '../../includes/film_functions.php';

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Journaliser les données reçues
error_log("rate_movie.php appelé avec POST: " . print_r($_POST, true));

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Vous devez être connecté pour noter un film'
    ]);
    exit;
}

// Vérifier si la requête est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
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
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'ID de film invalide'
    ]);
    exit;
}

if ($rating < 1 || $rating > 5) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'La note doit être entre 1 et 5'
    ]);
    exit;
}

// Enregistrer la note
try {
    $success = rateMovie($movieId, $userId, $rating, $pdo);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Note enregistrée avec succès' : 'Erreur lors de l\'enregistrement de la note'
    ]);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
