<?php
require "rb-mysql.php";
R::setup("mysql:host=localhost;dbname=tienda_angular_php_pixelperfect", "root", "");

// Configurar RedBean en modo desarrollo
R::freeze(false);  // false = modo desarrollo, permite crear/modificar tablas
?>