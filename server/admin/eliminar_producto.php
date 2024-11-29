<?php
require "../librerias_php/setup_red_bean.php";

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $videojuego = R::load('videojuego', $id);
        
        if ($videojuego->id) {
            // Eliminar la imagen asociada si existe
            $ruta_imagen = "../imagenes/{$videojuego->id}.jpg";
            if (file_exists($ruta_imagen)) {
                unlink($ruta_imagen);
            }
            
            // Eliminar el videojuego de la base de datos
            R::trash($videojuego);
            
            $mensaje = "Videojuego eliminado con éxito.";
            $tipo = "success";
        } else {
            $mensaje = "No se encontró el videojuego especificado.";
            $tipo = "warning";
        }
    } catch (Exception $e) {
        $mensaje = "Error al eliminar el videojuego: " . $e->getMessage();
        $tipo = "danger";
    }
} else {
    $mensaje = "ID de videojuego no proporcionado.";
    $tipo = "danger";
}

// Redirigir a la página de gestión con un mensaje
header("Location: gestionar_productos.php?mensaje=" . urlencode($mensaje) . "&tipo=" . $tipo);
exit;