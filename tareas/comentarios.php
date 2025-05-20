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
$stmt = $conexion->prepare("SELECT * FROM tareas WHERE id = ?");
$stmt->bind_param("i", $tarea_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "<p class='alert alert-danger'>Tarea no encontrada.</p>";
    include '../includes/footer.php';
    exit();
}

$tarea = $resultado->fetch_assoc();

// Comprobar permisos
$puede_ver = false;
if ($rol === 'admin') {
    $puede_ver = true;
} elseif ($rol === 'tecnico' && $tarea['tecnico_id'] == $usuario_id) {
    $puede_ver = true;
} elseif ($rol === 'usuario' && $tarea['creador_id'] == $usuario_id) {
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
}

// Obtener comentarios
$stmt = $conexion->prepare("SELECT c.*, u.nombre FROM comentarios_tareas c JOIN usuarios u ON c.usuario_id = u.id WHERE c.tarea_id = ? ORDER BY c.fecha DESC");
$stmt->bind_param("i", $tarea_id);
$stmt->execute();
$comentarios = $stmt->get_result();
?>

<h2>Comentarios de la tarea #<?= $tarea['id'] ?></h2>
<p><strong>Título:</strong> <?= htmlspecialchars($tarea['titulo']) ?></p>

<hr>

<h4>Comentarios</h4>

<?php if ($comentarios->num_rows > 0): ?>
    <?php while ($coment = $comentarios->fetch_assoc()): ?>
        <div class="alert alert-secondary">
            <strong><?= htmlspecialchars($coment['nombre']) ?></strong> (<?= $coment['fecha'] ?>):
            <br><?= nl2br(htmlspecialchars($coment['mensaje'])) ?>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No hay comentarios todavía.</p>
<?php endif; ?>

<hr>

<h4>Nuevo comentario</h4>
<form method="POST">
    <textarea name="mensaje" rows="4" class="form-control" required></textarea>
    <button type="submit" class="btn btn-primary mt-2">Enviar comentario</button>
</form>

<br><a href="listar.php" class="btn btn-secondary mt-3">Volver al listado</a>

<?php include '../includes/footer.php'; ?>

