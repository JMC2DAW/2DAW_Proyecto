<?php
include("templates/session.php");

// Cargar datos de juegos
$gamesData = file_get_contents('data/games.json');
$games = json_decode($gamesData, true)['games'];

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Manejar agregar al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $gameId = intval($_POST['game_id']);
    
    // Buscar el juego en los datos
    $selectedGame = null;
    foreach ($games as $game) {
        if ($game['id'] == $gameId) {
            $selectedGame = $game;
            break;
        }
    }
    
    if ($selectedGame) {
        // Verificar si el juego ya está en el carrito
        $gameFound = false;
        foreach ($_SESSION['carrito'] as &$item) {
            if ($item['id'] == $gameId) {
                $item['cantidad']++;
                $gameFound = true;
                break;
            }
        }
        
        // Si no está, añadirlo
        if (!$gameFound) {
            $_SESSION['carrito'][] = [
                'id' => $selectedGame['id'],
                'title' => $selectedGame['title'],
                'price' => $selectedGame['price'],
                'image' => $selectedGame['image'],
                'cantidad' => 1
            ];
        }
    }
}

// Redirigir a compras
header('Location: compras.php');
exit();
?>
