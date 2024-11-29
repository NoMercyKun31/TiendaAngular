<?php
require_once "../librerias_php/setup_red_bean.php";

class FavoritoService {
    public function agregarFavorito($usuarioId, $videojuegoId) {
        $favoritoExistente = R::findOne('favorito', 'usuario_id = ? AND videojuego_id = ?', [$usuarioId, $videojuegoId]);
        if ($favoritoExistente) {
            return ['success' => false, 'message' => 'Ya está en favoritos'];
        }

        $favorito = R::dispense('favorito');
        $favorito->usuario_id = $usuarioId;
        $favorito->videojuego_id = $videojuegoId;
        R::store($favorito);

        return ['success' => true, 'message' => 'Añadido a favoritos'];
    }

    public function eliminarFavorito($usuarioId, $videojuegoId) {
        $favorito = R::findOne('favorito', 'usuario_id = ? AND videojuego_id = ?', [$usuarioId, $videojuegoId]);
        if ($favorito) {
            R::trash($favorito);
            return ['success' => true, 'message' => 'Eliminado de favoritos'];
        }
        return ['success' => false, 'message' => 'No encontrado en favoritos'];
    }

    public function obtenerFavoritos($usuarioId) {
        $favoritos = R::findAll('favorito', 'usuario_id = ?', [$usuarioId]);
        $videojuegos = [];
        foreach ($favoritos as $favorito) {
            $videojuego = R::load('videojuego', $favorito->videojuego_id);
            if ($videojuego->id) {
                $videojuegos[] = $videojuego;
            }
        }
        return $videojuegos;
    }
}

$favoritoService = new FavoritoService();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data['action'] === 'add') {
        echo json_encode($favoritoService->agregarFavorito($data['usuario_id'], $data['videojuego_id']));
    } elseif ($data['action'] === 'remove') {
        echo json_encode($favoritoService->eliminarFavorito($data['usuario_id'], $data['videojuego_id']));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['usuario_id'])) {
        echo json_encode($favoritoService->obtenerFavoritos($_GET['usuario_id']));
    }
}

?>