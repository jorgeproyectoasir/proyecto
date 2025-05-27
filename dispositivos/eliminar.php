<?php
include '../includes/auth.php';
require_once '../includes/log.php';
session_start();

if ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'tecnico') {
    $_SESSION['flash'] = [
        'tipo' => 'danger',
        'mensaje' => '🚫 No tienes permisos para eliminar dispositivos.'
    ];
    header("Location: listar.php");
    exit();
}

include '../conexion.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    $_SESSION['flash'] = [
        'tipo' => 'danger',
        'mensaje' => '⚠️ ID de dispositivo no válido.'
    ];
    header("Location: listar.php");
    exit();
}

// Comprobar si existe
$stmt = $conexion->prepare("SELECT nombre FROM dispositivos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    $_SESSION['flash'] = [
        'tipo' => 'danger',
        'mensaje' => '❌ El dispositivo no existe o ya fue eliminado.'
    ];
    header("Location: listar.php");
    exit();
}

$dispositivo = $res->fetch_assoc();

// Eliminar
$stmt = $conexion->prepare("DELETE FROM dispositivos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

registrar_log($conexion, $_SESSION["usuario_id"], "Eliminó el dispositivo '{$dispositivo['nombre']}' (ID $id)");

$_SESSION['flash'] = [
    'tipo' => 'success',
    'mensaje' => "✅ Dispositivo eliminado correctamente."
];

header("Location: listar.php");
exit();

