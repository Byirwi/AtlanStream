<?php
require_once '../includes/session.php';
require_once '../config/db_connect.php';
require_once '../includes/admin-auth.php';

// Vérification des droits admin
redirectIfNotAdmin();

// Statistiques basiques
$stats = [
    'films' => $pdo->query("SELECT COUNT(*) FROM movies")->fetchColumn(),
    'categories' => $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn()
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - AtlanStream Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }
        .admin-header {
            background-color: #2D3748;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 8px;
            border-left: 4px solid #E53E3E;
        }
        .admin-menu {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }
        .admin-menu a {
            background: var(--card-bg);
            padding: 12px 20px;
            border-radius: 6px;
            color: var(--light-text);
            text-decoration: none;
            transition: all 0.3s;
        }
        .admin-menu a:hover {
            background: var(--primary-color);
            color: white;
        }
        .admin-menu a.active {
            background: var(--primary-color);
            color: white;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: var(--shadow-medium);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin: 10px 0;
        }
        .stat-label {
            color: var(--gray-text);
            font-size: 1.1rem;
        }
    </style>
</head>
<body class="dark">
    <header>
        <div class="logo">
            <h1>AtlanStream <span style="color:#E53E3E">Admin</span></h1>
        </div>
        <nav>
            <ul>
                <li><span class="welcome-user">Admin: <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                <li><a href="../pages/Accueil.php">Voir le site</a></li>
                <li><a href="../pages/logout.php" class="logout-btn">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="admin-container">
        <div class="admin-header">
            <h2>Tableau de bord administrateur</h2>
        </div>
        
        <div class="admin-menu">
            <a href="dashboard.php" class="active">Tableau de bord</a>
            <a href="films.php">Gestion des films</a>
            <a href="categories.php">Gestion des catégories</a>
            <a href="users.php">Gestion des utilisateurs</a>
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
            <p><a href="edit-film.php" class="btn btn-primary">Ajouter un nouveau film</a></p>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2025 AtlanStream - Administration</p>
    </footer>
</body>
</html>
