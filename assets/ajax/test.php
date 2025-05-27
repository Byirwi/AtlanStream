<?php
// Fichier de test simple sans dépendances
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Test réussi',
    'time' => date('Y-m-d H:i:s')
]);
