<?php
include '../includes/auth.php';
include '../includes/header.php';
require_once '../includes/log.php';

if (!in_array($_SESSION['rol'], ['admin', 'tecnico', 'usuario'])) {
    echo "Acceso denegado.";
    include '../includes/footer.php';
    exit();
}


include '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $descripcion = $_POST['descripcion'];
    $tipo = $_POST['tipo'];
    $dispositivo = $_POST['dispositivo'];
    $usuario_id = $_SESSION["usuario_id"]; // usuario logueado

    $stmt = $conexion->prepare("INSERT INTO incidencias (descripcion, tipo, dispositivo_id, usuario_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $descripcion, $tipo, $dispositivo, $usuario_id);
    $stmt->execute();

    echo "<p class='text-success'>Incidencia registrada correctamente.</p>";
    echo "<a href='listar.php' class='btn btn-primary'>Volver al listado</a>";
    include '../includes/footer.php';
    exit();
}

$dispositivos = $conexion->query("SELECT id, nombre FROM dispositivos");
registrar_log($conexion, $_SESSION["usuario_id"], 'Reportó incidencia "' . $descripcion . '"');
?>

<h2>Crear nueva incidencia</h2>

<form method="POST">
    <div class="mb-3">
        <label>Descripción:</label>
        <textarea name="descripcion" class="form-control" required></textarea>
    </div>
    <div class="mb-3">
        <label>Tipo:</label>
        <select name="tipo" class="form-control" required>
            <option value="error">Error</option>
            <option value="mantenimiento">Mantenimiento</option>
            <option value="aviso">Aviso</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Dispositivo afectado:</label>
        <select name="dispositivo" class="form-control" required>
            <?php while ($d = $dispositivos->fetch_assoc()): ?>
                <option value="<?= $d['id'] ?>"><?= $d['nombre'] ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Guardar</button>
</form>

<a href="listar.php" class="btn btn-secondary mt-3">Cancelar</a>

<?php include '../includes/footer.php'; ?>

