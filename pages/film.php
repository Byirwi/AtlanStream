<?php
require_once '../includes/session.php';
require_once '../config/db_connect.php';

redirectIfNotLoggedIn();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT movies.*, categories.name as category_name FROM movies 
                       LEFT JOIN categories ON movies.category_id = categories.id 
                       WHERE movies.id = ?");
$stmt->execute([$id]);
$film = $stmt->fetch();

if (!$film) {
    header('Location: catalogue.php');
    exit;
}

$poster = !empty($film['poster_url']) && file_exists(__DIR__ . '/../public/images/' . $film['poster_url'])
    ? '../public/images/' . htmlspecialchars($film['poster_url'])
    : '../public/images/default.jpg';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($film['title']) ?> - AtlanStream</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .film-container {
            max-width: 900px;
            margin: 40px auto;
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: var(--shadow-strong);
            display: flex;
            flex-wrap: wrap;
            overflow: hidden;
        }
        .film-poster {
            flex: 1 1 300px;
            min-width: 300px;
            background: var(--card-hover-bg);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .film-poster img {
            width: 100%;
            max-width: 320px;
            border-radius: 0;
            box-shadow: var(--shadow-medium);
        }
        .film-info {
            flex: 2 1 400px;
            padding: 40px 32px;
            color: var(--light-text);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .film-info h1 {
            font-size: 2.2rem;
            color: var(--primary-color);
            margin-bottom: 18px;
        }
        .film-info .movie-category {
            margin-bottom: 18px;
            display: inline-block;
            background: var(--accent-color-soft);
            color: var(--primary-color);
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 600;
        }
        .film-info p {
            font-size: 1.1rem;
            color: var(--gray-text);
            margin-bottom: 24px;
        }
        .film-info .back-link {
            margin-top: 24px;
            display: inline-block;
            color: var(--primary-color);
            text-decoration: underline;
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
                <li><a href="Accueil.php">Accueil</a></li>
                <li><a href="catalogue.php">Catalogue</a></li>
                <li><span class="welcome-user">Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?></span></li>
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
        <div class="film-container">
            <div class="film-poster">
                <img src="<?= $poster ?>" alt="<?= htmlspecialchars($film['title']) ?>">
            </div>
            <div class="film-info">
                <h1><?= htmlspecialchars($film['title']) ?></h1>
                <span class="movie-category"><?= htmlspecialchars($film['category_name'] ?? 'Non catégorisé') ?></span>
                <p><?= htmlspecialchars($film['description']) ?></p>
                <a href="catalogue.php" class="back-link">&larr; Retour au catalogue</a>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; 2025 AtlanStream - Tous droits réservés</p>
    </footer>
    <script src="../assets/js/theme.js"></script>
</body>
</html>
