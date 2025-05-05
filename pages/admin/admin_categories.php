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

// Gestion des catégories
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'] ?? '';
    if (!empty($category_name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$category_name]);
    }
}

if (isset($_GET['delete'])) {
    $category_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des catégories - AtlanStream Admin</title>
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
            <h2>Gestion des catégories</h2>
        </div>
        
        <div class="admin-menu">
            <a href="admin_dashboard.php">Tableau de bord</a>
            <a href="admin_films.php">Gestion des films</a>
            <a href="admin_categories.php" class="active">Gestion des catégories</a>
            <a href="admin_users.php">Gestion des utilisateurs</a>
        </div>
        
        <div class="admin-actions">
            <h3>Ajouter une nouvelle catégorie</h3>
            <form action="admin_categories.php" method="POST">
                <input type="text" name="category_name" placeholder="Nom de la catégorie" required>
                <button type="submit" name="add_category" class="btn btn-primary">Ajouter</button>
            </form>
        </div>
        
        <div class="admin-list">
            <h3>Liste des catégories</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= htmlspecialchars($category['id']) ?></td>
                        <td><?= htmlspecialchars($category['name']) ?></td>
                        <td class="actions">
                            <a href="admin_categories.php?edit=<?= $category['id'] ?>" class="edit-btn">Modifier</a>
                            <a href="admin_categories.php?delete=<?= $category['id'] ?>" class="delete-btn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2025 AtlanStream - Administration</p>
    </footer>
    
    <script src="../../assets/js/theme.js"></script>
</body>
</html>