<?php

// $nombre_recibido = $_POST["nombre"];
// $email_recibido = $_POST["email"];
// $mensaje_recibido = $_POST["mensajeEnvio"];

// Si recibimos informacion desde Angular. La vamos a recibir en formato JSON para procesar ese JSON recibido.
$informacion_recibida = json_decode(file_get_contents("php://input"));

require "rb-mysql.php";
R::setup("mysql:host=localhost;dbname=prueba_php", "root", "");
$prueba = R::dispense("pruebaangular");
$prueba->nombre = $informacion_recibida->nombre;
$prueba->email = $informacion_recibida->email;
$prueba->mensaje = $informacion_recibida->mensaje;
R::store($prueba);
echo json_encode("ok");
