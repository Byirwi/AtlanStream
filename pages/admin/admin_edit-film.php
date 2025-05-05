<?php
require_once '../../includes/session.php';
require_once '../../config/db_connect.php';
require_once '../../includes/admin-auth.php';

// Vérification des droits admin
redirectIfNotAdmin();

// ...existing code...

// Mise à jour des chemins pour l'upload d'images
$upload_dir = "../../public/images/";

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ...existing code...
    
    if (empty($error)) {
        $_SESSION['success'] = $success;
        header("Location: admin_films.php");
        exit;
    }
}

// ...existing code...
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= !empty($film['id']) ? 'Modifier' : 'Ajouter' ?> un film - AtlanStream Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body class="dark">
    <header>
        <!-- ...existing code... -->
        <nav>
            <ul>
                <li><span class="welcome-user">Admin: <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                <li><a href="../Accueil.php">Voir le site</a></li>
                <li><a href="../logout.php" class="logout-btn">Déconnexion</a></li>
                <li>
                    <button id="theme-toggle" class="theme-toggle" title="Changer de thème">
                        <span id="theme-icon">☀️</span>
                    </button>
                </li>
            </ul>
        </nav>
    </header>
    
    <div class="admin-container">
        <!-- ...existing code... -->
        
        <div class="admin-menu">
            <a href="admin_dashboard.php">Tableau de bord</a>
            <a href="admin_films.php" class="active">Gestion des films</a>
            <a href="admin_categories.php">Gestion des catégories</a>
            <a href="admin_users.php">Gestion des utilisateurs</a>
        </div>
        
        <!-- ...existing code... -->
        
        <div class="form-container">
            <form method="post" enctype="multipart/form-data">
                <!-- ...existing code... -->
                
                <div class="form-buttons" style="margin-top: 30px;">
                    <button type="submit" class="btn btn-primary"><?= !empty($film['id']) ? 'Mettre à jour' : 'Ajouter' ?> le film</button>
                    <a href="admin_films.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2025 AtlanStream - Administration</p>
    </footer>
    
    <script src="../../assets/js/theme.js"></script>
</body>
</html>