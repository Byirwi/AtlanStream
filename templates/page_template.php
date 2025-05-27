<?php
require_once '../includes/session.php';
require_once '../includes/admin-auth.php';

// Rediriger si non connecté
redirectIfNotLoggedIn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Titre de la page - AtlanStream</title>
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
                <li><a href="Accueil.php">Accueil</a></li>
                <li><a href="catalogue.php">Catalogue</a></li>
                <li><a href="favoris.php">Mes Favoris</a></li>
                <li><span class="welcome-user">Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
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
    
    <!-- Navigation mobile -->
    <nav class="mobile-nav">
        <div class="mobile-nav-container">
            <ul>
                <?php $menuIndex = 0; ?>
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
                <li style="--item-index: <?= $menuIndex++ ?>">
                    <button id="mobile-theme-toggle" class="theme-toggle" title="Changer de thème">
                        <span id="mobile-theme-icon">☀️</span>
                    </button>
                </li>
            </ul>
        </div>
    </nav>
    
    <main>
        <!-- Contenu principal de la page -->
        <div class="container">
            <h2>Titre de la page</h2>
            <p>Contenu de la page...</p>
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
