<?php
include '../includes/auth.php';
include '../includes/header.php';
require_once '../includes/log.php';
if ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'tecnico') {
    echo "Acceso denegado.";
    include '../includes/footer.php';
    exit();
}

include '../conexion.php';

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $ip = trim($_POST['ip']);

    if (!preg_match('/^\d{1,3}(\.\d{1,3}){3}$/', $ip)) {
       echo "<p class='text-danger'>IP inválida. Debe tener el formato correcto, como 192.168.0.1</p>";
       include '../includes/footer.php';
       exit();
    }

    $tipo = $_POST['tipo'];
    $estado = $_POST['estado'];
    $responsable = $_POST['responsable'];

    $stmt = $conexion->prepare("INSERT INTO dispositivos (nombre, ip, tipo, estado, responsable) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $nombre, $ip, $tipo, $estado, $responsable);
    $stmt->execute();
    
    registrar_log($conexion, $_SESSION["usuario_id"], 'Agregó dispositivo ' . $nombre);
    echo "<p class='text-success'>Dispositivo registrado correctamente.</p>";
    echo "<a href='listar.php' class='btn btn-primary'>Volver al listado</a>";

    include '../includes/footer.php';
    exit();
}

// Obtener usuarios responsables
$res = $conexion->query("SELECT id, nombre FROM usuarios");
?>

<h2>Registrar nuevo dispositivo</h2>

<form method="POST">
    <div class="mb-3">
        <label>Nombre del dispositivo:</label>
        <input type="text" name="nombre" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>IP:</label>
        <input type="text" name="ip" id="ip" required pattern="\d{1,3}(\.\d{1,3}){3}" title="Debe tener el formato: 192.168.0.1">
    </div>
    <div class="mb-3">
        <label>Tipo:</label>
        <input type="text" name="tipo" class="form-control" placeholder="Servidor, Switch..." required>
    </div>
    <div class="mb-3">
        <label>Estado:</label>
        <select name="estado" class="form-control">
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
            <option value="mantenimiento">Mantenimiento</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Responsable:</label>
        <select name="responsable" class="form-control">
            <?php while ($fila = $res->fetch_assoc()): ?>
                <option value="<?= $fila['id'] ?>"><?= $fila['nombre'] ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Guardar</button>
</form>

<a href="listar.php" class="btn btn-secondary mt-3">Cancelar</a>

<?php include '../includes/footer.php'; ?>

