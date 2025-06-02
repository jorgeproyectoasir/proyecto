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

<style>
/* Estilo inspirado en usuarios/registro.php */
body {
        margin: 0;
        padding: 0;
        background-color: #B0D0FF;
        font-family: Arial, sans-serif;
    }
.formulario {
    max-width: 450px;
    margin: 0 auto;
    background-color: #fefefe;
    padding: 25px 30px;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.formulario label {
    font-weight: 600;
    margin-top: 15px;
    display: block;
    color: #333;
}

.formulario .form-control {
    width: 100%;
    padding: 10px 12px;
    margin-top: 6px;
    border: 1px solid #bbb;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.formulario .form-control:focus {
    border-color: #004085;
    outline: none;
    box-shadow: 0 0 5px rgba(0, 64, 133, 0.5);
}

.btn {
    background-color: #198754;
    color: white;
    font-weight: 600;
    border: none;
    padding: 12px 20px;
    border-radius: 6px;
    margin-top: 20px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #136643;
    color: white;
}

.btn-secondary {
    background-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #565e64;
}

.flex-row {
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert {
    max-width: 450px;
    margin: 20px auto 0;
    padding: 15px;
    border-radius: 6px;
    text-align: center;
    font-weight: 600;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
}
</style>

<div class="contenido-flex">
    <div class="panel-container">

        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipo_mensaje === 'success' ? 'success' : 'danger' ?>"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <form method="POST" class="formulario" novalidate>
		<h2>Mi Perfil</h2>
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" class="form-control" value="<?= htmlspecialchars($nombre_actual) ?>" required>

            <label for="email">Correo:</label>
            <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($email_actual) ?>" required>

            <label for="password">Nueva contraseña (opcional):</label>
            <div class="flex-row">
                <input type="password" name="password" id="password" class="form-control">
                <button type="button" id="togglePassword" class="btn btn-secondary btn-sm" style="padding: 6px 12px;">Mostrar</button>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 25px;">
                <button type="submit" class="btn">Guardar cambios</button>
                <a href="../panel.php" class="btn btn-secondary" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">Volver</a>
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
