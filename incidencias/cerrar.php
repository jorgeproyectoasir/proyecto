<?php
include '../includes/auth.php';
include '../conexion.php';
require_once '../includes/log.php';

if ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'tecnico') {
    die("Acceso denegado.");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $stmt = $conexion->prepare("UPDATE incidencias SET estado = 'cerrada' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

registrar_log($conexion, $_SESSION["usuario_id"], 'Cerró incidencia ID ' . $id);

header("Location: listar.php");
exit();
?>

