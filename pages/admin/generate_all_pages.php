<?php
require_once '../../includes/session.php';
require_once '../../config/db_connect.php';
require_once '../../includes/admin-auth.php';
require_once '../../includes/film_functions.php';

// Vérification des droits admin
redirectIfNotAdmin();

$results = [
    'success' => 0,
    'failed' => 0,
    'errors' => []
];

// Récupérer tous les IDs de films
try {
    $stmt = $pdo->query("SELECT id FROM movies");
    $filmIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($filmIds as $filmId) {
        if (generateFilmPage($filmId, $pdo)) {
            $results['success']++;
        } else {
            $results['failed']++;
            $results['errors'][] = "Échec pour le film ID: " . $filmId;
        }
    }
    
    $_SESSION['success'] = "Génération terminée. Succès: " . $results['success'] . ", Échecs: " . $results['failed'];
    
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur lors de la génération des pages: " . $e->getMessage();
}

// Rediriger vers le tableau de bord
header("Location: admin_dashboard.php");
exit;
?>
