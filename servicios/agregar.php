<?php
include '../includes/auth.php';
include '../conexion.php';
require_once '../includes/log.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descripcion = trim($_POST['descripcion']);
    $estado = $_POST['estado'] ?? 'abierta';
    $dispositivo_id = $_POST['dispositivo'];
    $tipo = $_POST['tipo'];
    $usuario_id = $_POST['usuario'];

    if (empty($descripcion)) {
        $mensaje = "❌ La descripción es obligatoria.";
    } else {
        $stmt = $conexion->prepare(
            "INSERT INTO incidencias (descripcion, estado, dispositivo_id, tipo, usuario_id) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssisi", $descripcion, $estado, $dispositivo_id, $tipo, $usuario_id);
        $stmt->execute();

        registrar_log($conexion, $_SESSION["usuario_id"], "Creó una nueva incidencia: $descripcion");
        header("Location: listar.php?msg=Incidencia creada correctamente.");
        exit();
    }
}

// Obtener lista de dispositivos y usuarios para los select
$dispositivos = $conexion->query("SELECT id, nombre FROM dispositivos");
$usuarios = $conexion->query("SELECT id, nombre FROM usuarios");
?>

<?php include '../includes/header.php'; ?>

<style>
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
    <h2 class="titulos text-center">Crear nueva incidencia</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="alert"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Descripción:</label>
            <input type="text" name="descripcion" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Estado:</label>
            <select name="estado" class="form-select" required>
                <option value="abierta">Abierta</option>
                <option value="cerrada">Cerrada</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Dispositivo:</label>
            <select name="dispositivo" class="form-select" required>
                <?php while ($row = $dispositivos->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo:</label>
            <select name="tipo" class="form-select" required>
                <option value="error">Error</option>
                <option value="aviso">Aviso</option>
                <option value="mantenimiento">Mantenimiento</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Usuario asignado:</label>
            <select name="usuario" class="form-select" required>
                <?php while ($row = $usuarios->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
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

