<?php
require_once '../../includes/session.php';
require_once '../../config/db_connect.php';
require_once '../../includes/admin-auth.php';

// Vérification des droits admin
redirectIfNotAdmin();

$error = '';
$success = '';
$film = [
    'id' => '',
    'title' => '',
    'description' => '',
    'poster_url' => '',
    'category_id' => '',
    'year' => date('Y'),
    'duration' => 0
];

// Si on modifie un film existant
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->execute([$id]);
    $found_film = $stmt->fetch();
    
    if ($found_film) {
        $film = $found_film;
    } else {
        $_SESSION['error'] = "Film introuvable.";
        header("Location: admin_films.php");
        exit;
    }
}

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = isset($_POST['category_id']) && $_POST['category_id'] !== '' ? (int)$_POST['category_id'] : null;
    $year = isset($_POST['year']) ? (int)$_POST['year'] : date('Y');
    $duration = isset($_POST['duration']) ? (int)$_POST['duration'] : 0;
    
    if (empty($title)) {
        $error = "Le titre est obligatoire.";
    } else {
        // Gestion de l'upload d'image
        $poster_url = $film['poster_url']; // Valeur par défaut
        
        if (!empty($_FILES['poster']['name'])) {
            $upload_dir = "../../public/images/";
            
            // Créer le répertoire s'il n'existe pas
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (!in_array($file_extension, $allowed_extensions)) {
                $error = "Seules les images JPG, JPEG, PNG, GIF ou WEBP sont autorisées.";
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
                $stmt = $pdo->prepare("UPDATE movies SET title = ?, description = ?, category_id = ?, poster_url = ?, year = ?, duration = ? WHERE id = ?");
                $stmt->execute([$title, $description, $category_id, $poster_url, $year, $duration, $film['id']]);
                $success = "Film mis à jour avec succès.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO movies (title, description, category_id, poster_url, year, duration) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $category_id, $poster_url, $year, $duration]);
                $success = "Film ajouté avec succès.";
            }
            
            if (empty($error)) {
                $_SESSION['success'] = $success;
                header("Location: admin_films.php");
                exit;
            }
        }
    }
    
    // En cas d'erreur, on conserve les valeurs saisies
    $film['title'] = $title;
    $film['description'] = $description;
    $film['category_id'] = $category_id;
    $film['year'] = $year;
    $film['duration'] = $duration;
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
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        .preview-container {
            margin-top: 15px;
            text-align: center;
        }
        .poster-preview {
            max-width: 200px;
            border-radius: 8px;
            box-shadow: var(--shadow-medium);
            margin-top: 10px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        .file-input-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }
        .file-input-button {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block;
        }
        .file-name {
            margin-left: 10px;
            font-style: italic;
        }
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-row .form-group {
            flex: 1;
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
            <h2><?= !empty($film['id']) ? 'Modifier' : 'Ajouter' ?> un film</h2>
        </div>
        
        <div class="admin-menu">
            <a href="admin_dashboard.php">Tableau de bord</a>
            <a href="admin_films.php" class="active">Gestion des films</a>
            <a href="admin_categories.php">Gestion des catégories</a>
            <a href="admin_users.php">Gestion des utilisateurs</a>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Titre du film *</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($film['title']) ?>" required>
                </div>
                
                <div class="form-row">
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
                        <label for="year">Année de sortie</label>
                        <input type="number" id="year" name="year" min="1900" max="<?= date('Y') + 5 ?>" value="<?= $film['year'] ?? date('Y') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="duration">Durée (minutes)</label>
                        <input type="number" id="duration" name="duration" min="0" value="<?= $film['duration'] ?? 0 ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Synopsis</label>
                    <textarea id="description" name="description" rows="6"><?= htmlspecialchars($film['description']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Poster du film</label>
                    <div class="file-input-wrapper">
                        <div class="file-input-button">Choisir une image</div>
                        <input type="file" id="poster" name="poster" accept="image/*" onchange="updateFileName(this)">
                    </div>
                    <span class="file-name" id="fileName">Aucun fichier sélectionné</span>
                    
                    <div class="preview-container">
                        <?php if (!empty($film['poster_url'])): ?>
                            <p>Poster actuel :</p>
                            <img src="<?= '../../public/images/' . htmlspecialchars($film['poster_url']) ?>" alt="Poster actuel" class="poster-preview" id="preview">
                        <?php else: ?>
                            <img src="../../public/images/default.jpg" alt="Poster par défaut" class="poster-preview" id="preview">
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-buttons" style="margin-top: 30px;">
                    <button type="submit" class="btn btn-primary"><?= !empty($film['id']) ? 'Mettre à jour' : 'Ajouter' ?> le film</button>
                    <a href="admin_films.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2025 AtlanStream - Administration</p>
    </footer>
    
    <script src="../../assets/js/theme.js"></script>
    <script>
        // Afficher le nom du fichier sélectionné
        function updateFileName(input) {
            const fileName = document.getElementById('fileName');
            if (input.files.length > 0) {
                fileName.textContent = input.files[0].name;
                
                // Prévisualiser l'image
                const preview = document.getElementById('preview');
                const file = input.files[0];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                
                reader.readAsDataURL(file);
            } else {
                fileName.textContent = 'Aucun fichier sélectionné';
            }
        }
    </script>
</body>
</html>