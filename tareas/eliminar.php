<?php
include '../includes/auth.php';
include '../conexion.php';
include '../includes/log.php';

// Verificar permisos (admin o técnico)
if ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'tecnico') {
    header("Location: listar.php?error=1");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Obtener datos de la tarea
    $stmt = $conexion->prepare("SELECT descripcion, estado FROM tareas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $tarea = $resultado->fetch_assoc();

        if ($tarea['estado'] !== 'completada') {
            // No permitir eliminar tareas no completadas
            header("Location: listar.php?error=1");
            exit();
        }

        $descripcion = $tarea['descripcion'];

        // Eliminar la tarea
        $stmtDel = $conexion->prepare("DELETE FROM tareas WHERE id = ?");
        $stmtDel->bind_param("i", $id);
        $stmtDel->execute();
        $stmtDel->close();

        // Registrar log
        registrar_log($conexion, $_SESSION['usuario_id'], "Eliminó tarea ID $id: $descripcion");

        // Redirigir con mensaje de éxito
        header("Location: listar.php?eliminada=1");
        exit();
    }

    $stmt->close();
}

// Si no se encuentra la tarea o no se pasó un ID válido
header("Location: listar.php?error=1");
exit();

