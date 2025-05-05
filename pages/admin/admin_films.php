<?php
require_once '../../includes/session.php';
require_once '../../config/db_connect.php';
require_once '../../includes/admin-auth.php';

// Vérification des droits admin
redirectIfNotAdmin();

// Supprimer un film
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    // ...existing code...
    header("Location: admin_films.php");
    exit;
}

// ...existing code...

// Mise à jour des chemins pour l'upload d'images
$upload_dir = "../../public/images/";

// ...existing code...
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des films - AtlanStream Admin</title>
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
        <div class="admin-header">
            <h2>Gestion des films</h2>
            <a href="admin_edit-film.php" class="btn btn-primary">Ajouter un film</a>
        </div>
        
        <div class="admin-menu">
            <a href="admin_dashboard.php">Tableau de bord</a>
            <a href="admin_films.php" class="active">Gestion des films</a>
            <a href="admin_categories.php">Gestion des catégories</a>
            <a href="admin_users.php">Gestion des utilisateurs</a>
        </div>
        
        <!-- ...existing code... -->
        
        <table>
            <!-- ...existing code... -->
            <tbody>
                <?php foreach ($films as $film): ?>
                <tr>
                    <!-- ...existing code... -->
                    <td class="actions">
                        <a href="admin_edit-film.php?id=<?= $film['id'] ?>" class="edit-btn">Modifier</a>
                        <a href="admin_films.php?delete=<?= $film['id'] ?>" class="delete-btn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce film?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <!-- ...existing code... -->
            </tbody>
        </table>
    </div>
    
    <footer>
        <p>&copy; 2025 AtlanStream - Administration</p>
    </footer>
    
    <script src="../../assets/js/theme.js"></script>
</body>
</html>