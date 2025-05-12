<?php
$host = "localhost";
$usuario = "jorge";
$contrasena = "Jorge1234!";
$base_datos = "plataforma_it";
$conexion = new mysqli($host, $usuario, $contrasena, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Alias para compatibilidad
$conn = $conexion;
?>

