<?php
include '../includes/auth.php';
include '../includes/header.php';
include '../conexion.php';

$mensaje = "";
$tipo_mensaje = "success";
$usuario_id = $_SESSION['usuario_id'];

// Obtener datos actuales del usuario
$stmt = $conexion->prepare("SELECT nombre, email FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($nombre_actual, $email_actual);
$stmt->fetch();
$stmt->close();

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevo_nombre = trim($_POST["nombre"]);
    $nuevo_email = trim($_POST["email"]);
    $nueva_contra = $_POST["password"];

    if (!empty($nuevo_nombre) && !empty($nuevo_email)) {
        $sql = "UPDATE usuarios SET nombre = ?, email = ?" . (!empty($nueva_contra) ? ", contraseña = ?" : "") . " WHERE id = ?";
        $stmt = $conexion->prepare($sql);

        if (!empty($nueva_contra)) {
            $password_hash = password_hash($nueva_contra, PASSWORD_DEFAULT);
            $stmt->bind_param("sssi", $nuevo_nombre, $nuevo_email, $password_hash, $usuario_id);
        } else {
            $stmt->bind_param("ssi", $nuevo_nombre, $nuevo_email, $usuario_id);
        }

        if ($stmt->execute()) {
            $mensaje = "Perfil actualizado correctamente.";
            $_SESSION['nombre'] = $nuevo_nombre;
        } else {
            $mensaje = "Error al actualizar.";
            $tipo_mensaje = "danger";
        }

        $stmt->close();
    } else {
        $mensaje = "Nombre y correo no pueden estar vacíos.";
        $tipo_mensaje = "danger";
    }
}
?>

<div class="contenido-flex">
    <div class="panel-container">
        <h2>Mi Perfil</h2>

        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipo_mensaje ?> mt-3"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <form method="POST" class="formulario ancho-medio">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" class="form-control" value="<?= htmlspecialchars($nombre_actual) ?>" required>

            <label for="email">Correo:</label>
            <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($email_actual) ?>" required>

            <label for="password">Nueva contraseña (opcional):</label>
            <div style="display: flex; align-items: center; gap: 10px;">
                <input type="password" name="password" id="password" class="form-control">
                <button type="button" id="togglePassword" class="btn btn-outline-secondary btn-sm">Mostrar</button>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="../panel.php" class="btn btn-secondary">Volver</a>
            </div>
        </form>
    </div>

    <?php include_once '../includes/aside.php'; ?>
</div>

<script>
document.getElementById("togglePassword").addEventListener("click", function () {
    const passwordInput = document.getElementById("password");
    const tipo = passwordInput.type === "password" ? "text" : "password";
    passwordInput.type = tipo;
    this.textContent = tipo === "password" ? "Mostrar" : "Ocultar";
});
</script>

<?php include '../includes/footer.php'; ?>
