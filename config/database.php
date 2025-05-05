<?php
if ($_SERVER['HTTP_HOST'] === 'localhost') {
    // Paramètres pour le développement local
    $host = 'localhost';
    $dbname = 'atlanstream'; // nom de la base locale
    $username = 'root';      // utilisateur local par défaut sous WAMP
    $password = '';          // mot de passe local (souvent vide sous WAMP)
} else {
    // Paramètres pour o2switch (production)
    $host = 'localhost';
    $dbname = 'delo5366_AtlanStream';
    $username = 'delo5366_StreamWave_Account';
    $password = 'X2E_n!o9pjCriYnuKwT@rgaQ';
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}
?>