<?php
require_once '../includes/session.php';
require_once '../config/db_connect.php';
require_once '../includes/admin-auth.php';

// Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
redirectIfNotLoggedIn();

// Récupérer les films depuis la base de données
try {
    $stmt = $pdo->query("SELECT movies.*, categories.name as category_name FROM movies 
                         LEFT JOIN categories ON movies.category_id = categories.id 
                         LIMIT 30");
    $movies = $stmt->fetchAll();
} catch (Exception $e) {
    // En cas d'erreur, initialiser $movies comme un tableau vide
    $movies = [];
    // Éventuellement journaliser l'erreur
    error_log("Erreur lors de la récupération des films: " . $e->getMessage());
}

// Si aucun film n'a été trouvé ou en cas d'erreur, créer des exemples statiques
if (empty($movies)) {
    $movies = [
        [
            'id' => 1,
            'title' => 'La Cité Perdue',
            'description' => 'Un explorateur découvre les vestiges d\'une civilisation sous-marine avancée.',
            'poster_url' => 'default.jpg',
            'category_name' => 'Aventure'
        ],
        [
            'id' => 2,
            'title' => 'Les Secrets d\'Atlantis',
            'description' => 'Une équipe de scientifiques révèle les mystères de la technologie atlante.',
            'poster_url' => 'default.jpg',
            'category_name' => 'Documentaire'
        ],
        // Ajoutez d'autres films statiques si nécessaire
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AtlanStream - Catalogue</title>
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
                <li><span class="welcome-user">Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                <li><a href="compte.php">Mon compte</a></li>
                <?php if (isAdmin()): ?>
                    <li><a href="#" class="admin-dropdown-toggle">Admin <span>▼</span></a>
                        <ul class="admin-dropdown">
                            <li><a href="../admin/dashboard.php">Tableau de bord</a></li>
                            <li><a href="../admin/films.php">Gérer les films</a></li>
                            <li><a href="../admin/categories.php">Gérer les catégories</a></li>
                            <li><a href="../admin/users.php">Gérer les utilisateurs</a></li>
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
            <h2>Catalogue des films</h2>
            <p>Découvrez notre sélection de films sur l'Atlantide</p>
            <?php if (isAdmin()): ?>
                <a href="../admin/edit-film.php" class="btn btn-primary">Ajouter un nouveau film</a>
            <?php endif; ?>
        </div>
        
        <div class="movies-grid">
            <?php foreach ($movies as $movie): ?>
                <div class="movie-card">
                    <?php if (isAdmin()): ?>
                        <div class="admin-controls">
                            <a href="../admin/edit-film.php?id=<?= $movie['id'] ?>" class="edit-btn" title="Modifier">✏️</a>
                            <a href="../admin/films.php?delete=<?= $movie['id'] ?>" class="delete-btn" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce film?')">🗑️</a>
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
                        <p><?= htmlspecialchars($movie['description']) ?></p>
                        <span class="movie-category"><?= htmlspecialchars($movie['category_name'] ?? 'Non catégorisé') ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
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
