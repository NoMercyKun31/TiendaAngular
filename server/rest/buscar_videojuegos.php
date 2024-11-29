<?php
header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

$allowedOrigins = array('http://localhost:4200', 'http://otro-origen.com');
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
if (in_array($origin, $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Incluir el archivo de configuración de RedBeanPHP
require_once "../librerias_php/setup_red_bean.php";

// Verificar si RedBeanPHP está configurado correctamente
if (!class_exists('R')) {
    error_log('Error de configuración de RedBeanPHP');
    echo json_encode(array('error' => 'Error de configuración de RedBeanPHP'));
    exit;
}

$termino = isset($_GET['termino']) ? trim($_GET['termino']) : '';

$videojuegos = array();

if ($termino) {
    $searchTerm = "%$termino%";
    
    try {
        error_log("Buscando videojuegos con término: " . $termino);
        $videojuegos = R::find('videojuego', 
            'nombre LIKE ? OR categoria LIKE ? OR compania LIKE ?', 
            [$searchTerm, $searchTerm, $searchTerm]
        );

        // Convertir los objetos Bean a arrays
        $videojuegos = array_map(function($bean) {
            return $bean->export();
        }, $videojuegos);

        error_log("Número de resultados encontrados: " . count($videojuegos));

    } catch (Exception $e) {
        error_log("Error en la búsqueda de videojuegos: " . $e->getMessage());
        echo json_encode(array('error' => 'Error en la búsqueda de videojuegos'));
        exit;
    }
}
error_log("Raw videojuegos data: " . print_r($videojuegos, true));

echo json_encode(array('data' => array_values($videojuegos)));

// Cerrar la conexión
R::close();
?>

