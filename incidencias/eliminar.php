<?php
include '../includes/auth.php';
include '../conexion.php';
require_once '../includes/log.php';

if ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'tecnico') {
    echo "Acceso denegado.";
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Obtener descripción para el log
    $stmt = $conexion->prepare("SELECT descripcion FROM incidencias WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $incidencia = $res->fetch_assoc();
        $descripcion = $incidencia['descripcion'];

        $stmtDel = $conexion->prepare("DELETE FROM incidencias WHERE id = ?");
        $stmtDel->bind_param("i", $id);
        $stmtDel->execute();
        $stmtDel->close();

        registrar_log($conexion, $_SESSION['usuario_id'], 'eliminar_incidencia', "Incidencia eliminada: $descripcion (ID $id)");
    }

    $stmt->close();
}

header("Location: listar.php?eliminado=1");
exit();

