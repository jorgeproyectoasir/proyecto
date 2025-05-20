<?php
include '../includes/auth.php';
include '../includes/header.php';
require_once '../includes/log.php';
include '../conexion.php';

$incidencia_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

if ($incidencia_id <= 0) {
    echo "<p class='alert alert-danger'>ID de incidencia no válido.</p>";
    include '../includes/footer.php';
    exit();
}

// Verificamos si la incidencia existe
$stmt = $conexion->prepare("SELECT i.*, u.nombre AS autor FROM incidencias i LEFT JOIN usuarios u ON i.usuario_id = u.id WHERE i.id = ?");
$stmt->bind_param("i", $incidencia_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "<p class='alert alert-danger'>Incidencia no encontrada.</p>";
    include '../includes/footer.php';
    exit();
}

$incidencia = $resultado->fetch_assoc();

// Solo permitir acceso a admin, técnico o usuario creador
if ($rol !== 'admin' && $rol !== 'tecnico' && $incidencia['usuario_id'] != $usuario_id) {
    echo "<p class='alert alert-danger'>No tienes permiso para ver esta incidencia.</p>";
    include '../includes/footer.php';
    exit();
}

// Añadir comentario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['mensaje'])) {
    $mensaje = trim($_POST['mensaje']);
    $stmt = $conexion->prepare("INSERT INTO comentarios (incidencia_id, usuario_id, mensaje) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $incidencia_id, $usuario_id, $mensaje);
    $stmt->execute();
    registrar_log($conexion, $usuario_id, "Añadió un comentario a la incidencia ID $incidencia_id");
}

// Obtener comentarios
$stmt = $conexion->prepare("SELECT c.*, u.nombre FROM comentarios c JOIN usuarios u ON c.usuario_id = u.id WHERE c.incidencia_id = ? ORDER BY c.fecha DESC");
$stmt->bind_param("i", $incidencia_id);
$stmt->execute();
$comentarios = $stmt->get_result();
?>

<h2>Comentarios sobre la incidencia #<?= $incidencia['id'] ?></h2>
<p><strong>Descripción:</strong> <?= htmlspecialchars($incidencia['descripcion']) ?></p>

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

