<?php
require_once '../includes/session.php';
require_once '../config/db_connect.php';

$registerError = '';

// Si l'utilisateur est déjà connecté, rediriger vers le catalogue
if (isLoggedIn()) {
    header("Location: catalogue.php");
    exit;
}

// Traitement du formulaire d'inscription
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $email = trim($_POST['email'] ?? '');
    
    // Validation des champs
    if (empty($username) || empty($password) || empty($confirmPassword)) {
        $registerError = "Le nom d'utilisateur et le mot de passe sont obligatoires.";
    } elseif ($password !== $confirmPassword) {
        $registerError = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 6) {
        $registerError = "Le mot de passe doit comporter au moins 6 caractères.";
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registerError = "L'email n'est pas valide.";
    } else {
        // Vérifier si le nom d'utilisateur existe déjà
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $count = $stmt->fetchColumn();
        
        // Vérifier si l'email existe déjà (seulement si un email a été fourni)
        $emailExists = false;
        if (!empty($email)) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $emailExists = $stmt->fetchColumn() > 0;
        }
        
        if ($count > 0) {
            $registerError = "Ce nom d'utilisateur est déjà utilisé.";
        } elseif ($emailExists) {
            $registerError = "Cet email est déjà utilisé.";
        } else {
            // Hasher le mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            try {
                // Insertion de l'utilisateur avec is_admin = 0 (non admin)
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, 0)");
                $stmt->execute([$username, $email ?: null, $hashedPassword]);
                
                // Créer un message de succès et rediriger vers la page de connexion
                $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                header("Location: login.php");
                exit;
            } catch (Exception $e) {
                $registerError = "Une erreur est survenue lors de l'inscription.";
                // Si en développement, afficher plus de détails
                if (!strpos($_SERVER['HTTP_HOST'] ?? '', 'ldpa-tech.fr')) {
                    $registerError .= " Détail: " . $e->getMessage();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AtlanStream - Inscription</title>
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
                <li><a href="login.php">Connexion</a></li>
                <li>
                    <button id="theme-toggle" class="theme-toggle" title="Changer de thème">
                        <span id="theme-icon">☀️</span>
                    </button>
                </li>
            </ul>
        </nav>
    </header>
    
    <main class="login-container">
        <div class="login-form register-form">
            <h2>Inscription</h2>
            <?php if (!empty($registerError)): ?>
                <div class="alert alert-error">
                    <?php echo $registerError; ?>
                </div>
            <?php endif; ?>
            
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" required value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email (facultatif)</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary">S'inscrire</button>
            </form>
            <div class="form-footer">
                <p>Vous avez déjà un compte ? <a href="login.php">Connectez-vous</a></p>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2023 AtlanStream - Tous droits réservés</p>
    </footer>
    
    <script src="../assets/js/theme.js"></script>
</body>
</html>
