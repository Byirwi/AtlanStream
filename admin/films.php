<?php
require_once '../includes/session.php';
require_once '../config/db_connect.php';
require_once '../includes/admin-auth.php';

// Vérification des droits admin
redirectIfNotAdmin();

// Supprimer un film
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM movies WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "Film supprimé avec succès.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Erreur lors de la suppression du film.";
    }
    header("Location: films.php");
    exit;
}

// Récupérer tous les films
$films = $pdo->query("SELECT movies.*, categories.name as category_name FROM movies 
                      LEFT JOIN categories ON movies.category_id = categories.id 
                      ORDER BY movies.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des films - AtlanStream Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }
        .admin-header {
            background-color: #2D3748;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 8px;
            border-left: 4px solid #E53E3E;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--card-hover-bg);
        }
        th {
            background-color: var(--card-bg);
            color: var(--primary-color);
        }
        tr:hover {
            background-color: var(--card-hover-bg);
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .edit-btn, .delete-btn {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .edit-btn {
            background-color: var(--primary-color);
            color: white;
        }
        .delete-btn {
            background-color: #E53E3E;
            color: white;
        }
        .poster-thumb {
            width: 50px;
            height: 70px;
            object-fit: cover;
            border-radius: 4px;
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
            <h2>Gestion des films</h2>
            <a href="edit-film.php" class="btn btn-primary">Ajouter un film</a>
        </div>
        
        <div class="admin-menu">
            <a href="dashboard.php">Tableau de bord</a>
            <a href="films.php" class="active">Gestion des films</a>
            <a href="categories.php">Gestion des catégories</a>
            <a href="users.php">Gestion des utilisateurs</a>
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
                    <th>Poster</th>
                    <th>Titre</th>
                    <th>Catégorie</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($films as $film): ?>
                <tr>
                    <td><?= $film['id'] ?></td>
                    <td>
                        <?php 
                        $poster = !empty($film['poster_url']) && file_exists(__DIR__ . '/../public/images/' . $film['poster_url']) 
                            ? '../public/images/' . $film['poster_url'] 
                            : '../public/images/default.jpg';
                        ?>
                        <img src="<?= $poster ?>" alt="<?= htmlspecialchars($film['title']) ?>" class="poster-thumb">
                    </td>
                    <td><?= htmlspecialchars($film['title']) ?></td>
                    <td><?= htmlspecialchars($film['category_name'] ?? 'Non catégorisé') ?></td>
                    <td class="actions">
                        <a href="edit-film.php?id=<?= $film['id'] ?>" class="edit-btn">Modifier</a>
                        <a href="films.php?delete=<?= $film['id'] ?>" class="delete-btn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce film?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($films)): ?>
                <tr>
                    <td colspan="5" style="text-align:center">Aucun film trouvé.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <footer>
        <p>&copy; 2025 AtlanStream - Administration</p>
    </footer>
</body>
</html>
