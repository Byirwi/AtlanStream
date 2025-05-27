<?php
require_once '../includes/session.php';
require_once '../config/db_connect.php';
require_once '../includes/admin-auth.php';

// Rediriger si non connecté
redirectIfNotLoggedIn();

$userId = $_SESSION['user_id'];
$message = '';
$error = '';

// Récupérer les infos actuelles de l'utilisateur
try {
    $stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // Utilisateur non trouvé dans la base de données
        $_SESSION = array();
        session_destroy();
        header("Location: login.php");
        exit;
    }
} catch (Exception $e) {
    $error = "Erreur lors de la récupération des informations utilisateur.";
    error_log("Erreur user info: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Changement du nom d'utilisateur et/ou email
    if (isset($_POST['update_profile'])) {
        $newUsername = trim($_POST['username']);
        $newEmail = trim($_POST['email']);

        if (empty($newUsername)) {
            $error = "Le nom d'utilisateur ne peut pas être vide.";
        } elseif (!empty($newEmail) && !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $error = "L'email n'est pas valide.";
        } else {
            try {
                // Vérifier unicité du nom d'utilisateur
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id != ?");
                $stmt->execute([$newUsername, $userId]);
                if ($stmt->fetchColumn() > 0) {
                    $error = "Ce nom d'utilisateur est déjà utilisé.";
                } else {
                    // Vérifier unicité de l'email si fourni
                    if (!empty($newEmail)) {
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
                        $stmt->execute([$newEmail, $userId]);
                        if ($stmt->fetchColumn() > 0) {
                            $error = "Cet email est déjà utilisé.";
                        }
                    }
                    if (empty($error)) {
                        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                        $stmt->execute([$newUsername, $newEmail ?: null, $userId]);
                        $_SESSION['username'] = $newUsername;
                        $message = "Profil mis à jour avec succès.";
                        $user['username'] = $newUsername;
                        $user['email'] = $newEmail;
                    }
                }
            } catch (Exception $e) {
                $error = "Erreur lors de la mise à jour du profil.";
                error_log("Erreur update profile: " . $e->getMessage());
            }
        }
    }

    // Changement de mot de passe
    if (isset($_POST['update_password'])) {
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        // Vérification du nouveau mot de passe
        try {
            if (strlen($newPassword) < 6) {
                $error = "Le nouveau mot de passe doit comporter au moins 6 caractères.";
            } elseif ($newPassword !== $confirmPassword) {
                $error = "Les nouveaux mots de passe ne correspondent pas.";
            } else {
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $userId]);
                $message = "Mot de passe mis à jour avec succès.";
            }
        } catch (Exception $e) {
            $error = "Erreur lors de la mise à jour du mot de passe.";
            error_log("Erreur update password: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon compte - AtlanStream</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/mobile.css">
    <link rel="stylesheet" href="../assets/css/animated-menu.css">
</head>
<body class="dark">
    <!-- Animation de chargement -->
    <div class="loading-screen">
        <div class="loading-indicator"></div>
    </div>

    <header>
        <div class="logo">
            <h1>AtlanStream</h1>
        </div>
        
        <!-- Navigation desktop animée -->
        <nav class="desktop-menu">
            <ul>
                <li><a href="Accueil.php">Accueil</a></li>
                <li><a href="catalogue.php">Catalogue</a></li>
                <li><a href="favoris.php">Mes Favoris</a></li>
                <li><span class="welcome-user">Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                <?php if (isAdmin()): ?>
                    <li>
                        <a href="#" class="admin-dropdown-toggle">Admin <span>▼</span></a>
                        <ul class="admin-dropdown">
                            <li><a href="admin/admin_dashboard.php">Tableau de bord</a></li>
                            <li><a href="admin/admin_films.php">Gérer les films</a></li>
                            <li><a href="admin/admin_categories.php">Gérer les catégories</a></li>
                            <li><a href="admin/admin_users.php">Gérer les utilisateurs</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <li><a href="logout.php" class="logout-btn">Déconnexion</a></li>
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
        <ul>
            <li><a href="Accueil.php">Accueil</a></li>
            <li><a href="catalogue.php">Catalogue</a></li>
            <li><a href="favoris.php">Mes Favoris</a></li>
            <li><span class="welcome-user">Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
            <?php if (isAdmin()): ?>
                <li><a href="#" class="admin-dropdown-toggle">Admin <span>▼</span></a>
                    <ul class="admin-dropdown">
                        <li><a href="admin/admin_dashboard.php">Tableau de bord</a></li>
                        <li><a href="admin/admin_films.php">Gérer les films</a></li>
                        <li><a href="admin/admin_categories.php">Gérer les catégories</a></li>
                        <li><a href="admin/admin_users.php">Gérer les utilisateurs</a></li>
                    </ul>
                </li>
            <?php endif; ?>
            <li><a href="logout.php" class="logout-btn">Déconnexion</a></li>
            <li>
                <button id="mobile-theme-toggle" class="theme-toggle" title="Changer de thème">
                    <span id="mobile-theme-icon">☀️</span>
                </button>
            </li>
        </ul>
    </nav>
    
    <main class="login-container">
        <div class="login-form register-form">
            <h2>Mon compte</h2>
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post" style="margin-bottom:32px;">
                <h3>Modifier le profil</h3>
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($user['username']); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email (facultatif)</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                </div>
                <button type="submit" name="update_profile" class="btn btn-primary">Mettre à jour</button>
            </form>

            <form method="post">
                <h3>Changer le mot de passe</h3>
                <div class="form-group">
                    <label for="new_password">Nouveau mot de passe</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" name="update_password" class="btn btn-primary">Changer le mot de passe</button>
            </form>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2025 AtlanStream - Tous droits réservés</p>
    </footer>
    
    <script src="../assets/js/theme.js"></script>
    <script src="../assets/js/mobile-menu.js"></script>
    <script src="../assets/js/animated-menu.js"></script>
    
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
