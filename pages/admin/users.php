<?php
require_once '../../includes/session.php';
require_once '../../config/db_connect.php';
require_once '../../includes/admin-auth.php';

// Vérification des droits admin
redirectIfNotAdmin();

// Supprimer un utilisateur
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Protection: ne pas supprimer son propre compte
    if ($id == $_SESSION['user_id']) {
        $_SESSION['error'] = "Vous ne pouvez pas supprimer votre propre compte.";
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success'] = "Utilisateur supprimé avec succès.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur lors de la suppression de l'utilisateur.";
        }
    }
    header("Location: users.php");
    exit;
}

// Changer les droits administrateur
if (isset($_GET['toggle_admin']) && is_numeric($_GET['toggle_admin'])) {
    $id = $_GET['toggle_admin'];
    
    // Protection: ne pas modifier ses propres droits
    if ($id == $_SESSION['user_id']) {
        $_SESSION['error'] = "Vous ne pouvez pas modifier vos propres droits.";
    } else {
        try {
            // Récupérer l'état actuel
            $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();
            
            // Inverser l'état
            $new_state = $user['is_admin'] ? 0 : 1;
            
            $stmt = $pdo->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
            $stmt->execute([$new_state, $id]);
            $_SESSION['success'] = "Droits administrateur modifiés avec succès.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur lors de la modification des droits.";
        }
    }
    header("Location: users.php");
    exit;
}

// Récupérer tous les utilisateurs
$users = $pdo->query("SELECT * FROM users ORDER BY id")->fetchAll();
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
        <div class="logo">
            <h1>AtlanStream <span style="color:#E53E3E">Admin</span></h1>
        </div>
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
            <h2>Gestion des utilisateurs</h2>
        </div>
        
        <div class="admin-menu">
            <a href="dashboard.php">Tableau de bord</a>
            <a href="films.php">Gestion des films</a>
            <a href="categories.php">Gestion des catégories</a>
            <a href="users.php" class="active">Gestion des utilisateurs</a>
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
                    <th>Nom d'utilisateur</th>
                    <th>Email</th>
                    <th>Admin</th>
                    <th>Date de création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email'] ?? 'Non renseigné') ?></td>
                    <td>
                        <?php if ($user['is_admin']): ?>
                            <span style="color:var(--primary-color)">✓ Admin</span>
                        <?php else: ?>
                            <span style="color:var(--gray-text)">Utilisateur</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $user['created_at'] ?? 'N/A' ?></td>
                    <td class="actions">
                        <?php if ($user['id'] != $_SESSION['user_id']): // Ne pas afficher d'actions pour l'utilisateur actuel ?>
                            <a href="users.php?toggle_admin=<?= $user['id'] ?>" class="edit-btn" title="Changer les droits admin" onclick="return confirm('Êtes-vous sûr de vouloir modifier les droits administrateur?')">
                                <?= $user['is_admin'] ? '⬇️ Rétrograder' : '⬆️ Promouvoir' ?>
                            </a>
                            <a href="users.php?delete=<?= $user['id'] ?>" class="delete-btn" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?')">
                                🗑️ Supprimer
                            </a>
                        <?php else: ?>
                            <span style="color:var(--gray-text)">Session actuelle</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="6" style="text-align:center">Aucun utilisateur trouvé.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <footer>
        <p>&copy; 2025 AtlanStream - Administration</p>
    </footer>
    
    <script src="../../assets/js/theme.js"></script>
</body>
</html>
