<?php

// Añade estos headers al principio del archivo PHP
header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Credentials: true');
$allowedOrigins = array('http://localhost:4200', 'http://otro-origen.com');
if (in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
}header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Tu código PHP existente aquí...


require "../librerias_php/setup_red_bean.php";

// Obtener todos los videojuegos
$videojuegos = R::getAll('
    SELECT * 
    FROM videojuego 
    ORDER BY en_descuento DESC, descuento DESC
');
// Devolver los videojuegos en formato JSON
echo json_encode($videojuegos); 

