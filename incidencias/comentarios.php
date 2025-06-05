<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../includes/auth.php';
include '../includes/header.php';
require_once '../includes/log.php';
include '../conexion.php';

echo "<style>
    html, body {
        margin: 0;
        padding: 0;
        background-color: #B0D0FF;
    }
    .alert {
        margin: 10px auto;
        width: fit-content;
        padding: 10px 20px;
        background-color: #ffe6e6;
        color: #b30000;
        border-radius: 6px;
        text-align: center;
    }
</style>";

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];
$incidencia_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($incidencia_id <= 0) {
    echo "<p class='alert alert-danger'>ID de incidencia no válido.</p>";
    include '../includes/footer.php';
    exit();
}

$stmt = $conexion->prepare("SELECT i.*, u.nombre AS tecnico FROM incidencias i LEFT JOIN usuarios u ON i.tecnico_id = u.id WHERE i.id = ?");
$stmt->bind_param("i", $incidencia_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "<p class='alert alert-danger'>Incidencia no encontrada.</p>";
    include '../includes/footer.php';
    exit();
}

$incidencia = $resultado->fetch_assoc();

// Cualquier usuario autenticado puede ver la incidencia
if (!isset($_SESSION['usuario_id'])) {
    echo "<p class='alert alert-danger'>Debes iniciar sesión para ver esta página.</p>";
    include '../includes/footer.php';
    exit();
}

// Procesar nuevo comentario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['mensaje'])) {
    $mensaje = trim($_POST['mensaje']);
    if (!empty($mensaje)) {
        $stmt = $conexion->prepare("INSERT INTO comentarios_incidencias (incidencia_id, usuario_id, mensaje, fecha) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $incidencia_id, $usuario_id, $mensaje);
        $stmt->execute();
        registrar_log($conexion, $usuario_id, "Añadió un comentario a la incidencia ID $incidencia_id");
        header("Location: comentarios.php?id=$incidencia_id&comentado=1");
        exit();
    }
}

// Obtener comentarios
$stmt = $conexion->prepare("SELECT c.*, u.nombre FROM comentarios_incidencias c JOIN usuarios u ON c.usuario_id = u.id WHERE c.incidencia_id = ? ORDER BY c.fecha DESC");
$stmt->bind_param("i", $incidencia_id);
$stmt->execute();
$comentarios = $stmt->get_result();
?>

<style>
form.border {
    max-width: 600px;
    margin: 0 auto 1.5rem auto;
    font-size: 1.3rem;
}
form.border label {
    max-width: 100px;
}
form.border textarea.form-control {
    font-size: 1.3rem;
}
div.border.p-2.mb-2 {
    font-size: 1.3rem;
}
small.text-muted {
    font-size: 1rem;
}
</style>

<h2 class="titulos">Comentarios de la incidencia</h2>

<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title" style="font-size: 1.5rem;"><?= htmlspecialchars($incidencia['descripcion'] ?? 'Sin descripción') ?></h5>
        <p class="card-text" style="font-size: 1.4rem;"><?= htmlspecialchars($incidencia['descripcion'] ?? '') ?></p>
        <p class="card-text" style="font-size: 1.4rem;"><strong>Fecha:</strong> <?= htmlspecialchars($incidencia['fecha'] ?? '') ?></p>
        <p class="card-text" style="font-size: 1.4rem;"><strong>Estado:</strong> <?= htmlspecialchars($incidencia['estado'] ?? '') ?></p>
        <p class="card-text" style="font-size: 1.4rem;"><strong>Técnico asignado:</strong> <?= htmlspecialchars($incidencia['tecnico'] ?? 'No asignado') ?></p>
    </div>
</div>

<?php if (isset($_GET['comentado'])): ?>
    <div class="alert alert-success">Comentario añadido correctamente.</div>
<?php endif; ?>

<h4 class="botones-centrados" style="font-size: 1.8rem;">Agregar un comentario</h4>
<form method="POST" class="border p-3 mb-3 bg-light rounded">
    <div class="mb-3">
        <textarea name="mensaje" class="form-control" rows="4" required></textarea>
    </div>
    <div class="botones-centrados">
        <button type="submit" class="boton-accion">Enviar</button>
    </div>
</form>

<hr>

<h4 class="botones-centrados" style="font-size: 1.8rem;">Historial de comentarios</h4>
<?php if ($comentarios->num_rows > 0): ?>
    <?php while ($c = $comentarios->fetch_assoc()): ?>
        <div class="border p-2 mb-2">
	    <p><strong><?= htmlspecialchars($c['nombre']) ?>:</strong> <?= nl2br(htmlspecialchars($c['mensaje'])) ?></p>
            <small class="text-muted"><?= htmlspecialchars($c['fecha']) ?></small>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No hay comentarios todavía.</p>
<?php endif; ?>

<div class="botones-centrados">
    <a href="listar.php" class="boton-accion" style="background-color: #6c757d;">Volver al listado</a>
</div>

<?php include '../includes/footer.php'; ?>

