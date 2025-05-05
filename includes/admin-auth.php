<?php
require_once 'session.php';

// Vérifier si l'utilisateur est un administrateur
function isAdmin() {
    return isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

// Rediriger si l'utilisateur n'est pas un administrateur
function redirectIfNotAdmin() {
    if (!isAdmin()) {
        $_SESSION['error'] = "Accès réservé aux administrateurs.";
        header("Location: ../login.php");
        exit;
    }
}
?>
