<?php
include_once("../conexion.php");
include_once("../includes/auth.php");
include_once("../includes/log.php");

if (!isset($_SESSION["id"]) || !isset($_GET["id"])) {
    header("Location: listar.php");
    exit;
}

// Verificar permisos: solo admin (1) o técnico (2) pueden eliminar
if ($_SESSION["rol_id"] != 1 && $_SESSION["rol_id"] != 2) {
    die("No tienes permiso para realizar esta acción.");
}

$servicio_id = intval($_GET["id"]);

// Obtener nombre del servicio antes de eliminar (opcional, para el log)
$stmt = $conn->prepare("SELECT nombre FROM servicios WHERE id = ?");
$stmt->bind_param("i", $servicio_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $servicio = $resultado->fetch_assoc();
    $nombre_servicio = $servicio['nombre'];

    // Eliminar el servicio
    $stmt_del = $conn->prepare("DELETE FROM servicios WHERE id = ?");
    $stmt_del->bind_param("i", $servicio_id);
    $stmt_del->execute();
    $stmt_del->close();

    // Registrar log
    registrar_log($conn, $_SESSION["id"], "eliminar_servicio", "Se eliminó el servicio: $nombre_servicio (ID $servicio_id)");
}

$stmt->close();
header("Location: listar.php");
exit;
?>

