<?php
include '../includes/auth.php';
include '../conexion.php';
require_once '../includes/log.php';

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $conexion->prepare("DELETE FROM dispositivos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // ✅ Aquí SÍ debe ir el log
    registrar_log($conexion, $_SESSION['id'], 'Eliminó dispositivo ID ' . $id);
}

header("Location: listar.php");
exit();

