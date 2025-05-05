<?php
// Activer l'affichage des erreurs pour déboguer
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclure les fichiers nécessaires - chemins corrigés car nous sommes dans pages/admin
require_once '../../includes/session.php';
require_once '../../config/db_connect.php';
require_once '../../includes/admin-auth.php';

// Tester si la session admin est correctement configurée
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo "<div style='color:red;padding:20px;'>";
    echo "Erreur: Vous n'êtes pas connecté en tant qu'administrateur.<br>";
    echo "SESSION: <pre>" . print_r($_SESSION, true) . "</pre>";
    echo "</div>";
    exit;
}

// Vérification des droits admin
redirectIfNotAdmin();

// Statistiques basiques
try {
    $stats = [
        'films' => $pdo->query("SELECT COUNT(*) FROM movies")->fetchColumn(),
        'categories' => $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
        'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn()
    ];
} catch (PDOException $e) {
    echo "<div style='color:red;padding:20px;'>";
    echo "Erreur de base de données: " . $e->getMessage() . "<br>";
    echo "SESSION: <pre>" . print_r($_SESSION, true) . "</pre>";
    echo "</div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - AtlanStream Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body class="dark">
    <header>
        <div class="logo">
            <h1>AtlanStream <span style="color:#E53E3E">Admin</span></h1>
        </div>
        <nav>
            <ul>
                <li><span class="welcome-user">Admin: <?php echo htmlspecialchars($_SESSION['username'] ?? 'Inconnu'); ?></span></li>
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
            <h2>Tableau de bord administrateur</h2>
        </div>
        
        <div class="admin-menu">
            <a href="admin_dashboard.php" class="active">Tableau de bord</a>
            <a href="admin_films.php">Gestion des films</a>
            <a href="admin_categories.php">Gestion des catégories</a>
            <a href="admin_users.php">Gestion des utilisateurs</a>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['films']; ?></div>
                <div class="stat-label">Films</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['categories']; ?></div>
                <div class="stat-label">Catégories</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['users']; ?></div>
                <div class="stat-label">Utilisateurs</div>
            </div>
        </div>
        
        <div class="admin-actions">
            <h3>Actions rapides</h3>
            <p><a href="admin_edit-film.php" class="btn btn-primary">Ajouter un nouveau film</a></p>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2025 AtlanStream - Administration</p>
    </footer>
    
    <script src="../../assets/js/theme.js"></script>
</body>
</html>