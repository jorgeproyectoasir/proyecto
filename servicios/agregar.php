<?php
include '../includes/auth.php';
include '../conexion.php';
require_once '../includes/log.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre      = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $estado      = $_POST['estado'] ?? 'activo';
    $tipo        = trim($_POST['tipo']);

    if (empty($nombre)) {
        $mensaje = "❌ El nombre es obligatorio.";
    } elseif (empty($tipo)) {
        $mensaje = "❌ El tipo es obligatorio.";
    } else {
        $stmt = $conexion->prepare(
            "INSERT INTO servicios (nombre, descripcion, estado, tipo) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $nombre, $descripcion, $estado, $tipo);
        $stmt->execute();

        registrar_log($conexion, $_SESSION["usuario_id"], "Agregó un nuevo servicio: $nombre");
        header("Location: listar.php?msg=Servicio agregado correctamente.");
        exit();
    }
}
?>

<?php include '../includes/header.php'; ?>

<!-- ✅ ESTILOS EMBEBIDOS -->
<style>
    body {
        margin: 0;
        padding: 0;
        background-color: #B0D0FF;
        font-family: Arial, sans-serif;
    }

    .titulos {
        text-align: center;
        margin-top: 20px;
        font-size: 2em;
        color: #333;
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

<!-- ✅ CONTENIDO -->
<div class="contenido-flex">
<div class="panel-container">

    <h2 class="titulos text-center">Agregar Servicio</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="alert"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del servicio:</label>
            <input type="text" id="nombre" name="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción:</label>
            <textarea id="descripcion" name="descripcion" class="form-control" rows="4"></textarea>
        </div>

        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo:</label>
            <input type="text" id="tipo" name="tipo" class="form-control" required placeholder="Ej: mantenimiento">
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado:</label>
            <select id="estado" name="estado" class="form-select">
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
        </div>

        <div class="botones-centrados">
            <button type="submit" class="btn btn-success boton-accion">Guardar</button>
            <a href="listar.php" class="btn btn-secondary boton-accion">Volver a la lista</a>
        </div>
    </form>

</div>
<?php include '../includes/aside.php'; ?>
</div>
<?php include '../includes/footer.php'; ?>
