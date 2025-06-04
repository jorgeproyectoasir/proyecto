<?php
include '../includes/auth.php';
include '../includes/header.php';
include '../conexion.php';

$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
    echo "No has iniciado sesión.";
    include '../includes/footer.php';
    exit();
}

$mensaje = "";
$color = "green";

// Guardar cambios si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $contrasena = trim($_POST['contraseña']);

    // Hash de la contraseña solo si se modifica
    if (!empty($contrasena)) {
        $contrasena_hashed = password_hash($contrasena, PASSWORD_DEFAULT);
        $query = "UPDATE usuarios SET nombre=?, email=?, contraseña=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssi", $nombre, $email, $contrasena_hashed, $usuario_id);
    } else {
        $query = "UPDATE usuarios SET nombre=?, email=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $nombre, $email, $usuario_id);
    }

    if (mysqli_stmt_execute($stmt)) {
        $mensaje = "✅ Perfil actualizado correctamente.";
        $color = "green";
    } else {
        $mensaje = "❌ Error al actualizar el perfil.";
        $color = "red";
    }
}

// Obtener datos actualizados del usuario
$query = "SELECT nombre, email FROM usuarios WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $usuario_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($resultado);
?>

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

    .mensaje {
        text-align: center;
        margin-top: 10px;
        font-weight: bold;
    }
</style>

<div class="contenido-flex">
<div class="panel-container">

    <h2 class="titulos">Mi perfil</h2>

    <?php if ($mensaje): ?>
        <div class="mensaje" style="color: <?= $color ?>"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Nombre:</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Correo:</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Nueva contraseña (opcional):</label>
            <div class="input-group">
                <input type="password" name="contraseña" id="campo-contrasena" class="form-control">
                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">Mostrar</button>
            </div>
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

<script>
    function togglePassword() {
        const campo = document.getElementById('campo-contrasena');
        campo.type = campo.type === 'password' ? 'text' : 'password';
    }
</script>
