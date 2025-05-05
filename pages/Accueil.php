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
