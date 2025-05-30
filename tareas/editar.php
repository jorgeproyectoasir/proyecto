<?php
include '../includes/auth.php';
include '../conexion.php';
require_once '../includes/log.php';

// Obtener id de tarea a editar
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: listar.php");
    exit();
}

// Obtener tarea y datos relacionados
$sql = "SELECT * FROM tareas WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$tarea = $result->fetch_assoc();

if (!$tarea) {
    header("Location: listar.php");
    exit();
}

// Verificación de permisos
$rol_actual = $_SESSION['rol'];
$roles_nivel = ['admin' => 1, 'tecnico' => 2, 'usuario' => 3];
$nivel_usuario = $roles_nivel[$rol_actual] ?? 99;

$puede_editar = false;
if ($nivel_usuario == 1) {
    $puede_editar = true;
} elseif ($nivel_usuario == 2) {
    if ($tarea['tecnico_id'] == $_SESSION['usuario_id'] || $tarea['creador_id'] == $_SESSION['usuario_id']) {
        $puede_editar = true;
    }
} elseif ($nivel_usuario == 3) {
    if ($tarea['creador_id'] == $_SESSION['usuario_id']) {
        $puede_editar = true;
    }
}

if (!$puede_editar) {
    echo "No tienes permisos para editar esta tarea.";
    include '../includes/footer.php';
    exit();
}

// Obtener lista de técnicos para el select
$tecnicos = $conexion->query("SELECT id, nombre FROM usuarios WHERE rol_id = (SELECT id FROM roles WHERE nombre = 'tecnico')");

// Procesar POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $fecha = $_POST['programada_para'];
    $tecnico_id = $_POST['tecnico_id'];

    if (!empty($titulo) && !empty($fecha) && !empty($tecnico_id)) {
        $stmt = $conexion->prepare("UPDATE tareas SET titulo = ?, descripcion = ?, programada_para = ?, tecnico_id = ? WHERE id = ?");
        $stmt->bind_param("sssii", $titulo, $descripcion, $fecha, $tecnico_id, $id);
        $stmt->execute();

        registrar_log($conexion, $_SESSION["usuario_id"], "Editó tarea ID $id: '$titulo'");

        // Redirigir con mensaje de éxito
        header("Location: listar.php?editada=1");
        exit();
    } else {
        $mensaje = "Por favor, rellena todos los campos obligatorios.";
    }
}
?>

<?php include '../includes/header.php'; ?>
<h2>Editar tarea</h2>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-danger mt-3">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<form method="POST" class="form-bordered mt-3">
    <label>Título:</label><br>
    <input type="text" name="titulo" value="<?= htmlspecialchars($tarea['titulo']) ?>" required><br><br>

    <label>Descripción:</label><br>
    <textarea name="descripcion"><?= htmlspecialchars($tarea['descripcion']) ?></textarea><br><br>

    <label>Programada para:</label><br>
    <input type="date" name="programada_para" value="<?= htmlspecialchars($tarea['programada_para']) ?>" required><br><br>

    <label>Asignar a técnico:</label><br>
    <select name="tecnico_id" required>
        <option value="">-- Selecciona técnico --</option>
        <?php while ($tec = $tecnicos->fetch_assoc()): ?>
            <option value="<?= $tec['id'] ?>" <?= $tec['id'] == $tarea['tecnico_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($tec['nombre']) ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>

    <button type="submit" class="btn btn-primary">Guardar cambios</button>
    <a href="listar.php" class="btn btn-secondary ms-2">Cancelar</a>
</form>

<?php include '../includes/footer.php'; ?>

