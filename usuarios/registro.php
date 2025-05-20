<?php
include_once("../conexion.php");
session_start();

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Aquí usamos $password

    // Verificar si el usuario ya existe
    $consulta = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $consulta->bind_param("s", $email);
    $consulta->execute();
    $resultado = $consulta->get_result();

    if ($resultado->num_rows > 0) {
        $mensaje = "Ya existe un usuario con ese correo.";
    } else {
        // Rol por defecto: usuario (ajusta el ID según tu base de datos)
        $rol_id = 3;

        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, contraseña, rol_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $nombre, $email, $password, $rol_id); // Usamos $password correctamente

        if ($stmt->execute()) {
            $mensaje = "Registro exitoso. Ya puedes iniciar sesión.";
        } else {
            $mensaje = "Error al registrar usuario.";
        }
	include_once("../includes/log.php");

        $stmt->close();
    }

    $consulta->close();
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
    <h2>Registro de Usuario</h2>
    <?php if ($mensaje != "") echo "<p>$mensaje</p>"; ?>

    <form method="POST" action="" id="formRegistro">
        <label>Nombre:</label>
        <input type="text" name="nombre" required><br>

        <label>Correo:</label>
        <input type="email" name="email" required><br>

        <label>Contraseña:</label>
<div style="display: flex; align-items: center; gap: 10px;">
    <input type="password" name="password" id="password" required>
    <button type="button" id="togglePassword">Mostrar</button>
</div>


        <button type="submit">Registrarse</button>
    </form>

    <p><a href="listar.php" class="btn btn-secondary mt-3">Volver</a></p>
    
    <script src="../js/app.js" defer></script>

</body>
</html>

