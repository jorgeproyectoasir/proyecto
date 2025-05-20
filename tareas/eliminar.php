<?php
include '../includes/auth.php';
include '../conexion.php';
include '../includes/log.php';

// Verificar permisos (admin o técnico)
if ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'tecnico') {
    echo "Acceso denegado.";
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Obtener nombre o descripción para el log (opcional)
    $stmt = $conexion->prepare("SELECT descripcion, estado FROM tareas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $tarea = $resultado->fetch_assoc();
        if ($tarea['estado'] !== 'completada') {
	    echo "No puedes eliminar una tarea que aún no ha sido completada.";
    		exit();
	}

        $descripcion = $tarea['descripcion'];

        // Eliminar la tarea
        $stmtDel = $conexion->prepare("DELETE FROM tareas WHERE id = ?");
        $stmtDel->bind_param("i", $id);
        $stmtDel->execute();
        $stmtDel->close();

        registrar_log($conexion, $_SESSION['usuario_id'], 'eliminar_tarea', "Tarea eliminada: $descripcion (ID $id)");
    }

    $stmt->close();
}

header("Location: listar.php");
exit();
?>

