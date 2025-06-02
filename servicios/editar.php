<?php
include '../includes/auth.php';
include '../includes/header.php';
include '../conexion.php';

if ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'tecnico') {
    echo "Acceso denegado.";
    include '../includes/footer.php';
    exit();
}

$id = intval($_GET['id'] ?? 0);

// Guardar cambios
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $estado = $_POST['estado'];
    $dispositivo = $_POST['dispositivo'];

    $stmt = $conexion->prepare("UPDATE servicios SET nombre=?, tipo=?, estado=?, dispositivo_id=? WHERE id=?");
    $stmt->bind_param("sssii", $nombre, $tipo, $estado, $dispositivo, $id);
    $stmt->execute();

    header("Location: listar.php?msg=Servicio actualizado correctamente.");
    exit();
}

// Obtener datos del servicio
$stmt = $conexion->prepare("SELECT * FROM servicios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$servicio = $stmt->get_result()->fetch_assoc();

// Obtener lista de dispositivos
$dispositivos = $conexion->query("SELECT id, nombre FROM dispositivos");
?>

<!-- ✅ ESTILO EMBEBIDO -->
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

    label {
        font-weight: bold;
        display: block;
        margin-bottom: 6px;
    }

    .form-control {
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
</style>

<!-- ✅ FORMULARIO -->
<div class="contenido-flex">
<div class="panel-container">

    <h2 class="titulos">Editar servicio</h2>

    <form method="POST">
        <div class="mb-3">
            <label>Nombre:</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($servicio['nombre']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Tipo:</label>
            <input type="text" name="tipo" class="form-control" value="<?= htmlspecialchars($servicio['tipo']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Estado:</label>
            <select name="estado" class="form-control">
                <option value="activo" <?= $servicio['estado'] == 'activo' ? 'selected' : '' ?>>Activo</option>
                <option value="inactivo" <?= $servicio['estado'] == 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                <option value="error" <?= $servicio['estado'] == 'error' ? 'selected' : '' ?>>Error</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Dispositivo asociado:</label>
            <select name="dispositivo" class="form-control">
                <?php while ($d = $dispositivos->fetch_assoc()): ?>
                    <option value="<?= $d['id'] ?>" <?= $d['id'] == $servicio['dispositivo_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d['nombre']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="botones-centrados">
            <button type="submit" class="btn btn-success boton-accion">Actualizar</button>
            <a href="listar.php" class="btn btn-secondary boton-accion">Cancelar</a>
        </div>
    </form>

</div>
<?php include '../includes/aside.php'; ?>
</div>
<?php include '../includes/footer.php'; ?>
