<?php
// Allow requests from your Angular app's origin
header("Access-Control-Allow-Origin: http://localhost:4200");
// Allow credentials
header("Access-Control-Allow-Credentials: true");
// Allow specific HTTP methods
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// Allow specific headers
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Just exit with 200 OK status
    exit(0);
}
require_once "../librerias_php/setup_red_bean.php";


class CarritoService {
    public function obtenerCarrito($usuarioId) {
        $items = R::findAll('carrito', 'usuario_id = ?', [$usuarioId]);
        $resultado = [];
        foreach ($items as $item) {
            $resultado[] = [
                'id' => $item->id,
                'usuario_id' => $item->usuario_id,
                'videojuego_id' => $item->videojuego_id,
                'cantidad' => $item->cantidad
            ];
        }
        return $resultado;
    }

    public function agregarAlCarrito($usuarioId, $videojuegoId, $cantidad) {
        $item = R::findOne('carrito', 'usuario_id = ? AND videojuego_id = ?', [$usuarioId, $videojuegoId]);
        if ($item) {
            $item->cantidad += $cantidad;
        } else {
            $item = R::dispense('carrito');
            $item->usuario_id = $usuarioId;
            $item->videojuego_id = $videojuegoId;
            $item->cantidad = $cantidad;
        }
        R::store($item);
        return ['success' => true, 'message' => 'Añadido al carrito'];
    }

    public function actualizarCantidad($usuarioId, $videojuegoId, $cantidad) {
        $item = R::findOne('carrito', 'usuario_id = ? AND videojuego_id = ?', [$usuarioId, $videojuegoId]);
        if ($item) {
            $item->cantidad = $cantidad;
            R::store($item);
            return ['success' => true, 'message' => 'Cantidad actualizada'];
        }
        return ['success' => false, 'message' => 'Item no encontrado'];
    }

    public function eliminarDelCarrito($usuarioId, $videojuegoId) {
        $item = R::findOne('carrito', 'usuario_id = ? AND videojuego_id = ?', [$usuarioId, $videojuegoId]);
        if ($item) {
            R::trash($item);
            return ['success' => true, 'message' => 'Item eliminado del carrito'];
        }
        return ['success' => false, 'message' => 'Item no encontrado'];
    }

    public function vaciarCarrito($usuarioId) {
        R::exec('DELETE FROM carrito WHERE usuario_id = ?', [$usuarioId]);
        return ['success' => true, 'message' => 'Carrito vaciado'];
    }

    public function sincronizarCarrito($usuarioId, $items) {
        R::exec('DELETE FROM carrito WHERE usuario_id = ?', [$usuarioId]);
        foreach ($items as $item) {
            $this->agregarAlCarrito($usuarioId, $item['videojuego_id'], $item['cantidad']);
        }
        return ['success' => true, 'message' => 'Carrito sincronizado'];
    }
}

$carritoService = new CarritoService();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'get' && isset($_GET['usuario_id'])) {
        echo json_encode($carritoService->obtenerCarrito($_GET['usuario_id']));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $result = [];
    switch ($data['action']) {
        case 'add':
            $result = $carritoService->agregarAlCarrito($data['usuario_id'], $data['videojuego_id'], $data['cantidad']);
            break;
        case 'update':
            $result = $carritoService->actualizarCantidad($data['usuario_id'], $data['videojuego_id'], $data['cantidad']);
            break;
        case 'remove':
            $result = $carritoService->eliminarDelCarrito($data['usuario_id'], $data['videojuego_id']);
            break;
        case 'clear':
            $result = $carritoService->vaciarCarrito($data['usuario_id']);
            break;
    }
    echo json_encode($result);
}
?>