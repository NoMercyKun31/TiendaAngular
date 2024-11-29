<?php
    $allowedOrigins = array('http://localhost:4200');
    if (in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }

    require_once "../librerias_php/setup_red_bean.php";
    // Incluir el archivo de configuración de RedBeanPHP
    // El carrito de compras sera almacenado en la sesion del usuario. 
    // Para usar la sesion en PHP es obligatorio tener la siguiente linea.
    session_start();
    // Recibir el JSON
    $json_recibido = file_get_contents('php://input');
    // Decodificar el JSON
    $id_producto = json_decode($json_recibido)->id;
    $cantidad = json_decode($json_recibido)->cantidad;
    // Vamos a guardar el carrito en una variable de sesion
    if( !isset($_SESSION['carrito']) ){
        $_SESSION['carrito'] = array();
    }
    // Agregar el producto al carrito en el array con los elementos recibidos
    $producto_en_carrito = false;
    for ($i=0; $i < count($_SESSION['carrito']); $i++) { 
        // Vamos a ver si el carrito ya tiene el producto
        if( $_SESSION['carrito'][$i][0] == $id_producto ){
            $producto_en_carrito = true;
            // Actualizar la cantidad
            $_SESSION['carrito'][$i][1] += $cantidad;
            break;
        }
    }
    if( !$producto_en_carrito ){
        array_push($_SESSION['carrito'], array($id_producto, $cantidad));
    } 

    echo json_encode("ok");
?>
