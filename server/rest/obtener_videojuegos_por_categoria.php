<?php
// File: obtener_videojuegos_por_categoria.php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Credentials: true');
$allowedOrigins = array('http://localhost:4200', 'http://otro-origen.com');
if (in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
}header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');


require "../librerias_php/setup_red_bean.php";

$categoria = $_GET['categoria'] ?? '';

if ($categoria) {
    $videojuegos = R::find('videojuego', 'categoria = ?', [$categoria]);
} else {
    $videojuegos = R::findAll('videojuego');
}

echo json_encode(array_values($videojuegos));