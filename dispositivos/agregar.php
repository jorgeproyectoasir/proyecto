<?php
include '../includes/auth.php';
include '../includes/header.php';
echo "<style>
    html, body {
        margin: 0;
        padding: 0;
        background-color: #B0D0FF;
    }

    .contenido-flex {
        display: flex;
        align-items: flex-start;
        gap: 30px;
        padding: 20px;
        flex-wrap: wrap;
    }

    .panel-container {
        flex: 1;
        min-width: 300px;
    }

    .aside-estandar {
        width: 300px;
        max-width: 100%;
        flex-shrink: 0;
        background-color: #f9f9f9;
        padding: 20px;
        font-size: 25px;
        border-radius: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .titulos {
        text-align: center;
        font-size: 3.5em;
        font-weight: bold;
        margin-bottom: 30px;
    }

    .formulario {
        background-color: #fff;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        margin: 0 auto;
    }

    .form-label {
        font-weight: bold;
        font-size: 1.2em;
    }

    .form-control, .form-select {
        font-size: 1.1em;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #ccc;
        width: 100%;
    }

    .mb-3 {
        margin-bottom: 20px;
    }

    .botones-centrados {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
        margin-top: 30px;
        flex-wrap: wrap;
    }

    .boton-accion {
        min-width: 180px;
        font-weight: bold;
        font-size: 1.1em;
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

    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }

</style>";

require_once '../includes/log.php';
include '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $ip = trim($_POST['ip']);
    $tipo = trim($_POST['tipo']);
    $estado = trim($_POST['estado']);
    $responsable = intval($_POST['responsable']);

    $stmt = $conexion->prepare("INSERT INTO dispositivos (nombre, ip, tipo, estado, responsable) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $nombre, $ip, $tipo, $estado, $responsable);
    $stmt->execute();

    $_SESSION['mensaje_exito'] = "Dispositivo agregado correctamente.";
    header("Location: listar.php");
    exit;
}

$resultadoUsuarios = $conexion->query("SELECT id, nombre FROM usuarios ORDER BY nombre ASC");
?>

<div class="contenido-flex">
    <div class="panel-container">
        <h2 class="titulos">Agregar nuevo dispositivo</h2>

        <?php if (!empty($_SESSION['mensaje_error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['mensaje_error'] ?></div>
            <?php unset($_SESSION['mensaje_error']); ?>
        <?php endif; ?>

        <form action="agregar.php" method="POST" class="formulario" style="font-size: 1.2em;">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="ip" class="form-label">IP:</label>
                <input type="text" name="ip" id="ip" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo:</label>
                <select name="tipo" id="tipo" class="form-select" required>
                    <option value="">-- Selecciona --</option>
                    <option value="Router">Router</option>
                    <option value="Switch">Switch</option>
                    <option value="Servidor">Servidor</option>
                    <option value="PC">PC</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="estado" class="form-label">Estado:</label>
                <select name="estado" id="estado" class="form-select" required>
                    <option value="">-- Selecciona --</option>
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                    <option value="En mantenimiento">En mantenimiento</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="responsable" class="form-label">Responsable:</label>
                <select name="responsable" id="responsable" class="form-select">
                    <option value="">-- Sin asignar --</option>
                    <?php while ($usuario = $resultadoUsuarios->fetch_assoc()): ?>
                        <option value="<?= $usuario['id'] ?>"><?= htmlspecialchars($usuario['nombre']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="botones-centrados">
                <button type="submit" class="btn btn-success boton-accion">Guardar</button>
                <a href="listar.php" class="btn btn-secondary boton-accion">Volver al listado</a>
            </div>
        </form>
    </div>

    <?php include_once __DIR__ . '/../includes/aside.php'; ?>
</div>

<?php include '../includes/footer.php'; ?>

