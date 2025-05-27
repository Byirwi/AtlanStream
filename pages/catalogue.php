<?php
require_once '../includes/session.php';
require_once '../config/db_connect.php';
require_once '../includes/admin-auth.php';

// Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
redirectIfNotLoggedIn();

// Récupérer les films depuis la base de données
try {
    // Récupérer les films de base
    $stmt = $pdo->query("SELECT * FROM movies ORDER BY id DESC LIMIT 30");
    $movies = $stmt->fetchAll();
    
    // Pour chaque film, récupérer les catégories associées
    foreach ($movies as &$movie) {
        // Utiliser un nom de variable différent pour éviter les conflits
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
    // Important: libérer la référence
    unset($movie);
    
} catch (Exception $e) {
    // En cas d'erreur, initialiser $movies comme un tableau vide
    $movies = [];
    // Éventuellement journaliser l'erreur
    error_log("Erreur lors de la récupération des films: " . $e->getMessage());
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
            <h2>Catalogue des films</h2>
            <p>Découvrez notre sélection de films sur l'Atlantide</p>
            <div class="search-container">
                <div class="search-box">
                    <input type="text" id="search-input" class="search-input" placeholder="Rechercher un film...">
                    <button id="search-button" class="search-button">Rechercher</button>
                </div>
            </div>
            <?php if (isAdmin()): ?>
                <a href="admin/admin_edit-film.php" class="btn btn-primary">Ajouter un nouveau film</a>
            <?php endif; ?>
        </div>
        
        <div class="loading" id="loading-indicator">
            <p>Recherche en cours...</p>
        </div>
        
        <div class="movies-grid" id="search-results">
            <?php foreach ($movies as $movie): ?>
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
                        <!-- Affichage de l'ID pour le débogage -->
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
            <?php endforeach; ?>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2025 AtlanStream - Tous droits réservés</p>
    </footer>
    
    <script src="../assets/js/theme.js"></script>
    
    <!-- Script pour la recherche AJAX -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const searchButton = document.getElementById('search-button');
        const searchResults = document.getElementById('search-results');
        const loadingIndicator = document.getElementById('loading-indicator');
        
        // Fonction pour effectuer la recherche
        function performSearch() {
            const searchTerm = searchInput.value.trim();
            
            // Ne rien faire si la recherche est vide
            if (searchTerm === '') {
                return;
            }
            
            // Afficher l'indicateur de chargement
            loadingIndicator.style.display = 'block';
            
            // Utiliser le fichier de recherche principal
            fetch('../assets/ajax/search_movies.php?q=' + encodeURIComponent(searchTerm))
                .then(response => {
                    console.log('Réponse reçue:', response);
                    if (!response.ok) {
                        throw new Error('Erreur réseau: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Données reçues:', data);
                    loadingIndicator.style.display = 'none';
                    
                    if (data.success) {
                        // Mettre à jour les résultats de recherche
                        searchResults.innerHTML = data.html;
                        console.log('Nombre de résultats:', data.count);
                    } else {
                        // Afficher un message d'erreur
                        searchResults.innerHTML = '<p class="error-message">' + data.message + '</p>';
                    }
                })
                .catch(error => {
                    console.error('Erreur AJAX:', error);
                    loadingIndicator.style.display = 'none';
                    searchResults.innerHTML = '<p class="error-message">Erreur: ' + error.message + '</p>';
                });
        }
        
        // Événement de clic sur le bouton de recherche
        searchButton.addEventListener('click', performSearch);
        
        // Événement de touche Entrée dans le champ de recherche
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    });
    </script>
    
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
