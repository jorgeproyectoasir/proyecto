<?php
session_start();
require_once 'conexion.php';       // Asegúrate de tener acceso a la BD
require_once 'includes/log.php';   // Función de logging

$usuario_id = $_SESSION['id'] ?? null;

if ($usuario_id) {
    registrar_log($conexion, $usuario_id, 'Cierre de sesión');
}

session_unset();
session_destroy();
header("Location: index.php");
exit();
?>

