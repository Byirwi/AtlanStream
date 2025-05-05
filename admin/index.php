<?php
require_once '../includes/session.php';
require_once '../config/db_connect.php';
require_once '../includes/admin-auth.php';

$loginError = '';

// Si déjà connecté en tant qu'admin, rediriger vers le tableau de bord
if (isAdmin()) {
    header("Location: dashboard.php");
    exit;
}

// Traitement du formulaire de connexion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $loginError = "Tous les champs sont obligatoires.";
    } else {
        // Vérifier si l'utilisateur existe et est un admin
        $stmt = $pdo->prepare("SELECT id, username, password, is_admin FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password']) && $user['is_admin'] == 1) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = true;
            
            header("Location: dashboard.php");
            exit;
        } else {
            $loginError = "Identifiants incorrects ou vous n'avez pas les droits d'administration.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - AtlanStream</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-header {
            background-color: #2D3748;
            color: #E53E3E;
            padding: 10px 20px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="dark">
    <header>
        <div class="logo">
            <h1>AtlanStream <span style="color:#E53E3E">Admin</span></h1>
        </div>
    </header>
    
    <main class="login-container">
        <div class="login-form">
            <div class="admin-header">
                <h2>Connexion Administrateur</h2>
            </div>
            
            <?php if (!empty($loginError)): ?>
                <div class="alert alert-error">
                    <?php echo $loginError; ?>
                </div>
            <?php endif; ?>
            
            <form action="index.php" method="POST">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Connexion</button>
            </form>
            
            <div class="form-footer">
                <p><a href="../pages/Accueil.php">Retour au site</a></p>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2025 AtlanStream - Administration</p>
    </footer>
</body>
</html>
