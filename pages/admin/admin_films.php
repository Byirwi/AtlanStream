<?php
require_once '../../includes/session.php';
require_once '../../config/db_connect.php';
require_once '../../includes/admin-auth.php';
require_once '../../includes/film_functions.php';

// Vérification des droits admin
redirectIfNotAdmin();

// Supprimer un film
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        // Démarrer une transaction
        $pdo->beginTransaction();
        
        // Supprimer d'abord les relations avec les catégories
        $stmt = $pdo->prepare("DELETE FROM movie_categories WHERE movie_id = ?");
        $stmt->execute([$id]);
        
        // Ensuite supprimer le film
        $stmt = $pdo->prepare("DELETE FROM movies WHERE id = ?");
        $stmt->execute([$id]);
        
        // Valider la transaction
        $pdo->commit();
        
        // Supprimer la page HTML associée
        deleteFilmPage($id);
        
        $_SESSION['success'] = "Film supprimé avec succès.";
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        $_SESSION['error'] = "Erreur lors de la suppression du film: " . $e->getMessage();
    }
    header("Location: admin_films.php");
    exit;
}

// Récupérer tous les films avec leurs catégories
try {
    // Récupérer les films de base - ajout de ORDER BY pour garantir l'ordre
    $films = $pdo->query("SELECT movies.* FROM movies ORDER BY movies.id DESC")->fetchAll();
    
    // Pour chaque film, récupérer les catégories associées
    foreach ($films as &$film) {
        $stmtCategories = $pdo->prepare("
            SELECT c.name 
            FROM categories c
            JOIN movie_categories mc ON c.id = mc.category_id
            WHERE mc.movie_id = ?
            ORDER BY c.name
        ");
        $stmtCategories->execute([$film['id']]);
        $categories = $stmtCategories->fetchAll(PDO::FETCH_COLUMN);
        $film['categories'] = $categories;
    }
    // Important: libérer la référence
    unset($film);
    
} catch (Exception $e) {
    $films = [];
    $_SESSION['error'] = "Erreur lors de la récupération des films: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des films - AtlanStream Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="../../assets/css/mobile.css">
    <link rel="stylesheet" href="../../assets/css/animated-menu.css">
</head>
<body class="dark">
    <!-- Animation de chargement -->
    <div class="loading-screen">
        <div class="loading-indicator"></div>
    </div>

    <header>
        <div class="logo">
            <h1>AtlanStream <span style="color:#E53E3E">Admin</span></h1>
        </div>
        
        <!-- Navigation desktop animée -->
        <nav class="desktop-menu">
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
                <li style="--item-index: <?= $menuIndex++ ?>"><span class="welcome-user">Admin: <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                <li style="--item-index: <?= $menuIndex++ ?>"><a href="../Accueil.php">Voir le site</a></li>
                <li style="--item-index: <?= $menuIndex++ ?>"><a href="../logout.php" class="logout-btn">Déconnexion</a></li>
                <li style="--item-index: <?= $menuIndex++ ?>">
                    <button id="mobile-theme-toggle" class="theme-toggle" title="Changer de thème">
                        <span id="mobile-theme-icon">☀️</span>
                    </button>
                </li>
            </ul>
        </div>
    </nav>
    
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
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Poster</th>
                    <th>Titre</th>
                    <th>Catégories</th>
                    <th>Année</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($films as $film): ?>
                <tr>
                    <td><?= $film['id'] ?></td>
                    <td>
                        <?php 
                        $poster = !empty($film['poster_url']) && file_exists(__DIR__ . '/../../public/images/' . $film['poster_url']) 
                            ? '../../public/images/' . $film['poster_url'] 
                            : '../../public/images/default.jpg';
                        ?>
                        <img src="<?= $poster ?>" alt="<?= htmlspecialchars($film['title']) ?>" class="poster-thumb">
                    </td>
                    <td><?= htmlspecialchars($film['title']) ?></td>
                    <td>
                        <?php if (!empty($film['categories'])): ?>
                            <div class="category-tags">
                                <?php foreach ($film['categories'] as $category): ?>
                                    <span class="category-tag"><?= htmlspecialchars($category) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <span class="text-muted">Non catégorisé</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $film['year'] ?? 'N/A' ?></td>
                    <td class="actions">
                        <a href="admin_edit-film.php?id=<?= $film['id'] ?>" class="edit-btn">Modifier</a>
                        <a href="admin_films.php?delete=<?= $film['id'] ?>" class="delete-btn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce film?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($films)): ?>
                <tr>
                    <td colspan="6" style="text-align:center">Aucun film trouvé.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <footer>
        <p>&copy; 2025 AtlanStream - Administration</p>
    </footer>
    
    <script src="../../assets/js/theme.js"></script>
    <script src="../../assets/js/mobile-menu.js"></script>
    <script src="../../assets/js/animated-menu.js"></script>
</body>
</html>