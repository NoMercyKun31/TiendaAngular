<?php
session_start();
header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Credentials: true');
$allowedOrigins = array('http://localhost:4200', 'http://otro-origen.com');
if (in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
}header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
// Vamos a guardar el carrito en una variable de sesión
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    // De cada ID de producto que haya en carrito,
    // Vamos a sacar el videojuego correspondiente
    // Toda esa información la vamos a entregar a la parte cliente
    require_once "../librerias_php/setup_red_bean.php";
    $ids = array();
    $cantidades = array();
    foreach ($_SESSION['carrito'] as $elemento) {
        array_push($ids, $elemento[0]); // El elemento [0] es el ID
        array_push($cantidades, $elemento[1]); // El elemento [1] es la cantidad
    }

    // Si no hay IDs, devolvemos un array vacío
    if (empty($ids)) {
        echo json_encode(array());
        exit;
    }

    $id_sql = implode(",", $ids); // Generamos la lista de IDs para la consulta
    $videojuegos = R::getAll("SELECT * FROM videojuego WHERE id IN ($id_sql)");

    $respuesta = array();
    for ($i = 0; $i < count($videojuegos); $i++) {
        $respuesta[] = array(
            "videojuego" => $videojuegos[$i],
            "cantidad" => $cantidades[$i]
        );
    }

    echo json_encode($respuesta);
} else {
    // Si no existe el carrito o está vacío, devolvemos un array vacío
    echo json_encode(array());
}
?>
