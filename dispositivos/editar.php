<?php
include '../includes/header.php';
include '../includes/auth.php';
include '../conexion.php';

if (!isset($_GET['id'])) {
    header('Location: listar.php');
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM dispositivos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$dispositivo = $resultado->fetch_assoc();

if (!$dispositivo) {
    header('Location: listar.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $estado = $_POST['estado'];
    $ubicacion = $_POST['ubicacion'];

    $sql_update = "UPDATE dispositivos SET nombre = ?, tipo = ?, estado = ?, ubicacion = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssi", $nombre, $tipo, $estado, $ubicacion, $id);

    if ($stmt_update->execute()) {
        header("Location: listar.php");
        exit();
    } else {
        echo "Error al actualizar el dispositivo.";
    }
}
?>

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

<div class="contenido-flex">
    <div class="panel-container">
        <h1 class="titulos">Editar dispositivo</h1>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($dispositivo['nombre']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="tipo">Tipo:</label>
                <input type="text" name="tipo" id="tipo" value="<?= htmlspecialchars($dispositivo['tipo']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="estado">Estado:</label>
                <input type="text" name="estado" id="estado" value="<?= htmlspecialchars($dispositivo['estado']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="ubicacion">Ubicación:</label>
                <input type="text" name="ubicacion" id="ubicacion" value="<?= htmlspecialchars($dispositivo['ubicacion']) ?>" class="form-control" required>
            </div>

            <div class="botones-centrados">
                <button type="submit" class="boton-accion btn-success">Guardar cambios</button>
                <a href="listar.php" class="boton-accion btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <?php include '../includes/aside.php'; ?>
</div>

<?php include '../includes/footer.php'; ?>

