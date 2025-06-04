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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id <= 0) {
        $_SESSION['flash'] = [
            'tipo' => 'danger',
            'mensaje' => '⚠️ ID de dispositivo no válido.'
        ];
        header("Location: listar.php");
        exit();
    }

    // Buscar nombre del dispositivo
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
    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Confirmar eliminación</title>
        <link rel="stylesheet" href="/css/estilo.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body style="background-color: #f8d7da;">
    <div class="container mt-5">
        <div class="panel-container text-center p-4 bg-white rounded shadow">
            <h2 class="text-danger mb-4">¿Estás seguro de eliminar el dispositivo?</h2>
            <p><strong>Nombre:</strong> <?= htmlspecialchars($dispositivo['nombre']) ?></p>
            <form method="POST">
                <input type="hidden" name="id" value="<?= $id ?>">
                <div class="botones-centrados mt-4">
                    <button type="submit" class="btn btn-danger boton-accion">Sí, eliminar</button>
                    <a href="listar.php" class="btn btn-secondary boton-accion">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
    </body>
    </html>

    <?php
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        $_SESSION['flash'] = [
            'tipo' => 'danger',
            'mensaje' => '⚠️ ID inválido al intentar eliminar.'
        ];
        header("Location: listar.php");
        exit();
    }

    // Obtener nombre antes de eliminar
    $stmt = $conexion->prepare("SELECT nombre FROM dispositivos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        $_SESSION['flash'] = [
            'tipo' => 'danger',
            'mensaje' => '❌ El dispositivo ya fue eliminado o no existe.'
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
}
?>
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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id <= 0) {
        $_SESSION['flash'] = [
            'tipo' => 'danger',
            'mensaje' => '⚠️ ID de dispositivo no válido.'
        ];
        header("Location: listar.php");
        exit();
    }

    // Buscar nombre del dispositivo
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
    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Confirmar eliminación</title>
        <link rel="stylesheet" href="/css/estilo.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body style="background-color: #f8d7da;">
    <div class="container mt-5">
        <div class="panel-container text-center p-4 bg-white rounded shadow">
            <h2 class="text-danger mb-4">¿Estás seguro de eliminar el dispositivo?</h2>
            <p><strong>Nombre:</strong> <?= htmlspecialchars($dispositivo['nombre']) ?></p>
            <form method="POST">
                <input type="hidden" name="id" value="<?= $id ?>">
                <div class="botones-centrados mt-4">
                    <button type="submit" class="btn btn-danger boton-accion">Sí, eliminar</button>
                    <a href="listar.php" class="btn btn-secondary boton-accion">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
    </body>
    </html>

    <?php
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        $_SESSION['flash'] = [
            'tipo' => 'danger',
            'mensaje' => '⚠️ ID inválido al intentar eliminar.'
        ];
        header("Location: listar.php");
        exit();
    }

    // Obtener nombre antes de eliminar
    $stmt = $conexion->prepare("SELECT nombre FROM dispositivos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        $_SESSION['flash'] = [
            'tipo' => 'danger',
            'mensaje' => '❌ El dispositivo ya fue eliminado o no existe.'
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
}
?>
