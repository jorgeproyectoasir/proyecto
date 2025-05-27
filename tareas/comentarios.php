<?php
include '../includes/auth.php';
include '../includes/header.php';
require_once '../includes/log.php';
include '../conexion.php';

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];
$tarea_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($tarea_id <= 0) {
    echo "<p class='alert alert-danger'>ID de tarea no válido.</p>";
    include '../includes/footer.php';
    exit();
}

// Obtener tarea y verificar permisos
$stmt = $conexion->prepare("SELECT t.*, u.nombre AS tecnico FROM tareas t LEFT JOIN usuarios u ON t.tecnico_id = u.id WHERE t.id = ?");
$stmt->bind_param("i", $tarea_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "<p class='alert alert-danger'>Tarea no encontrada.</p>";
    include '../includes/footer.php';
    exit();
}

$tarea = $resultado->fetch_assoc();

// Verificar permisos
$puede_ver = false;
if ($rol === 'admin' || ($rol === 'tecnico' && $tarea['tecnico_id'] == $usuario_id) || ($rol === 'usuario' && $tarea['creador_id'] == $usuario_id)) {
    $puede_ver = true;
}

if (!$puede_ver) {
    echo "<p class='alert alert-danger'>No tienes permisos para acceder a esta tarea.</p>";
    include '../includes/footer.php';
    exit();
}

// Añadir comentario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['mensaje'])) {
    $mensaje = trim($_POST['mensaje']);
    $stmt = $conexion->prepare("INSERT INTO comentarios_tareas (tarea_id, usuario_id, mensaje) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $tarea_id, $usuario_id, $mensaje);
    $stmt->execute();

    registrar_log($conexion, $usuario_id, "Añadió un comentario a la tarea ID $tarea_id");

    header("Location: comentarios.php?id=$tarea_id&comentado=1");
    exit();
}

// Obtener comentarios
$stmt = $conexion->prepare("SELECT c.*, u.nombre FROM comentarios_tareas c JOIN usuarios u ON c.usuario_id = u.id WHERE c.tarea_id = ?");
$stmt->bind_param("i", $tarea_id);
$stmt->execute();
$comentarios = $stmt->get_result();
?>

<h2>Comentarios de la tarea</h2>

<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title"><?= htmlspecialchars($tarea['titulo']) ?></h5>
        <p class="card-text"><?= htmlspecialchars($tarea['descripcion']) ?></p>
        <p class="card-text"><strong>Programada para:</strong> <?= htmlspecialchars($tarea['programada_para']) ?></p>
        <p class="card-text"><strong>Estado:</strong> <?= htmlspecialchars($tarea['estado']) ?></p>
        <p class="card-text"><strong>Técnico asignado:</strong> <?= htmlspecialchars($tarea['tecnico']) ?></p>
    </div>
</div>

<?php if (isset($_GET['comentado'])): ?>
    <div class="alert alert-success">Comentario añadido correctamente.</div>
<?php endif; ?>

<h4>Agregar un comentario</h4>
<form method="POST" class="border p-3 mb-3 bg-light rounded">
    <div class="mb-3">
        <textarea name="mensaje" class="form-control" rows="4" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Enviar</button>
</form>

<hr>

<h4>Historial de comentarios</h4>
<?php if ($comentarios->num_rows > 0): ?>
    <?php while ($c = $comentarios->fetch_assoc()): ?>
        <div class="border p-2 mb-2">
            <p><strong><?= htmlspecialchars($c['nombre']) ?>:</strong> <?= nl2br(htmlspecialchars($c['mensaje'])) ?></p>
            <small class="text-muted"><?= $c['fecha'] ?></small>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No hay comentarios todavía.</p>
<?php endif; ?>

<a href="listar.php" class="btn btn-secondary mt-3">Volver al listado</a>

<?php include '../includes/footer.php'; ?>

