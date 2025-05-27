<?php
// Fichier de test simple pour vérifier les problèmes AJAX
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Test réussi',
    'time' => date('Y-m-d H:i:s'),
    'html' => '<p>Ceci est un test HTML</p>'
]);
