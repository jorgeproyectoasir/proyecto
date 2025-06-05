<?php
include '../includes/auth.php';
include '../includes/header.php';
include '../conexion.php';
require_once '../includes/log.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $rol_id = intval($_POST['rol_id']);

    if ($nombre && $email && $password && $rol_id) {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        // Verificar si ya existe ese email
        $check_stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error = "El correo ya está registrado.";
        } else {
            $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email, contraseña, rol_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $nombre, $email, $password_hashed, $rol_id);

            if ($stmt->execute()) {
                registrar_log($conexion, $_SESSION['usuario_id'], 'crear_usuario', "Registró al usuario $email");
                header("Location: listar.php?msg=Usuario registrado correctamente");
                exit();
            } else {
                $error = "Error al registrar el usuario.";
            }
            $stmt->close();
        }

        $check_stmt->close();
    } else {
        $error = "Por favor, completa todos los campos.";
    }
}
?>

<style>
    html, body {
        margin: 0;
        padding: 0;
        background-color: #B0D0FF; /* Fondo azul claro global */
    }
    .form-container {
        max-width: 600px;
        margin: 40px auto;
        padding: 30px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
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
</style>

<div class="contenido-flex">
    <div class="panel-container">

        <h2 class="titulos">Registrar nuevo usuario</h2>

        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="form-container">
            <div class="mb-3">
                <label class="form-label">Nombre:</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Correo electrónico:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Rol:</label>
                <select name="rol_id" class="form-select" required>
                    <option value="">Selecciona un rol</option>
                    <?php
                    $roles = $conexion->query("SELECT id, nombre FROM roles");
                    while ($rol = $roles->fetch_assoc()) {
                        echo "<option value='{$rol['id']}'>" . htmlspecialchars($rol['nombre']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success px-4">Registrar</button>
                <a href="listar.php" class="btn btn-secondary px-4">Volver a listar</a>
            </div>
        </form>
    </div>

    <?php include_once '../includes/aside.php'; ?>
</div>

<?php include '../includes/footer.php'; ?>

