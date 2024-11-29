<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once "../librerias_php/setup_red_bean.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $videojuego = R::load('videojuego', $id);
    
    if ($videojuego->id) {
        echo json_encode([
            'id' => $videojuego->id,
            'nombre' => $videojuego->nombre,
            'categoria' => $videojuego->categoria,
            'precio' => $videojuego->precio,
            'imagen' => $videojuego->imagen,
            'descuento' => $videojuego->descuento,
            'en_descuento' => $videojuego->en_descuento,
            'stock' => $videojuego->stock,
            'compania' => $videojuego->compania,
            'anyolanzamiento' => $videojuego->anyolanzamiento
        ]);
    } else {
        echo json_encode(['error' => 'Videojuego no encontrado']);
    }
}

