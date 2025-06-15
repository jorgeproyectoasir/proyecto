<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../includes/auth.php';
include '../conexion.php';
require_once '../includes/log.php';

$mensaje = "";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: listar.php?msg=ID de tarea no válido.");
    exit();
}

$tarea_id = (int)$_GET['id'];

$stmt = $conexion->prepare("SELECT titulo, descripcion, programada_para, estado, tecnico_id FROM tareas WHERE id = ?");
$stmt->bind_param("i", $tarea_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header("Location: listar.php?msg=Tarea no encontrada.");
    exit();
}

$tarea = $resultado->fetch_assoc();

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

        if ($stmt->affected_rows >= 0) {
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
    body {
        margin: 0;
        padding: 0;
        background-color: #B0D0FF;
        font-family: Arial, sans-serif;
    }

    .contenido-flex {
        display: flex;
        align-items: flex-start;
    }

    aside {
        width: 250px;
        margin-left: 20px;
    }

    .panel-container {
        max-width: 700px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        padding: 30px;
        margin: 30px auto;
        flex-grow: 1;
    }

    .titulos {
        text-align: center;
        font-size: 2em;
        color: #333;
        margin-bottom: 20px;
    }

    label {
        font-weight: bold;
        display: block;
        margin-bottom: 6px;
    }

    .form-control, .form-select {
        width: 100%;
        padding: 10px;
        font-size: 1em;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    .mb-3 {
        margin-bottom: 20px;
    }

    .botones-centrados {
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    .boton-accion {
        min-width: 160px;
        font-weight: bold;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    .btn-success {
        background-color: #198754;
        color: white;
    }

    .btn-secondary {
        background-color: gray;
        color: white;
    }

    .alert {
        background-color: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 20px;
        text-align: center;
    }
</style>

<div class="contenido-flex">
    <div class="panel-container">
        <h2 class="titulos">Editar tarea</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="alert"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label>Título:</label>
                <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($tarea['titulo']) ?>" required>
            </div>

            <div class="mb-3">
                <label>Descripción:</label>
                <input type="text" name="descripcion" class="form-control" value="<?= htmlspecialchars($tarea['descripcion']) ?>" required>
            </div>

            <div class="mb-3">
                <label>Fecha límite:</label>
                <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($tarea['programada_para']) ?>" required>
            </div>

            <div class="mb-3">
                <label>Estado:</label>
                <select name="estado" class="form-select" required>
                    <option value="pendiente" <?= $tarea['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="completada" <?= $tarea['estado'] === 'completada' ? 'selected' : '' ?>>Completada</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Técnico asignado:</label>
                <select name="tecnico" class="form-select" required>
                    <?php while ($row = $tecnicos->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>" <?= $row['id'] == $tarea['tecnico_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row['nombre']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="botones-centrados">
                <button type="submit" class="boton-accion btn-success">Actualizar</button>
                <a href="listar.php" class="boton-accion btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <?php include '../includes/aside.php'; ?>
</div>

<?php include '../includes/footer.php'; ?>

