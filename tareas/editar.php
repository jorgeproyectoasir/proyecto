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
    echo "<div class='container mt-4 alert alert-danger'>No tienes permisos para editar esta tarea.</div>";
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

        header("Location: listar.php?editada=1");
        exit();
    } else {
        $mensaje = "Por favor, rellena todos los campos obligatorios.";
    }
}
?>

<?php include '../includes/header.php'; ?>
<style>
    html, body {
        margin: 0;
        padding: 0;
        background-color: #B0D0FF; /* Fondo azul claro global */
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
</style>

<div class="container mt-4">
    <h2 class="titulos">Editar tarea</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="POST" class="mx-auto bg-light p-4 border rounded" style="max-width: 600px; width: 100%;">
        <div class="mb-3">
            <label for="titulo" class="form-label">Título:</label>
            <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($tarea['titulo']) ?>" class="form-control w-100" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción:</label>
            <textarea id="descripcion" name="descripcion" class="form-control w-100" rows="4"><?= htmlspecialchars($tarea['descripcion']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="programada_para" class="form-label">Programada para:</label>
            <input type="date" id="programada_para" name="programada_para" value="<?= htmlspecialchars($tarea['programada_para']) ?>" class="form-control w-100" required>
        </div>

        <div class="mb-4">
            <label for="tecnico_id" class="form-label">Asignar a técnico:</label>
            <select id="tecnico_id" name="tecnico_id" class="form-select w-100" required>
                <option value="">-- Selecciona técnico --</option>
                <?php while ($tec = $tecnicos->fetch_assoc()): ?>
                    <option value="<?= $tec['id'] ?>" <?= $tec['id'] == $tarea['tecnico_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tec['nombre']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="botones-centrados">
            <button type="submit" class="btn btn-success" style='font-weight:bold'>Guardar cambios</button>
            <a href="listar.php" class="btn btn-secondary" style='font-weight:bold'>Cancelar</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>

