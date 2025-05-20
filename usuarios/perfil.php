<?php
include '../includes/auth.php';
include '../includes/header.php';
include '../conexion.php';

$mensaje = "";
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
        // Actualizar nombre y correo
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
        }

        $stmt->close();
    } else {
        $mensaje = "Nombre y correo no pueden estar vacíos.";
    }
}
?>

<h2>Mi Perfil</h2>
<?php if ($mensaje): ?>
    <div class="alert <?= strpos($mensaje, 'correctamente') !== false ? 'alert-success' : 'alert-danger' ?>"><?= $mensaje ?></div>
<?php endif; ?>

<form method="POST">
    <label>Nombre:</label>
    <input type="text" name="nombre" value="<?= htmlspecialchars($nombre_actual) ?>" required><br><br>

    <label>Correo:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($email_actual) ?>" required><br><br>

    <label>Nueva contraseña (opcional):</label>
    <input type="password" name="password" id="password"><br><br>

    <button type="submit" class="btn btn-primary">Guardar cambios</button>
    <a href="../panel.php" class="btn btn-secondary">Volver</a>
</form>

<?php include '../includes/footer.php'; ?>

