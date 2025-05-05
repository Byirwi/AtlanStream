<?php
require_once '../../includes/session.php';
require_once '../../config/db_connect.php';
require_once '../../includes/admin-auth.php';

// Vérification des droits admin
redirectIfNotAdmin();

// Ajouter un nouvel utilisateur
if (isset($_POST['add_user'])) {
    // ...existing code...
    header("Location: admin_users.php");
    exit;
}

// Supprimer un utilisateur
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    // ...existing code...
    header("Location: admin_users.php");
    exit;
}

// Changer les droits administrateur
if (isset($_GET['toggle_admin']) && is_numeric($_GET['toggle_admin'])) {
    // ...existing code...
    header("Location: admin_users.php");
    exit;
}

// ...existing code...
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs - AtlanStream Admin</title>
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
        <div class="admin-menu">
            <a href="admin_dashboard.php">Tableau de bord</a>
            <a href="admin_films.php">Gestion des films</a>
            <a href="admin_categories.php">Gestion des catégories</a>
            <a href="admin_users.php" class="active">Gestion des utilisateurs</a>
        </div>
        
        <!-- Formulaire d'ajout d'utilisateur -->
        <div class="user-form">
            <h3>Ajouter un nouvel utilisateur</h3>
            <form action="admin_users.php" method="POST">
                <!-- ...existing code... -->
            </form>
        </div>
        
        <table>
            <!-- ...existing code... -->
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <!-- ...existing code... -->
                    <td class="actions">
                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <a href="admin_users.php?toggle_admin=<?= $user['id'] ?>" class="edit-btn" title="Changer les droits admin">
                                <?= $user['is_admin'] ? '⬇️ Rétrograder' : '⬆️ Promouvoir' ?>
                            </a>
                            <a href="admin_users.php?delete=<?= $user['id'] ?>" class="delete-btn" title="Supprimer">🗑️ Supprimer</a>
                        <?php else: ?>
                            <span style="color:var(--gray-text)">Session actuelle</span>
                        <?php endif; ?>
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