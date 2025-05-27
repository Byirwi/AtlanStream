<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Journaliser la requête pour le débogage
error_log("Requête de recherche reçue: " . (isset($_GET['q']) ? $_GET['q'] : 'non défini'));

require_once '../../includes/session.php';
require_once '../../config/db_connect.php';
require_once '../../includes/admin-auth.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Utilisateur non connecté'
    ]);
    exit;
}

// Récupérer le terme de recherche
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

// Vérifier si le terme de recherche est vide
if (empty($searchTerm)) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Veuillez entrer un terme de recherche'
    ]);
    exit;
}

try {
    // Vérifier la connexion à la base de données
    $pdo->query("SELECT 1");
    error_log("Connexion à la base de données OK");
    
    // Préparer la requête SQL avec recherche sur le titre et la description
    // Utiliser des paramètres positionnels (?) au lieu de paramètres nommés (:search)
    $stmt = $pdo->prepare("
        SELECT * FROM movies 
        WHERE title LIKE ? OR description LIKE ?
        ORDER BY id DESC
    ");
    
    // Exécuter la requête avec deux paramètres identiques
    $searchParam = '%' . $searchTerm . '%';
    error_log("Exécution de la requête de recherche avec le paramètre: " . $searchParam);
    $stmt->execute([$searchParam, $searchParam]);
    
    // Récupérer les résultats
    $movies = $stmt->fetchAll();
    
    // Utiliser une requête SQL plus simple sans paramètres pour tester
    error_log("Exécution d'une requête simplifiée");
    $stmt = $pdo->query("SELECT * FROM movies ORDER BY id DESC LIMIT 10");
    
    // Récupérer les résultats
    $movies = $stmt->fetchAll();
    
    // Désactiver temporairement la récupération des catégories pour isoler le problème
    foreach ($movies as &$movie) {
        $movie['categories'] = []; // Catégories vides pour le test
    }
    unset($movie);
    
    // Générer le HTML des résultats
    ob_start();
    
    if (count($movies) > 0) {
        foreach ($movies as $movie) {
            ?>
            <div class="movie-card">
                <?php if (isAdmin()): ?>
                    <div class="admin-controls">
                        <a href="../../pages/admin/admin_edit-film.php?id=<?= $movie['id'] ?>" class="edit-btn" title="Modifier">✏️</a>
                        <a href="../../pages/admin/admin_films.php?delete=<?= $movie['id'] ?>" class="delete-btn" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce film?')">🗑️</a>
                    </div>
                <?php endif; ?>
                <div class="movie-poster">
                    <?php 
                    $poster = !empty($movie['poster_url']) && file_exists(__DIR__ . '/../../public/images/' . $movie['poster_url']) 
                        ? '../../public/images/' . $movie['poster_url'] 
                        : '../../public/images/default.jpg';
                    ?>
                    <img src="<?= $poster ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                </div>
                <div class="movie-info">
                    <h3><?= htmlspecialchars($movie['title']) ?></h3>
                    <?php if (isAdmin()): ?>
                        <small style="color: #999;">[ID: <?= $movie['id'] ?>]</small>
                    <?php endif; ?>
                    <?php if (!empty($movie['year'])): ?>
                        <div class="movie-year"><?= htmlspecialchars($movie['year']) ?></div>
                    <?php endif; ?>
                    <p><?= htmlspecialchars($movie['description']) ?></p>
                    <?php if (!empty($movie['categories'])): ?>
                        <div class="movie-categories">
                            <?php foreach ($movie['categories'] as $category): ?>
                                <span class="movie-category"><?= htmlspecialchars($category) ?></span>
                        <span class="movie-category">Non catégorisé</span>
                    <?php endif; ?>
                </div>
            </div>
            <?phpphp else: ?>
        }      <span class="movie-category">Non catégorisé</span>
    } else {   <?php endif; ?>
        echo '<p class="no-results">Aucun film ne correspond à votre recherche "' . htmlspecialchars($searchTerm) . '"</p>';       </div>
    }</div>
    
    $html = ob_get_clean();   }
    } else {
    // Retourner les résultats au format JSONresults">Aucun film ne correspond à votre recherche "' . htmlspecialchars($searchTerm) . '"</p>';
    header('Content-Type: application/json');}
    echo json_encode([
        'success' => true,
        'count' => count($movies),
        'html' => $htmltats au format JSON
    ]);ion/json');
    
} catch (PDOException $e) { 'success' => true,
    header('Content-Type: application/json');    'count' => count($movies),
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
    
    // Journaliser l'erreur avec plus de détailso json_encode([
    error_log("Erreur PDO lors de la recherche AJAX: " . $e->getMessage() . " - Trace: " . $e->getTraceAsString());    'success' => false,
    exit; ' . $e->getMessage()
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([eur avec plus de détails
        'success' => false,e AJAX: " . $e->getMessage() . " - Trace: " . $e->getTraceAsString());
        'message' => 'Erreur lors de la recherche: ' . $e->getMessage()
    ]);
    
    // Journaliser l'erreur avec plus de détailso json_encode([
    error_log("Erreur générale lors de la recherche AJAX: " . $e->getMessage() . " - Trace: " . $e->getTraceAsString());    'success' => false,
    exit;e: ' . $e->getMessage()
}
    exit;
}
