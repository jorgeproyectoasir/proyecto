<?php
include '../includes/auth.php';
include '../conexion.php';
require_once '../includes/log.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $tipo = trim($_POST['tipo']);
    $estado = $_POST['estado'] ?? 'activo';
    $dispositivo_id = $_POST['dispositivo'] ?: null;
    $descripcion = trim($_POST['descripcion']);

    if (empty($nombre)) {
        $mensaje = "❌ El nombre del servicio es obligatorio.";
    } else {
        $stmt = $conexion->prepare(
            "INSERT INTO servicios (nombre, tipo, estado, dispositivo_id, descripcion) VALUES (?, ?, ?, ?, ?)"
        );
        // dispositivo_id puede ser null, usamos bind_param con tipos correspondientes
        $stmt->bind_param("sssis", $nombre, $tipo, $estado, $dispositivo_id, $descripcion);
        $stmt->execute();

        registrar_log($conexion, $_SESSION["usuario_id"], "Creó un nuevo servicio: $nombre");
        header("Location: listar.php?msg=Servicio creado correctamente.");
        exit();
    }
}

// Obtener dispositivos para el select
$dispositivos = $conexion->query("SELECT id, nombre FROM dispositivos ORDER BY nombre");
?>

<?php include '../includes/header.php'; ?>

<style>
    	html, body {
        margin: 0;
        padding: 0;
        background-color: #B0D0FF;
    }

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

    .form-control, .form-select, textarea {
        width: 100%;
        padding: 10px;
        font-size: 1em;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    textarea {
        resize: vertical;
        min-height: 80px;
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
    <h2 class="titulos text-center">Crear nuevo servicio</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="alert"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label" for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required maxlength="100">
        </div>

        <div class="mb-3">
            <label class="form-label" for="tipo">Tipo:</label>
            <input type="text" name="tipo" id="tipo" class="form-control" maxlength="50" placeholder="Opcional">
        </div>

        <div class="mb-3">
            <label class="form-label" for="estado">Estado:</label>
            <select name="estado" id="estado" class="form-select" required>
                <option value="activo" selected>Activo</option>
                <option value="inactivo">Inactivo</option>
                <option value="error">Error</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label" for="dispositivo">Dispositivo asociado:</label>
            <select name="dispositivo" id="dispositivo" class="form-select">
                <option value="">-- Ninguno --</option>
                <?php while ($row = $dispositivos->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label" for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion" class="form-control" placeholder="Opcional"></textarea>
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

