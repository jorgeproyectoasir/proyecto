<?php
include '../includes/auth.php';
include '../includes/header.php';
require_once '../includes/log.php';
session_start();

if ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'tecnico') {
    echo "<p class='alert alert-danger text-center'>❌ Acceso denegado.</p>";
    include '../includes/footer.php';
    exit();
}

include '../conexion.php';

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $ip = trim($_POST['ip']);
    $tipo = trim($_POST['tipo']);
    $estado = $_POST['estado'];
    $responsable = $_POST['responsable'];

    // Validación de la IP
    if (!preg_match('/^\d{1,3}(\.\d{1,3}){3}$/', $ip)) {
        $_SESSION['flash'] = [
            'tipo' => 'danger',
            'mensaje' => '❌ IP inválida. Debe tener el formato correcto, como 192.168.0.1'
        ];
        header("Location: agregar.php");
        exit();
    }

    // Insertar dispositivo en la base de datos
    $stmt = $conexion->prepare("INSERT INTO dispositivos (nombre, ip, tipo, estado, responsable) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $nombre, $ip, $tipo, $estado, $responsable);

    if ($stmt->execute()) {
        registrar_log($conexion, $_SESSION["usuario_id"], "Agregó dispositivo $nombre");

        $_SESSION['flash'] = [
            'tipo' => 'success',
            'mensaje' => "✅ Dispositivo agregado correctamente."
        ];
    } else {
        $_SESSION['flash'] = [
            'tipo' => 'danger',
            'mensaje' => "❌ Error al agregar dispositivo."
        ];
    }

    $stmt->close();
    header("Location: listar.php");
    exit();
}

// Obtener usuarios responsables
$res = $conexion->query("SELECT id, nombre FROM usuarios");
?>

<h2>Registrar nuevo dispositivo</h2>

<!-- Mostrar mensaje flash si existe -->
<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?= $_SESSION['flash']['tipo'] ?> text-center">
        <?= $_SESSION['flash']['mensaje'] ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<form method="POST" action="" class="login-form">
    <div class="mb-3">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="email">Correo:</label>
        <input type="email" id="email" name="email" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="password">Contraseña:</label>
        <div style="display: flex; align-items: center; gap: 10px;">
            <input type="password" id="password" name="password" class="form-control" required>
            <button type="button" id="togglePassword" class="btn">Mostrar</button>
        </div>
    </div>

    <button type="submit" class="btn">Registrarse</button>
</form>


<a href="listar.php" class="btn btn-secondary mt-3">Cancelar</a>

<?php include '../includes/footer.php'; ?>

