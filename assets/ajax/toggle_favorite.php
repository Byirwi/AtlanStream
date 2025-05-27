<?php
require_once '../../includes/session.php';
require_once '../../config/db_connect.php';
require_once '../../includes/film_functions.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Vous devez être connecté pour gérer vos favoris'
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
$action = isset($_POST['action']) ? $_POST['action'] : '';
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

if (!in_array($action, ['add', 'remove'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Action invalide'
    ]);
    exit;
}

// Exécuter l'action
$success = false;
if ($action === 'add') {
    $success = addToFavorites($movieId, $userId, $pdo);
    $message = 'Film ajouté aux favoris';
} else {
    $success = removeFromFavorites($movieId, $userId, $pdo);
    $message = 'Film retiré des favoris';
}

header('Content-Type: application/json');
echo json_encode([
    'success' => $success,
    'message' => $success ? $message : 'Erreur lors de la gestion des favoris'
]);
