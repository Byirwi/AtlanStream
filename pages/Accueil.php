<?php
require_once '../includes/session.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AtlanStream - Accueil</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dark">
    <header>
        <div class="logo">
            <h1>AtlanStream</h1>
        </div>
        <nav>
            <ul>
                <?php if (isLoggedIn()): ?>
                    <li><span class="welcome-user">Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                    <li><a href="catalogue.php">Catalogue</a></li>
                    <li><a href="logout.php" class="logout-btn">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="login.php">Connexion</a></li>
                    <li><a href="register.php">Inscription</a></li>
                <?php endif; ?>
                <li>
                    <button id="theme-toggle" class="theme-toggle" title="Changer de thème">
                        <span id="theme-icon">☀️</span>
                    </button>
                </li>
            </ul>
        </nav>
    </header>
    
    <main class="hero">
        <div class="hero-content">
            <h2>Bienvenue sur AtlanStream</h2>
            <p>Découvrez les secrets des océans et l'univers fascinant de l'Atlantide</p>
            <div class="cta-buttons">
                <?php if (isLoggedIn()): ?>
                    <a href="catalogue.php" class="btn btn-primary">Explorer le catalogue</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">Se connecter</a>
                    <a href="register.php" class="btn btn-secondary">S'inscrire</a>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2023 AtlanStream - Tous droits réservés</p>
    </footer>
    
</body>
</html>
