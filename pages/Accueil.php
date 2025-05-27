<?php
require_once '../includes/session.php';
require_once '../includes/admin-auth.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AtlanStream - Accueil</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/mobile.css">
    <link rel="stylesheet" href="../assets/css/animated-menu.css">
</head>
<body class="dark">
    <!-- Animation de chargement -->
    <div class="loading-screen">
        <div class="loading-indicator"></div>
    </div>

    <header>
        <div class="logo">
            <h1>AtlanStream</h1>
        </div>
        
        <!-- Navigation desktop animée -->
        <nav class="desktop-menu">
            <ul>
                <?php if (isLoggedIn()): ?>
                    <li><span class="welcome-user">Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                    <li><a href="catalogue.php">Catalogue</a></li>
                    <li><a href="favoris.php">Mes Favoris</a></li>
                    <li><a href="compte.php">Mon compte</a></li>
                    <?php if (isAdmin()): ?>
                        <li>
                            <a href="#" class="admin-dropdown-toggle">Admin <span>▼</span></a>
                            <ul class="admin-dropdown">
                                <li><a href="admin/admin_dashboard.php">Tableau de bord</a></li>
                                <li><a href="admin/admin_films.php">Gérer les films</a></li>
                                <li><a href="admin/admin_categories.php">Gérer les catégories</a></li>
                                <li><a href="admin/admin_users.php">Gérer les utilisateurs</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
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
        
        <!-- Hamburger pour menu mobile - nouvelle version -->
        <button class="mobile-menu-toggle" aria-label="Menu">
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </button>
    </header>
    
    <!-- Navigation mobile - nouvelle version -->
    <nav class="mobile-nav">
        <div class="mobile-nav-container">
            <ul>
                <?php $menuIndex = 0; ?>
                <?php if (isLoggedIn()): ?>
                    <li style="--item-index: <?= $menuIndex++ ?>"><span class="welcome-user">Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                    <li style="--item-index: <?= $menuIndex++ ?>"><a href="catalogue.php">Catalogue</a></li>
                    <li style="--item-index: <?= $menuIndex++ ?>"><a href="favoris.php">Mes Favoris</a></li>
                    <li style="--item-index: <?= $menuIndex++ ?>"><a href="compte.php">Mon compte</a></li>
                    <?php if (isAdmin()): ?>
                        <li style="--item-index: <?= $menuIndex++ ?>"><a href="#" class="admin-dropdown-toggle">Admin ▾</a>
                            <ul class="admin-dropdown">
                                <li><a href="admin/admin_dashboard.php">Tableau de bord</a></li>
                                <li><a href="admin/admin_films.php">Gérer les films</a></li>
                                <li><a href="admin/admin_categories.php">Gérer les catégories</a></li>
                                <li><a href="admin/admin_users.php">Gérer les utilisateurs</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <li style="--item-index: <?= $menuIndex++ ?>"><a href="logout.php" class="logout-btn">Déconnexion</a></li>
                <?php else: ?>
                    <li style="--item-index: <?= $menuIndex++ ?>"><a href="login.php">Connexion</a></li>
                    <li style="--item-index: <?= $menuIndex++ ?>"><a href="register.php">Inscription</a></li>
                <?php endif; ?>
                <li style="--item-index: <?= $menuIndex++ ?>">
                    <button id="mobile-theme-toggle" class="theme-toggle" title="Changer de thème">
                        <span id="mobile-theme-icon">☀️</span>
                    </button>
                </li>
            </ul>
        </div>
    </nav>
    
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
        <p>&copy; 2025 AtlanStream - Tous droits réservés</p>
    </footer>
    
    <script src="../assets/js/theme.js"></script>
    <script src="../assets/js/mobile-menu.js"></script>
    <script src="../assets/js/animated-menu.js"></script>
    
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
