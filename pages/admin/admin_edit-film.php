<?php
require_once '../../includes/session.php';
require_once '../../config/db_connect.php';
require_once '../../includes/admin-auth.php';

// Activer l'affichage des erreurs pour le débogage
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Vérification des droits admin
redirectIfNotAdmin();

$error = '';
$success = '';
$debug_info = []; // Pour stocker les informations de débogage
$film = [
    'id' => '',
    'title' => '',
    'description' => '',
    'poster_url' => '',
    'category_id' => '',
    'year' => date('Y'),
    'duration' => 0,
    'director' => '',
    'actors' => ''
];

// Récupérer les catégories associées si on modifie un film existant
$selectedCategories = [];

// Si on modifie un film existant
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->execute([$id]);
    $found_film = $stmt->fetch();
    
    if ($found_film) {
        $film = $found_film;
        
        // Récupérer les catégories actuelles du film - Version plus robuste
        try {
            $stmtCategories = $pdo->prepare("SELECT category_id FROM movie_categories WHERE movie_id = ?");
            $stmtCategories->execute([$id]);
            
            // Récupérer les IDs de catégorie comme un tableau simple
            $selectedCategories = [];
            while ($row = $stmtCategories->fetch(PDO::FETCH_ASSOC)) {
                $selectedCategories[] = (int)$row['category_id']; // Conversion explicite en entier
            }
            
            $debug_info['selected_categories'] = $selectedCategories;
        } catch (Exception $e) {
            $error = "Erreur lors de la récupération des catégories: " . $e->getMessage();
        }
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
    $categories = isset($_POST['categories']) ? $_POST['categories'] : [];
    $year = isset($_POST['year']) ? (int)$_POST['year'] : date('Y');
    $duration = isset($_POST['duration']) ? (int)$_POST['duration'] : 0;
    $director = trim($_POST['director'] ?? '');
    $actors = trim($_POST['actors'] ?? '');
    
    // Débogage des données de formulaire
    $debug_info['post_data'] = [
        'title' => $title,
        'categories' => $categories,
        'year' => $year,
        'duration' => $duration
    ];
    
    // Conversion explicite des IDs de catégorie en entiers
    $categories = array_map('intval', $categories);
    
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
            try {
                // Démarrer une transaction
                $pdo->beginTransaction();
                
                // Mise à jour ou ajout du film (avec les nouveaux champs director et actors)
                if (!empty($film['id'])) {
                    $stmt = $pdo->prepare("UPDATE movies SET title = ?, description = ?, poster_url = ?, year = ?, duration = ?, director = ?, actors = ? WHERE id = ?");
                    $stmt->execute([$title, $description, $poster_url, $year, $duration, $director, $actors, $film['id']]);
                    $movieId = $film['id'];
                    $success = "Film mis à jour avec succès.";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO movies (title, description, poster_url, year, duration, director, actors) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $description, $poster_url, $year, $duration, $director, $actors]);
                    $movieId = $pdo->lastInsertId();
                    $success = "Film ajouté avec succès.";
                }
                
                // Supprimer les anciennes relations de catégories
                $stmtDelete = $pdo->prepare("DELETE FROM movie_categories WHERE movie_id = ?");
                $stmtDelete->execute([$movieId]);
                
                // Ajouter les nouvelles relations de catégories - Méthode améliorée
                if (!empty($categories)) {
                    // Méthode alternative avec des requêtes individuelles (plus robuste)
                    foreach ($categories as $categoryId) {
                        $stmtInsert = $pdo->prepare("INSERT INTO movie_categories (movie_id, category_id) VALUES (?, ?)");
                        $stmtInsert->execute([$movieId, $categoryId]);
                    }
                }
                
                // Valider la transaction
                $pdo->commit();
                
                $_SESSION['success'] = $success;
                header("Location: admin_films.php");
                exit;
                
            } catch (Exception $e) {
                // Annuler la transaction en cas d'erreur
                $pdo->rollBack();
                $error = "Erreur lors de l'enregistrement du film: " . $e->getMessage();
                $debug_info['error'] = $e->getMessage();
                $debug_info['trace'] = $e->getTraceAsString();
            }
        }
    }
    
    // En cas d'erreur, on conserve les valeurs saisies
    $film['title'] = $title;
    $film['description'] = $description;
    $film['year'] = $year;
    $film['duration'] = $duration;
    $film['director'] = $director;
    $film['actors'] = $actors;
    $selectedCategories = $categories;
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
        .categories-select {
            height: 150px;
            overflow-y: auto;
        }
        .form-section {
            margin-bottom: 30px;
            border-bottom: 1px solid var(--input-border);
            padding-bottom: 20px;
        }
        .form-section h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        .checkbox-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        .checkbox-item {
            display: flex;
            align-items: center;
            padding: 8px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        .checkbox-item:hover {
            background-color: var(--card-hover-bg);
        }
        .checkbox-item input[type="checkbox"] {
            margin-right: 8px;
        }
        .debug-info {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            color: #333;
            font-family: monospace;
            white-space: pre-wrap;
            display: none;
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
        
        <!-- Information sur les catégories sélectionnées (pour débogage) -->
        <?php if (!empty($debug_info)): ?>
            <div class="debug-info">
                <h4>Informations de débogage:</h4>
                <pre><?php print_r($debug_info); ?></pre>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="post" enctype="multipart/form-data">
                <div class="form-section">
                    <h3>Informations générales</h3>
                    <div class="form-group">
                        <label for="title">Titre du film *</label>
                        <input type="text" id="title" name="title" value="<?= htmlspecialchars($film['title']) ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="year">Année de sortie</label>
                            <input type="number" id="year" name="year" min="1900" max="<?= date('Y') + 5 ?>" value="<?= $film['year'] ?? date('Y') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="duration">Durée (minutes)</label>
                            <input type="number" id="duration" name="duration" min="0" value="<?= $film['duration'] ?? 0 ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>Catégories</h3>
                    <p>Sélectionnez une ou plusieurs catégories pour ce film (facultatif):</p>
                    
                    <!-- Afficher les valeurs actuellement sélectionnées pour le débogage -->
                    <p style="font-size: 0.9em; color: #6c757d;">
                        Catégories sélectionnées: 
                        <?= !empty($selectedCategories) ? implode(', ', $selectedCategories) : 'Aucune' ?>
                    </p>
                    
                    <div class="checkbox-grid">
                        <?php foreach ($categories as $category): ?>
                            <div class="checkbox-item">
                                <input type="checkbox" 
                                       id="category_<?= $category['id'] ?>" 
                                       name="categories[]" 
                                       value="<?= $category['id'] ?>" 
                                       <?= in_array((int)$category['id'], $selectedCategories) ? 'checked' : '' ?>>
                                <label for="category_<?= $category['id'] ?>">
                                    <?= htmlspecialchars($category['name']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>Réalisation</h3>
                    <div class="form-group">
                        <label for="director">Réalisateur</label>
                        <input type="text" id="director" name="director" value="<?= htmlspecialchars($film['director'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="actors">Acteurs principaux</label>
                        <input type="text" id="actors" name="actors" value="<?= htmlspecialchars($film['actors'] ?? '') ?>" placeholder="Séparés par des virgules">
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>Synopsis</h3>
                    <div class="form-group">
                        <textarea id="description" name="description" rows="6"><?= htmlspecialchars($film['description']) ?></textarea>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>Affiche du film</h3>
                    <div class="form-group">
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

        // Activer/désactiver l'affichage des informations de débogage
        document.addEventListener('DOMContentLoaded', function() {
            const debugInfo = document.querySelector('.debug-info');
            if (debugInfo) {
                const toggle = document.createElement('button');
                toggle.textContent = 'Afficher les infos de débogage';
                toggle.classList.add('btn', 'btn-secondary');
                toggle.style.marginBottom = '15px';
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (debugInfo.style.display === 'none' || !debugInfo.style.display) {
                        debugInfo.style.display = 'block';
                        toggle.textContent = 'Masquer les infos de débogage';
                    } else {
                        debugInfo.style.display = 'none';
                        toggle.textContent = 'Afficher les infos de débogage';
                    }
                });
                debugInfo.parentNode.insertBefore(toggle, debugInfo);
            }
        });
    </script>
</body>
</html>