<?php
require_once '../includes/session.php';
require_once '../config/db_connect.php';

$loginError = '';

// Si l'utilisateur est déjà connecté, rediriger vers le catalogue
if (isLoggedIn()) {
    header("Location: catalogue.php");
    exit;
}

// Traitement du formulaire de connexion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = trim($_POST['identifier'] ?? ''); // Peut être un nom d'utilisateur ou un email
    $password = $_POST['password'] ?? '';
    
    if (empty($identifier) || empty($password)) {
        $loginError = "Tous les champs sont obligatoires.";
    } else {
        // Rechercher l'utilisateur par nom d'utilisateur ou email avec une seule requête
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch();
        
        // Vérifier si l'utilisateur existe et si le mot de passe est correct
        if ($user && password_verify($password, $user['password'])) {
            // Enregistrer l'ID de l'utilisateur en session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Rediriger vers le catalogue
            header("Location: catalogue.php");
            exit;
        } else {
            $loginError = "Identifiant ou mot de passe incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AtlanStream - Connexion</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dark">
    <header>
        <div class="logo">
            <h1>AtlanStream</h1>
        </div>
        <nav>
            <ul>
                <li><a href="Accueil.php">Accueil</a></li>
                <li><a href="register.php">Inscription</a></li>
                <li>
                    <button id="theme-toggle" class="theme-toggle" title="Changer de thème">
                        <span id="theme-icon">☀️</span>
                    </button>
                </li>
            </ul>
        </nav>
    </header>
    
    <main class="login-container">
        <div class="login-form">
            <h2>Connexion</h2>
            <?php if (!empty($loginError)): ?>
                <div class="alert alert-error">
                    <?php echo $loginError; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']); 
                    ?>
                </div>
            <?php endif; ?>
            
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="identifier">Nom d'utilisateur ou Email</label>
                    <input type="text" id="identifier" name="identifier" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Se connecter</button>
            </form>
            <div class="form-footer">
                <p>Vous n'avez pas de compte ? <a href="register.php">Inscrivez-vous</a></p>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2023 AtlanStream - Tous droits réservés</p>
    </footer>
    
    <script src="../assets/js/theme.js"></script>
</body>
</html>
