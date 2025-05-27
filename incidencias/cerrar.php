<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Solo iniciar sesión si aún no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../includes/auth.php';
include '../conexion.php';
require_once '../includes/log.php';
require_once '../includes/historial.php'; // ← Asegúrate de que este archivo exista

// Verificación de permisos
if ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'tecnico') {
    $_SESSION['flash'] = [
        'tipo' => 'danger',
        'mensaje' => '❌ Acceso denegado.'
    ];
    header("Location: listar.php");
    exit;
}

// Obtener el ID de la incidencia desde la URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Actualizar estado a 'cerrada'
    $stmt = $conexion->prepare("UPDATE incidencias SET estado = 'cerrada' WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Registrar log e historial
        registrar_log($conexion, $_SESSION["usuario_id"], 'Cerró incidencia ID ' . $id);
        registrar_historial($conexion, $_SESSION['usuario_id'], 'incidencia', $id, 'cerrada');

        // Mensaje de éxito
        $_SESSION['flash'] = [
            'tipo' => 'success',
            'mensaje' => '✅ Incidencia cerrada correctamente.'
        ];
    } else {
        $_SESSION['flash'] = [
            'tipo' => 'danger',
            'mensaje' => '❌ Error al preparar la consulta SQL.'
        ];
    }
} else {
    $_SESSION['flash'] = [
        'tipo' => 'warning',
        'mensaje' => '⚠️ ID de incidencia no válido.'
    ];
}

// Redirigir a la lista de incidencias
header("Location: listar.php");
exit;

