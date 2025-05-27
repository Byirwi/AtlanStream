<?php
require_once '../includes/session.php';
require_once '../config/db_connect.php';
require_once '../includes/admin-auth.php';
require_once '../includes/film_functions.php';

// Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
redirectIfNotLoggedIn();

// Récupérer les films favoris de l'utilisateur
$favoriteMovies = getFavoriteMovies($_SESSION['user_id'], $pdo);

// Pour chaque film, récupérer les catégories associées
foreach ($favoriteMovies as &$movie) {
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
// Libérer la référence
unset($movie);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Favoris - AtlanStream</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dark">
    <header>
        <div class="logo">
            <h1>AtlanStream</h1>
        </div>
        <nav>
            <ul>
                <li><a href="Accueil.php">Accueil</a></li>
                <li><a href="catalogue.php">Catalogue</a></li>
                <li><span class="welcome-user">Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                <li><a href="compte.php">Mon compte</a></li>
                <?php if (isAdmin()): ?>
                    <li><a href="#" class="admin-dropdown-toggle">Admin <span>▼</span></a>
                        <ul class="admin-dropdown">
                            <li><a href="admin/admin_dashboard.php">Tableau de bord</a></li>
                            <li><a href="admin/admin_films.php">Gérer les films</a></li>
                            <li><a href="admin/admin_categories.php">Gérer les catégories</a></li>
                            <li><a href="admin/admin_users.php">Gérer les utilisateurs</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <li><a href="logout.php" class="logout-btn">Déconnexion</a></li>
                <li>
                    <button id="theme-toggle" class="theme-toggle" title="Changer de thème">
                        <span id="theme-icon">☀️</span>
                    </button>
                </li>
            </ul>
        </nav>
    </header>
    
    <main>
        <div class="catalogue-header">
            <h2>Mes Films Favoris</h2>
            <p>Retrouvez ici tous vos films préférés</p>
        </div>
        
        <div class="movies-grid">
            <?php if (empty($favoriteMovies)): ?>
                <div class="no-results">
                    <p>Vous n'avez pas encore de films favoris.</p>
                    <a href="catalogue.php" class="btn btn-primary" style="margin-top:20px;">Parcourir le catalogue</a>
                </div>
            <?php else: ?>
                <?php foreach ($favoriteMovies as $movie): ?>
                    <div class="movie-card">
                        <div class="movie-poster">
                            <?php 
                            $poster = !empty($movie['poster_url']) && file_exists(__DIR__ . '/../public/images/' . $movie['poster_url']) 
                                ? '../public/images/' . $movie['poster_url'] 
                                : '../public/images/default.jpg';
                            
                            // Vérifier si la page de détail existe
                            $detailPage = '../public/films/film_' . $movie['id'] . '.html';
                            $detailExists = file_exists(__DIR__ . '/' . $detailPage);
                            ?>
                            <img src="<?= $poster ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                            <?php if ($detailExists): ?>
                                <a href="<?= $detailPage ?>" class="view-details">Voir détails</a>
                            <?php endif; ?>
                        </div>
                        <div class="movie-info">
                            <h3><?= htmlspecialchars($movie['title']) ?></h3>
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
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2025 AtlanStream - Tous droits réservés</p>
    </footer>
    
    <script src="../assets/js/theme.js"></script>
    
    <?php if (isAdmin()): ?>
    <script>
        // Script pour le menu déroulant admin
        document.addEventListener('DOMContentLoaded', function() {
            const adminToggle = document.querySelector('.admin-dropdown-toggle');
            const adminMenu = document.querySelector('.admin-dropdown');
            
            if (adminToggle) {
                adminToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    adminMenu.classList.toggle('active');
                });
                
                // Fermer le menu au clic à l'extérieur
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.admin-dropdown') && !e.target.closest('.admin-dropdown-toggle')) {
                        adminMenu.classList.remove('active');
                    }
                });
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
