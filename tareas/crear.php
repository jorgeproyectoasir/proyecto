<?php
include '../includes/auth.php';
include '../conexion.php';
require_once '../includes/log.php';

// Solo admin o técnico pueden acceder
if ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'tecnico') {
    header("Location: ../panel.php");
    exit();
}

// Obtener técnicos para el select
$tecnicos = $conexion->query("SELECT id, nombre FROM usuarios WHERE rol_id = (SELECT id FROM roles WHERE nombre = 'tecnico')");

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo      = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $fecha       = $_POST['programada_para'];
    $tecnico_id  = intval($_POST['tecnico_id']);
    $creador_id  = $_SESSION['usuario_id'];

    if (!empty($titulo) && !empty($fecha) && !empty($tecnico_id)) {
        $stmt = $conexion->prepare("INSERT INTO tareas (titulo, descripcion, programada_para, tecnico_id, creador_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", $titulo, $descripcion, $fecha, $tecnico_id, $creador_id);
        $stmt->execute();

        registrar_log($conexion, $creador_id, "Creó tarea '$titulo' asignada al técnico ID $tecnico_id");

        header("Location: listar.php?creada=1");
        exit();
    } else {
        $error = "Rellena todos los campos obligatorios.";
    }
}
?>

<?php include '../includes/header.php'; ?>
<div class="container mt-4">
    <h2>Crear nueva tarea</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger mt-3">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="mt-4 p-4 border rounded bg-light">
        <div class="mb-3">
            <label for="titulo" class="form-label">Título:</label>
            <input type="text" id="titulo" name="titulo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción:</label>
            <textarea id="descripcion" name="descripcion" class="form-control" rows="4"></textarea>
        </div>

        <div class="mb-3">
            <label for="programada_para" class="form-label">Programada para:</label>
            <input type="date" id="programada_para" name="programada_para" class="form-control" required>
        </div>

        <div class="mb-4">
            <label for="tecnico_id" class="form-label">Asignar a técnico:</label>
            <select id="tecnico_id" name="tecnico_id" class="form-control" required>
                <option value="">-- Selecciona técnico --</option>
                <?php while ($tec = $tecnicos->fetch_assoc()): ?>
                    <option value="<?= $tec['id'] ?>"><?= htmlspecialchars($tec['nombre']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Crear tarea</button>
            <a href="listar.php" class="btn btn-secondary">Volver</a>
        </div>
    </form>
</div>
<?php include '../includes/footer.php'; ?>


