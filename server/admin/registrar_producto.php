<?php

require "../librerias_php/setup_red_bean.php";

$videojuego = R::dispense('videojuego');
$videojuego->nombre = $_POST['nombre'];
$videojuego->genero = $_POST['genero'];
$videojuego->compania = $_POST['compania'];
$videojuego->anyolanzamiento = $_POST['anyolanzamiento'];
$videojuego->precio = $_POST['precio'];
$videojuego->stock = max(0, min(99, intval($_POST['stock']))); // Nuevo campo de stock
$videojuego->descuento = 0; // Inicializar descuento
$videojuego->en_descuento = false; // Inicializar en_descuento

$id_generada = R::store($videojuego);

if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $nuevo_nombre = $id_generada . '.' . $extension;
    move_uploaded_file($_FILES['foto']['tmp_name'], "../imagenes/$nuevo_nombre");
    $videojuego->imagen = $nuevo_nombre;
    R::store($videojuego);
}

include("registro_ok.php");
?>