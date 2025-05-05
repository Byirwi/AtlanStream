<?php
// Fichier de test pour vérifier la connexion à la base de données sur o2switch
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Afficher les informations du serveur
echo "<h2>Informations du serveur</h2>";
echo "<pre>";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'Non défini') . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Non défini') . "\n";
echo "PHP version: " . phpversion() . "\n";
echo "</pre>";

// Tester la connexion à la base de données
echo "<h2>Test de connexion à la base de données</h2>";
try {
    // Inclure la configuration
    require_once __DIR__ . '/config/db_config.php';
    
    echo "<p>Configuration chargée avec succès:</p>";
    echo "<pre>";
    echo "DB_HOST: " . DB_HOST . "\n";
    echo "DB_USER: " . DB_USER . "\n"; 
    echo "DB_NAME: " . DB_NAME . "\n";
    echo "DB_PORT: " . DB_PORT . "\n";
    echo "</pre>";
    
    // Tester la connexion MySQLi
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    if ($conn->connect_error) {
        throw new Exception("Erreur de connexion MySQLi: " . $conn->connect_error);
    }
    
    echo "<p style='color:green'>✓ Connexion MySQLi réussie!</p>";
    
    // Tester la connexion PDO
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";port=" . DB_PORT;
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    echo "<p style='color:green'>✓ Connexion PDO réussie!</p>";
    
    // Tester une requête simple
    $result = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p>Tables dans la base de données:</p>";
    echo "<ul>";
    foreach ($result as $table) {
        echo "<li>" . $table . "</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>❌ ERREUR: " . $e->getMessage() . "</p>";
}
?>
