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
 html, body {
        margin: 0;
        padding: 0;
        background-color: #B0D0FF; /* Fondo azul claro global */
    }
/* Contenedor principal para formularios y paneles */
.panel-container {
    max-width: 700px;
    margin: 30px auto;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    padding: 30px 40px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Títulos de formularios */
.titulos {
    font-weight: 700;
    font-size: 2em;
    margin-bottom: 25px;
    color: #333;
    text-align: center;
}

/* Etiquetas para inputs */
.form-label {
    font-weight: 600;
    display: block;
    margin-bottom: 8px;
    color: #444;
    user-select: none;
}

/* Inputs, selects y textareas */
.form-control, .form-select, textarea {
    width: 100%;
    padding: 12px 15px;
    font-size: 1em;
    border: 1.8px solid #ccc;
    border-radius: 8px;
    transition: border-color 0.3s ease;
    outline-offset: 2px;
    outline-color: transparent;
}

.form-control:focus, .form-select:focus, textarea:focus {
    border-color: #198754;
    outline-color: #198754;
    box-shadow: 0 0 5px rgba(25, 135, 84, 0.5);
}

/* Textarea específico */
textarea {
    resize: vertical;
    min-height: 100px;
}

/* Contenedor para mensajes de error o alerta */
.alert {
    margin: 15px auto;
    padding: 12px 25px;
    width: fit-content;
    background-color: #ffe6e6;
    color: #b30000;
    border-radius: 8px;
    font-weight: 600;
    text-align: center;
    user-select: none;
}

/* Contenedor flexible para botones */
.botones-centrados {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
    margin-top: 30px;
}

/* Botones base */
.boton-accion {
    min-width: 160px;
    font-weight: 700;
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    user-select: none;
    text-decoration: none; /* Para enlaces tipo botón */
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* Botón verde */
.btn-success {
    background-color: #198754;
    color: white;
}

.btn-success:hover, .btn-success:focus {
    background-color: #146c43;
}

/* Botón gris */
.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover, .btn-secondary:focus {
    background-color: #5a6268;
}
</style>

<div class="contenido-flex">
<div class="panel-container">
    <h2 class="titulos">Crear nueva tarea</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="alert"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
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

