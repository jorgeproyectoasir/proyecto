<?php
include '../includes/auth.php';
include '../conexion.php';
require_once '../includes/log.php';

$mensaje = "";

// Solo admin puede acceder
if ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'tecnico') {
    header("Location: ../panel.php");
    exit();
}

// Obtener técnicos para el select
$tecnicos = $conexion->query("SELECT id, nombre FROM usuarios WHERE rol_id = (SELECT id FROM roles WHERE nombre = 'tecnico')");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo      = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $fecha       = $_POST['programada_para'];
    $tecnico_id  = $_POST['tecnico_id'];
    $creador_id  = $_SESSION['usuario_id']; // Capturamos el creador desde sesión

    if (!empty($titulo) && !empty($fecha) && !empty($tecnico_id)) {
        $stmt = $conexion->prepare("INSERT INTO tareas (titulo, descripcion, programada_para, tecnico_id, creador_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", $titulo, $descripcion, $fecha, $tecnico_id, $creador_id);
        $stmt->execute();

        registrar_log($conexion, $_SESSION["usuario_id"], "Creó tarea '$titulo' asignada al técnico ID $tecnico_id");
        $mensaje = "Tarea creada correctamente.";
    } else {
        $mensaje = "Rellena todos los campos obligatorios.";
    }
}

?>

<?php include '../includes/header.php'; ?>
<h2>Crear nueva tarea</h2>

<?php if (!empty($mensaje)): ?>
    <div class="alert <?= strpos($mensaje, 'correctamente') !== false ? 'alert-success' : 'alert-danger' ?> mt-3">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<form method="POST" class="mt-3">
    <label>Título:</label><br>
    <input type="text" name="titulo" required><br><br>

    <label>Descripción:</label><br>
    <textarea name="descripcion"></textarea><br><br>

    <label>Programada para:</label><br>
    <input type="date" name="programada_para" required><br><br>

    <label>Asignar a técnico:</label><br>
    <select name="tecnico_id" required>
        <option value="">-- Selecciona técnico --</option>
        <?php while ($tec = $tecnicos->fetch_assoc()): ?>
            <option value="<?= $tec['id'] ?>"><?= htmlspecialchars($tec['nombre']) ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <button type="submit" class="btn btn-primary">Crear tarea</button>
    <a href="listar.php" class="btn btn-secondary ms-2">Volver</a>
</form>

<?php include '../includes/footer.php'; ?>

