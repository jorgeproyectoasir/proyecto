<?php
session_start();
require_once '../includes/log.php';
include '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($password, $usuario['contraseña'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = obtenerRol($conexion, $usuario['rol_id']);
            header("Location: ../panel.php");
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }

    $stmt->close();
}

function obtenerRol($conexion, $rol_id) {
    $sql = "SELECT nombre FROM roles WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $rol_id);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    return $resultado['nombre'];
    registrar_log($conexion, $user['id'], 'Inicio de sesión');
}
?>


<?php include '../includes/header.php'; ?>
<link rel="stylesheet" href="../css/estilo.css">
<h2>Error al iniciar sesión</h2>
<p class="text-danger"><?php echo $error ?? ''; ?></p>
<a href="../index.php" class="btn btn-secondary">Volver</a>
<?php include '../includes/footer.php'; ?>

