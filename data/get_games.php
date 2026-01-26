<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Leer el archivo JSON
$json_file = __DIR__ . '/games.json';

if (!file_exists($json_file)) {
    http_response_code(404);
    echo json_encode(['error' => 'Archivo no encontrado']);
    exit;
}

$json_data = file_get_contents($json_file);
echo $json_data;
?>
