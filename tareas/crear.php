<?php
include '../includes/auth.php';
include '../conexion.php';
require_once '../includes/log.php';

$mensaje = "";

// Verifica que sea admin
if ($_SESSION['rol'] !== 'admin') {
    header("Location: ../panel.php");
    exit();
}

// Obtener técnicos para el desplegable
$tecnicos = $conexion->query("SELECT id, nombre FROM usuarios WHERE rol_id = (SELECT id FROM roles WHERE nombre = 'tecnico')");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $fecha = $_POST['programada_para'];
    $tecnico_id = $_POST['tecnico_id'];

    if (!empty($titulo) && !empty($fecha) && !empty($tecnico_id)) {
        $stmt = $conexion->prepare("INSERT INTO tareas (titulo, descripcion, programada_para, tecnico_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $titulo, $descripcion, $fecha, $tecnico_id);
        $stmt->execute();

        registrar_log($conexion, $_SESSION['id'], "Creó tarea '$titulo' asignada al técnico ID $tecnico_id");
        $mensaje = "✅ Tarea creada correctamente.";
    } else {
        $mensaje = "⚠️ Rellena todos los campos obligatorios.";
    }
}
?>

<?php include '../includes/header.php'; ?>
<h2>Crear nueva tarea</h2>

<?php if ($mensaje) echo "<p>$mensaje</p>"; ?>

<form method="POST">
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

    <button type="submit">Crear tarea</button>
</form>

<?php include '../includes/footer.php'; ?>

