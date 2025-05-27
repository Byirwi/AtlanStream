<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($film['title']) ?> - AtlanStream</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/rating.css">
    <style>
        .film-detail {
            max-width: 1000px;
            margin: 40px auto;
            padding: 30px;
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--shadow-strong);
        }
        
        .film-header {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .film-poster {
            flex: 0 0 300px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow-medium);
        }
        
        .film-poster img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .film-info {
            flex: 1;
        }
        
        .film-title {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
        
        .film-meta {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .film-meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--gray-text);
        }
        
        .film-description {
            font-size: 1.1rem;
            line-height: 1.7;
            margin-bottom: 30px;
        }
        
        .film-categories {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }
        
        .film-category {
            background: var(--accent-color-soft);
            color: var(--primary-color);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .back-button {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 25px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .back-button:hover {
            background-color: var(--primary-color-hover);
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .film-header {
                flex-direction: column;
            }
            .film-poster {
                flex: 0 0 auto;
                max-width: 250px;
                margin: 0 auto;
            }
        }
    </style>
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
                    
                    <div class="film-meta">
                        <?php if (!empty($film['year'])): ?>
                        <div class="film-meta-item">
                            <span>📅</span> <?= htmlspecialchars($film['year']) ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($film['duration'])): ?>
                        <div class="film-meta-item">
                            <span>⏱️</span> <?= htmlspecialchars($film['duration']) ?> min
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($film['director'])): ?>
                        <div class="film-meta-item">
                            <span>🎬</span> Réalisé par <?= htmlspecialchars($film['director']) ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Affichage de la note moyenne -->
                        <?php $rating = getMovieRating($film['id'], $pdo); ?>
                        <?php if ($rating['count'] > 0): ?>
                        <div class="film-meta-item film-rating">
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star <?= ($i <= round($rating['average'])) ? 'filled' : '' ?>">★</span>
                                <?php endfor; ?>
                            </div>
                            <span><?= $rating['average'] ?>/5 (<?= $rating['count'] ?> votes)</span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($film['description'])): ?>
                    <div class="film-description">
                        <?= nl2br(htmlspecialchars($film['description'])) ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($film['actors'])): ?>
                    <div class="film-meta-item">
                        <strong>Avec:</strong> <?= htmlspecialchars($film['actors']) ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($categories)): ?>
                    <div class="film-categories">
                        <?php foreach ($categories as $category): ?>
                        <span class="film-category"><?= htmlspecialchars($category) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Système de notation pour l'utilisateur connecté -->
            <div class="user-rating">
                <h3>Votre note</h3>
                <form action="../../assets/ajax/rate_movie.php" method="post" class="rating-form" data-movie-id="<?= $film['id'] ?>">
                    <div class="rating-stars">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" />
                            <label for="star<?= $i ?>" title="<?= $i ?> étoiles">★</label>
                        <?php endfor; ?>
                    </div>
                    <button type="submit" class="rate-btn">Noter</button>
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
