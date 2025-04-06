<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AtlanStream - Connexion</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>AtlanStream</h1>
        </div>
        <nav>
            <ul>
                <li><a href="Accueil.php">Accueil</a></li>
                <li><a href="catalogue.php">Catalogue</a></li>
            </ul>
        </nav>
    </header>
    
    <main class="login-container">
        <div class="login-form">
            <h2>Connexion</h2>
            <form action="#" method="POST">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Se connecter</button>
            </form>
            <div class="form-footer">
                <p>Vous n'avez pas de compte ? <a href="#">Inscrivez-vous</a></p>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2023 AtlanStream - Tous droits réservés</p>
    </footer>
</body>
</html>
