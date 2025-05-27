<?php
include '../includes/header.php';
include_once("../conexion.php");
session_start();

$mensaje = "";
$claseMensaje = "alert";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $consulta = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $consulta->bind_param("s", $email);
    $consulta->execute();
    $resultado = $consulta->get_result();

    if ($resultado->num_rows > 0) {
        $_SESSION['flash'] = [
            'tipo' => 'danger',
            'mensaje' => '❌ Ya existe un usuario con ese correo.'
        ];
    } else {
        $rol_id = 3;
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, contraseña, rol_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $nombre, $email, $password, $rol_id);

        if ($stmt->execute()) {
            $_SESSION['flash'] = [
                'tipo' => 'success',
                'mensaje' => '✅ Registro exitoso. Ya puedes iniciar sesión.'
            ];
        } else {
            $_SESSION['flash'] = [
                'tipo' => 'danger',
                'mensaje' => '❌ Error al registrar usuario.'
            ];
        }
        $stmt->close();
    }

    $consulta->close();
    header("Location: listar.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <h1 style="text-align: center; font-size: 4em; font-weight: bold;">Registrar nuevo usuario</h1>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?= $_SESSION['flash']['tipo'] ?> text-center">
            <?= $_SESSION['flash']['mensaje'] ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <form method="POST" action="" style="max-width: 700px; margin: 0 auto; font-size: 3em;">
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
                <button type="button" id="togglePassword" class="btn btn-secondary">Mostrar</button>
            </div>
        </div>

        <!-- Botones alineados -->
        <div style="display: flex; justify-content: center; gap: 30px; margin-top: 20px;">
            <button type="submit" class="btn" style="background-color: #198754; color: white; min-width: 150px;">Registrarse</button>
            <a href="listar.php" class="btn btn-secondary" style="min-width: 150px;">Volver</a>
        </div>
    </form>

    <script src="../js/app.js" defer></script>
</body>
</html>

<?php include '../includes/footer.php'; ?>


