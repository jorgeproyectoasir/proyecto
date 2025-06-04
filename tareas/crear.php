<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../includes/auth.php';
include '../conexion.php';
require_once '../includes/log.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $fecha = $_POST['fecha'] ?? '';
    $estado = $_POST['estado'] ?? 'pendiente';
    $tecnico_id = isset($_POST['tecnico']) ? (int)$_POST['tecnico'] : null;
    $creador_id = $_SESSION['usuario_id'] ?? 0;

    if ($titulo === '' || $descripcion === '' || $fecha === '' || $tecnico_id === null) {
        $mensaje = "❌ Todos los campos son obligatorios.";
    } else {
        $stmt = $conexion->prepare(
            "INSERT INTO tareas (titulo, descripcion, programada_para, estado, tecnico_id, creador_id)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        if ($stmt === false) {
            $mensaje = "❌ Error en la preparación de la consulta: " . $conexion->error;
        } else {
            $stmt->bind_param("ssssii", $titulo, $descripcion, $fecha, $estado, $tecnico_id, $creador_id);
            $exec = $stmt->execute();
            if ($exec) {
                registrar_log($conexion, $creador_id, "Creó una nueva tarea: $titulo");
                header("Location: listar.php?msg=" . urlencode("Tarea creada correctamente."));
                exit();
            } else {
                $mensaje = "❌ Error al crear la tarea: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Consulta para obtener los técnicos
$tecnicos = $conexion->query("
    SELECT usuarios.id, usuarios.nombre
    FROM usuarios
    JOIN roles ON usuarios.rol_id = roles.id
    WHERE roles.nombre = 'tecnico'
");
if (!$tecnicos) {
    die("Error al obtener técnicos: " . $conexion->error);
}
?>

<?php include '../includes/header.php'; ?>

<style>
/* Tus estilos aquí */
.panel-container {
    max-width: 700px;
    margin: 30px auto;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    padding: 30px;
}
.form-label {
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
.alert {
    margin: 10px auto;
    width: fit-content;
    padding: 10px 20px;
    background-color: #ffe6e6;
    color: #b30000;
    border-radius: 6px;
    text-align: center;
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
</style>

<div class="contenido-flex">
<div class="panel-container">
    <h2 class="titulos text-center">Crear nueva tarea</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="alert"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label" for="titulo">Título:</label>
            <input type="text" name="titulo" id="titulo" class="form-control" required value="<?= isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : '' ?>">
        </div>

        <div class="mb-3">
            <label class="form-label" for="descripcion">Descripción:</label>
            <input type="text" name="descripcion" id="descripcion" class="form-control" required value="<?= isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : '' ?>">
        </div>

        <div class="mb-3">
            <label class="form-label" for="fecha">Fecha límite:</label>
            <input type="date" name="fecha" id="fecha" class="form-control" required value="<?= isset($_POST['fecha']) ? htmlspecialchars($_POST['fecha']) : '' ?>">
        </div>

        <div class="mb-3">
            <label class="form-label" for="estado">Estado:</label>
            <select name="estado" id="estado" class="form-select" required>
                <option value="pendiente" <?= (isset($_POST['estado']) && $_POST['estado'] === 'pendiente') ? 'selected' : '' ?>>Pendiente</option>
                <option value="completada" <?= (isset($_POST['estado']) && $_POST['estado'] === 'completada') ? 'selected' : '' ?>>Completada</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label" for="tecnico">Técnico asignado:</label>
            <select name="tecnico" id="tecnico" class="form-select" required>
                <option value="">-- Seleccione técnico --</option>
                <?php while ($row = $tecnicos->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>" <?= (isset($_POST['tecnico']) && $_POST['tecnico'] == $row['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['nombre']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="botones-centrados">
            <button type="submit" class="btn btn-success boton-accion">Crear</button>
            <a href="listar.php" class="btn btn-secondary boton-accion">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../includes/aside.php'; ?>
</div>

<?php include '../includes/footer.php'; ?>
