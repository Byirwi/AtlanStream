<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($film['title']) ?> - AtlanStream</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/rating.css">
</head>
<body class="dark">
    <header>
        <div class="logo">
            <h1>AtlanStream</h1>
        </div>
        <nav>
            <ul>
                <li><a href="../../pages/Accueil.php">Accueil</a></li>
                <li><a href="../../pages/catalogue.php">Catalogue</a></li>
                <li><a href="../../pages/favoris.php">Mes Favoris</a></li>
                <li><a href="../../pages/compte.php">Mon compte</a></li>
                <li><a href="../../pages/logout.php" class="logout-btn">Déconnexion</a></li>
                <li>
                    <button id="theme-toggle" class="theme-toggle" title="Changer de thème">
                        <span id="theme-icon">☀️</span>
                    </button>
                </li>
            </ul>
        </nav>
    </header>
    
    <main>
        <div class="film-detail">
            <div class="film-header">
                <div class="film-poster">
                    <img src="<?= $posterPath ?>" alt="<?= htmlspecialchars($film['title']) ?>">
                </div>
                <div class="film-info">
                    <div class="film-title-row">
                        <h1 class="film-title"><?= htmlspecialchars($film['title']) ?></h1>
                        
                        <!-- Bouton favoris -->
                        <?php $isFavorite = isInFavorites($film['id'], $_SESSION['user_id'] ?? 0, $pdo); ?>
                        <button class="favorite-btn <?= $isFavorite ? 'is-favorite' : '' ?>" 
                                data-movie-id="<?= $film['id'] ?>"
                                title="<?= $isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>">
                            <span class="favorite-icon">❤</span>
                        </button>
                    </div>
                    
                    <!-- Affichage de la note moyenne -->
                    <?php $rating = getMovieRating($film['id'], $pdo); ?>
                    <div class="rating-display">
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star <?= ($i <= round($rating['average'])) ? 'filled' : '' ?>">★</span>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-count"><?= $rating['average'] ?>/5 (<?= $rating['count'] ?> votes)</span>
                    </div>
                    
                    <?php if (!empty($film['year'])): ?>
                        <div class="film-year">Année: <?= htmlspecialchars($film['year']) ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($film['director'])): ?>
                        <div class="film-director">Réalisateur: <?= htmlspecialchars($film['director']) ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($film['actors'])): ?>
                        <div class="film-actors">Acteurs: <?= htmlspecialchars($film['actors']) ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($categories)): ?>
                        <div class="film-categories">
                            Catégories: 
                            <?php foreach ($categories as $index => $category): ?>
                                <span class="film-category"><?= htmlspecialchars($category) ?></span>
                                <?= ($index < count($categories) - 1) ? ', ' : '' ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="film-description">
                        <h2>Synopsis</h2>
                        <p><?= htmlspecialchars($film['description']) ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Système de notation -->
            <div class="rating-container">
                <h3>Notez ce film</h3>
                <form class="rating-form" data-movie-id="<?= $film['id'] ?>">
                    <div class="rating-stars">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" />
                            <label for="star<?= $i ?>" title="<?= $i ?> étoile<?= $i > 1 ? 's' : '' ?>">★</label>
                        <?php endfor; ?>
                    </div>
                    <button type="submit" class="rate-btn">Envoyer ma note</button>
                </form>
                <div class="rating-message"></div>
            </div>
            
            <a href="../../pages/catalogue.php" class="back-button">Retour au catalogue</a>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2025 AtlanStream - Tous droits réservés</p>
    </footer>
    
    <script src="../../assets/js/theme.js"></script>
    <script src="../../assets/js/rating.js"></script>
    <script src="../../assets/js/favorites.js"></script>
</body>
</html>
