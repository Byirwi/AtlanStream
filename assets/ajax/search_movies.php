<?php
require_once '../includes/session.php';
require_once '../config/db_connect.php';
require_once '../includes/admin-auth.php';

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
    // Préparer la requête SQL avec recherche sur le titre et la description
    $stmt = $pdo->prepare("
        SELECT * FROM movies 
        WHERE title LIKE :search OR description LIKE :search 
        ORDER BY id DESC
    ");
    
    // Exécuter la requête
    $stmt->execute(['search' => '%' . $searchTerm . '%']);
    
    // Récupérer les résultats
    $movies = $stmt->fetchAll();
    
    // Pour chaque film, récupérer les catégories associées
    foreach ($movies as &$movie) {
        $stmtCategories = $pdo->prepare("
            SELECT c.name 
            FROM categories c
            JOIN movie_categories mc ON c.id = mc.category_id
            WHERE mc.movie_id = ?
            ORDER BY c.name
        ");
        $stmtCategories->execute([$movie['id']]);
        $categories = $stmtCategories->fetchAll(PDO::FETCH_COLUMN);
        $movie['categories'] = $categories;
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
                        <a href="admin/admin_edit-film.php?id=<?= $movie['id'] ?>" class="edit-btn" title="Modifier">✏️</a>
                        <a href="admin/admin_films.php?delete=<?= $movie['id'] ?>" class="delete-btn" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce film?')">🗑️</a>
                    </div>
                <?php endif; ?>
                <div class="movie-poster">
                    <?php 
                    $poster = !empty($movie['poster_url']) && file_exists(__DIR__ . '/../public/images/' . $movie['poster_url']) 
                        ? '../public/images/' . $movie['poster_url'] 
                        : '../public/images/default.jpg';
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
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <span class="movie-category">Non catégorisé</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<p class="no-results">Aucun film ne correspond à votre recherche "' . htmlspecialchars($searchTerm) . '"</p>';
    }
    
    $html = ob_get_clean();
    
    // Retourner les résultats au format JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'count' => count($movies),
        'html' => $html
    ]);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la recherche: ' . $e->getMessage()
    ]);
    
    // Journaliser l'erreur
    error_log("Erreur lors de la recherche AJAX: " . $e->getMessage());
}
