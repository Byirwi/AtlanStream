<?php
require_once '../../includes/session.php';
require_once '../../config/db_connect.php';
require_once '../../includes/admin-auth.php';

// Vérification des droits admin
redirectIfNotAdmin();

// Ajouter un nouvel utilisateur
if (isset($_POST['add_user'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    
    $errors = [];
    
    // Validation
    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est obligatoire.";
    }
    
    if (empty($password)) {
        $errors[] = "Le mot de passe est obligatoire.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit comporter au moins 6 caractères.";
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide.";
    }
    
    // Vérifier si le nom d'utilisateur existe déjà
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Ce nom d'utilisateur est déjà utilisé.";
        }
    }
    
    // Vérifier si l'email existe déjà (si fourni)
    if (empty($errors) && !empty($email)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Cet email est déjà utilisé.";
        }
    }
    
    // Enregistrer l'utilisateur
    if (empty($errors)) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email ?: null, $hashedPassword, $is_admin]);
            
            $_SESSION['success'] = "Utilisateur créé avec succès.";
            header("Location: admin_users.php");
            exit;
        } catch (Exception $e) {
            $errors[] = "Erreur lors de la création de l'utilisateur: " . $e->getMessage();
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
    }
}

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
    header("Location: admin_users.php");
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
    header("Location: admin_users.php");
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
    <style>
        .user-form {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .user-form h3 {
            margin-top: 0;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .checkbox-group input[type="checkbox"] {
            margin-right: 10px;
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
            <a href="admin_dashboard.php">Tableau de bord</a>
            <a href="admin_films.php">Gestion des films</a>
            <a href="admin_categories.php">Gestion des catégories</a>
            <a href="admin_users.php" class="active">Gestion des utilisateurs</a>
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
        
        <!-- Formulaire d'ajout d'utilisateur -->
        <div class="user-form">
            <h3>Ajouter un nouvel utilisateur</h3>
            <form action="admin_users.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur*</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email (facultatif)</label>
                        <input type="email" id="email" name="email">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Mot de passe*</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="password_confirm">Confirmer le mot de passe*</label>
                        <input type="password" id="password_confirm" name="password_confirm" required>
                    </div>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="is_admin" name="is_admin">
                    <label for="is_admin">Administrateur</label>
                </div>
                <button type="submit" name="add_user" class="btn btn-primary">Créer l'utilisateur</button>
            </form>
        </div>
        
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
                            <a href="admin_users.php?toggle_admin=<?= $user['id'] ?>" class="edit-btn" title="Changer les droits admin" onclick="return confirm('Êtes-vous sûr de vouloir modifier les droits administrateur?')">
                                <?= $user['is_admin'] ? '⬇️ Rétrograder' : '⬆️ Promouvoir' ?>
                            </a>
                            <a href="admin_users.php?delete=<?= $user['id'] ?>" class="delete-btn" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?')">
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
    <script>
        // Validation du formulaire côté client
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            
            if (password !== passwordConfirm) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
            }
        });
    </script>
</body>
</html>
