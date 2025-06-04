<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../includes/auth.php';
include '../conexion.php';
require_once '../includes/log.php';

$mensaje = "";

// Comprobar que llega el ID por GET y es válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: listar.php?msg=ID de tarea no válido.");
    exit();
}

$tarea_id = (int)$_GET['id'];

// Cargar datos de la tarea para mostrar en el formulario
$stmt = $conexion->prepare("SELECT titulo, descripcion, programada_para, estado, tecnico_id FROM tareas WHERE id = ?");
$stmt->bind_param("i", $tarea_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header("Location: listar.php?msg=Tarea no encontrada.");
    exit();
}

$tarea = $resultado->fetch_assoc();

// Cargar lista de técnicos para el select
$tecnicos = $conexion->query("
    SELECT usuarios.id, usuarios.nombre
    FROM usuarios
    JOIN roles ON usuarios.rol_id = roles.id
    WHERE roles.nombre = 'tecnico'
");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $fecha = $_POST['fecha'];
    $estado = $_POST['estado'] ?? 'pendiente';
    $tecnico_id = $_POST['tecnico'] ?? null;

    if (empty($titulo) || empty($descripcion) || empty($fecha) || empty($tecnico_id)) {
        $mensaje = "❌ Todos los campos son obligatorios.";
    } else {
        $stmt = $conexion->prepare(
            "UPDATE tareas 
             SET titulo = ?, descripcion = ?, programada_para = ?, estado = ?, tecnico_id = ?
             WHERE id = ?"
        );
        $stmt->bind_param("ssssii", $titulo, $descripcion, $fecha, $estado, $tecnico_id, $tarea_id);
        $stmt->execute();

        if ($stmt->affected_rows >= 0) { // >=0 porque puede que no cambie nada pero se considere exitoso
            registrar_log($conexion, $_SESSION['usuario_id'], "Editó la tarea ID $tarea_id: $titulo");
            header("Location: listar.php?msg=Tarea actualizada correctamente.");
            exit();
        } else {
            $mensaje = "❌ Error al actualizar la tarea.";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<style>
/* ... puedes copiar el mismo CSS que tienes en el archivo listar.php o externalizarlo */
</style>

<div class="contenido-flex">
<div class="panel-container">
    <h2 class="titulos text-center">Editar tarea</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="alert"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Título:</label>
            <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($tarea['titulo']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción:</label>
            <input type="text" name="descripcion" class="form-control" value="<?= htmlspecialchars($tarea['descripcion']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha límite:</label>
            <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($tarea['programada_para']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Estado:</label>
            <select name="estado" class="form-select" required>
                <option value="pendiente" <?= $tarea['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                <option value="completada" <?= $tarea['estado'] === 'completada' ? 'selected' : '' ?>>Completada</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Técnico asignado:</label>
            <select name="tecnico" class="form-select" required>
                <?php while ($row = $tecnicos->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>" <?= $row['id'] == $tarea['tecnico_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['nombre']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="botones-centrados">
            <button type="submit" class="btn btn-success boton-accion">Actualizar</button>
            <a href="listar.php" class="btn btn-secondary boton-accion">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../includes/aside.php'; ?>
</div>

<?php include '../includes/footer.php'; ?>
