<?php
session_start();

// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fonction pour rediriger les utilisateurs non connectés
function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = "Vous devez être connecté pour accéder à cette page.";
        header("Location: login.php");
        exit;
    }
}
?>
