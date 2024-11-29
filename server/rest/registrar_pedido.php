<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

require_once "../librerias_php/setup_red_bean.php";

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    // Verificar que hay un usuario_id
    if (!isset($data->usuario_id) || !$data->usuario_id) {
        throw new Exception('Usuario no autenticado');
    }

    // Crear el pedido
    $pedido = R::dispense('pedido');
    $pedido->usuario_id = $data->usuario_id;
    $pedido->nombreCompleto = $data->nombreCompleto;
    $pedido->email = $data->email;
    $pedido->direccion = $data->direccion;
    $pedido->ciudad = $data->ciudad;
    $pedido->provincia = $data->provincia;
    $pedido->codigoPostal = $data->codigoPostal;
    $pedido->titularTarjeta = $data->titularTarjeta;
    $pedido->numeroTarjeta = $data->numeroTarjeta;
    $pedido->fechaVencimiento = $data->fechaVencimiento;
    $pedido->cvv = $data->cvv;
    $pedido->fecha = date('Y-m-d H:i:s');
    $pedido->total = $data->total;

    // Guardar el pedido primero para obtener su ID
    $pedido_id = R::store($pedido);

    // Guardar los items del pedido
    if (isset($data->items) && is_array($data->items)) {
        foreach ($data->items as $item) {
            // Actualizar el stock del videojuego
            $videojuego = R::load('videojuego', $item->id);
            if (!$videojuego->id) {
                throw new Exception('Videojuego no encontrado');
            }
            if ($videojuego->stock < $item->cantidad) {
                throw new Exception('No hay suficiente stock para ' . $videojuego->nombre);
            }
            $videojuego->stock -= $item->cantidad;
            R::store($videojuego);

            $pedidoVideojuego = R::dispense('pedidovideojuego');
            $pedidoVideojuego->pedido_id = $pedido_id;
            $pedidoVideojuego->usuario_id = $data->usuario_id;  
            $pedidoVideojuego->videojuego_id = $item->id;
            $pedidoVideojuego->cantidad = $item->cantidad;
            $pedidoVideojuego->precio = $item->precio;
            $pedidoVideojuego->descuento = $item->descuento ?? 0;
            $pedidoVideojuego->precio_final = isset($item->descuento) ? $item->precio * (1 - $item->descuento/100) : $item->precio;
            $pedidoVideojuego->nombre = $item->nombre;
            $pedidoVideojuego->subtotal = $pedidoVideojuego->precio_final * $item->cantidad;
            $pedidoVideojuego->fecha = date('Y-m-d H:i:s');  
            R::store($pedidoVideojuego);
        }
    }

    echo json_encode([
        "status" => "ok", 
        "id" => $pedido_id,
        "message" => "Pedido registrado correctamente. Total: €" . number_format($pedido->total, 2)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => $e->getMessage()
    ]);
}
?>
