<?php
session_start();
require_once 'conexion.php';
require_once 'includes/log.php';

$usuario_id = $_SESSION['id'] ?? null;

if ($usuario_id) {
    registrar_log($conn, $usuario_id, 'logout', 'El usuario cerró sesión');
}

session_unset();
session_destroy();
header("Location: index.php");
exit();
?>

