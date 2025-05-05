<?php
require_once '../includes/session.php';
require_once '../config/db_connect.php';
require_once '../includes/admin-auth.php';

// Vérification des droits admin
redirectIfNotAdmin();

$error = '';
$success = '';
$film = [
    'id' => '',
    'title' => '',
    'description' => '',
    'poster_url' => '',
    'category_id' => ''
];

// Si on modifie un film existant
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->execute([$id]);
    $film = $stmt->fetch();
    
    if (!$film) {
        $_SESSION['error'] = "Film introuvable.";
        header("Location: films.php");
        exit;
    }
}

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    
    if (empty($title)) {
        $error = "Le titre est obligatoire.";
    } else {
        // Gestion de l'upload d'image
        $poster_url = $film['poster_url']; // Valeur par défaut
        
        if (!empty($_FILES['poster']['name'])) {
            $upload_dir = "../public/images/";
            $file_extension = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($file_extension, $allowed_extensions)) {
                $error = "Seules les images JPG, JPEG, PNG ou GIF sont autorisées.";
            } elseif ($_FILES['poster']['size'] > 5000000) { // 5MB max
                $error = "L'image ne doit pas dépasser 5MB.";
            } else {
                $new_filename = time() . '_' . uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['poster']['tmp_name'], $upload_path)) {
                    $poster_url = $new_filename;
                    
                    // Supprimer l'ancienne image si on en uploade une nouvelle
                    if ($film['poster_url'] && $film['poster_url'] !== 'default.jpg' && file_exists($upload_dir . $film['poster_url'])) {
                        unlink($upload_dir . $film['poster_url']);
                    }
                } else {
                    $error = "Erreur lors de l'upload de l'image.";
                }
            }
        }
        
        if (empty($error)) {
            // Mise à jour ou ajout du film
            if (!empty($film['id'])) {
                $stmt = $pdo->prepare("UPDATE movies SET title = ?, description = ?, category_id = ?, poster_url = ? WHERE id = ?");
                $stmt->execute([$title, $description, $category_id, $poster_url, $film['id']]);
                $success = "Film mis à jour avec succès.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO movies (title, description, category_id, poster_url) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $description, $category_id, $poster_url]);
                $success = "Film ajouté avec succès.";
            }
            
            if (empty($error)) {
                $_SESSION['success'] = $success;
                header("Location: films.php");
                exit;
            }
        }
    }
}

// Récupérer les catégories
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= !empty($film['id']) ? 'Modifier' : 'Ajouter' ?> un film - AtlanStream Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-container {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
        }
        .admin-header {
            background-color: #2D3748;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 8px;
            border-left: 4px solid #E53E3E;
        }
        .admin-menu {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }
        .admin-menu a {
            background: var(--card-bg);
            padding: 12px 20px;
            border-radius: 6px;
            color: var(--light-text);
            text-decoration: none;
            transition: all 0.3s;
        }
        .admin-menu a:hover, .admin-menu a.active {
            background: var(--primary-color);
            color: white;
        }
        .form-container {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 8px;
            box-shadow: var(--shadow-medium);
        }
        textarea {
            width: 100%;
            padding: 14px;
            border-radius: 6px;
            background-color: var(--input-bg);
            border: 1px solid var(--input-border);
            color: var(--light-text);
            font-size: 16px;
            min-height: 150px;
            font-family: inherit;
        }
        select {
            width: 100%;
            padding: 14px;
            border-radius: 6px;
            background-color: var(--input-bg);
            border: 1px solid var(--input-border);
            color: var(--light-text);
            font-size: 16px;
        }
        .poster-preview {
            max-width: 200px;
            margin-top: 10px;
            border-radius: 6px;
            box-shadow: var(--shadow-soft);
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
                <li><a href="../pages/Accueil.php">Voir le site</a></li>
                <li><a href="../pages/logout.php" class="logout-btn">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="admin-container">
        <div class="admin-header">
            <h2><?= !empty($film['id']) ? 'Modifier' : 'Ajouter' ?> un film</h2>
        </div>
        
        <div class="admin-menu">
            <a href="dashboard.php">Tableau de bord</a>
            <a href="films.php" class="active">Gestion des films</a>
            <a href="categories.php">Gestion des catégories</a>
            <a href="users.php">Gestion des utilisateurs</a>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Titre</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($film['title']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"><?= htmlspecialchars($film['description']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="category_id">Catégorie</label>
                    <select id="category_id" name="category_id">
                        <option value="">-- Sélectionner une catégorie --</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= $film['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="poster">Poster</label>
                    <input type="file" id="poster" name="poster" accept="image/*">
                    <?php if (!empty($film['poster_url'])): ?>
                        <p>Poster actuel :</p>
                        <img src="<?= '../public/images/' . htmlspecialchars($film['poster_url']) ?>" alt="Poster actuel" class="poster-preview">
                    <?php endif; ?>
                </div>
                
                <div class="form-buttons" style="margin-top: 30px;">
                    <button type="submit" class="btn btn-primary"><?= !empty($film['id']) ? 'Mettre à jour' : 'Ajouter' ?> le film</button>
                    <a href="films.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2025 AtlanStream - Administration</p>
    </footer>
</body>
</html>
