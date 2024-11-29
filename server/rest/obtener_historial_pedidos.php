<?php
header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "../librerias_php/setup_red_bean.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $usuario_id = isset($_GET['usuario_id']) ? $_GET['usuario_id'] : null;
    
    if (!$usuario_id) {
        echo json_encode(array(
            'success' => false,
            'message' => 'ID de usuario no proporcionado'
        ));
        exit();
    }

    error_log("Buscando pedidos para usuario_id: " . $usuario_id);
    $pedidos = R::find('pedido', 'usuario_id = ? ORDER BY fecha DESC', [$usuario_id]);
    error_log("Pedidos encontrados: " . count($pedidos));
    
    $resultado = array();

    foreach ($pedidos as $pedido) {
        error_log("Procesando pedido ID: " . $pedido->id);
        
        $items = R::find('pedidovideojuego', 'pedido_id = ?', [$pedido->id]);
        error_log("Items encontrados para pedido " . $pedido->id . ": " . count($items));
        
        $pedido_items = array();
        
        foreach ($items as $item) {
            error_log("Procesando item ID: " . $item->id);
            
            // Usar los campos que ya están en pedidovideojuego
            $pedido_items[] = array(
                'id' => $item->id,
                'producto_id' => $item->videojuego_id,
                'cantidad' => $item->cantidad,
                'precio' => floatval($item->precio),
                'precio_final' => floatval($item->precio_final),
                'descuento' => floatval($item->descuento),
                'subtotal' => floatval($item->subtotal),
                'nombre' => $item->nombre,
                'fecha' => $item->fecha
            );
        }

        $resultado[] = array(
            'id' => $pedido->id,
            'fecha' => $pedido->fecha,
            'total' => floatval($pedido->total),
            'descuento' => floatval($pedido->descuento),
            'nombreCompleto' => $pedido->nombreCompleto,
            'email' => $pedido->email,
            'direccion' => $pedido->direccion,
            'ciudad' => $pedido->ciudad,
            'provincia' => $pedido->provincia,
            'codigoPostal' => $pedido->codigoPostal,
            'items' => $pedido_items
        );
    }

    error_log("Resultado final: " . json_encode($resultado));

    echo json_encode(array(
        'success' => true,
        'pedidos' => $resultado
    ));
} else {
    echo json_encode(array(
        'success' => false,
        'message' => 'Método no permitido'
    ));
}