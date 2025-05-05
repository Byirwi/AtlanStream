<?php
require_once '../includes/session.php';
require_once '../config/db_connect.php';

// Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
redirectIfNotLoggedIn();
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
        </div>
        
        <div class="movies-grid">
            <?php
            // Récupérer les films depuis la base de données (limité à 30)
            try {
                $stmt = $pdo->query("SELECT movies.*, categories.name as category_name FROM movies 
                                    LEFT JOIN categories ON movies.category_id = categories.id 
                                    LIMIT 30");
                $movies = $stmt->fetchAll();
            } catch (Exception $e) {
                $movies = [];
            }
            ?>
            <?php if (empty($movies)): ?>
                <p style="color:var(--light-text);text-align:center;">Aucun film trouvé.</p>
            <?php else: ?>
                <?php foreach ($movies as $movie): ?>
                <a href="film.php?id=<?= urlencode($movie['id']) ?>" class="movie-card" style="text-decoration:none;">
                    <div class="movie-poster">
                        <?php
                        $poster = !empty($movie['poster_url']) && file_exists(__DIR__ . '/../public/images/' . $movie['poster_url'])
                            ? '../public/images/' . htmlspecialchars($movie['poster_url'])
                            : '../public/images/default.jpg';
                        ?>
                        <img src="<?= $poster ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                    </div>
                    <div class="movie-info">
                        <h3><?= htmlspecialchars($movie['title']) ?></h3>
                        <p><?= htmlspecialchars($movie['description']) ?></p>
                        <span class="movie-category"><?= htmlspecialchars($movie['category_name'] ?? 'Non catégorisé') ?></span>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2025 AtlanStream - Tous droits réservés</p>
    </footer>
    
    <script src="../assets/js/theme.js"></script>
</body>
</html>
