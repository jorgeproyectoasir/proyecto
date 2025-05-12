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

    <form method="POST" action="">
        <label>Nombre:</label>
        <input type="text" name="nombre" required><br>

        <label>Correo:</label>
        <input type="email" name="email" required><br>

        <label>Contraseña:</label>
        <input type="password" name="password" required><br>

        <button type="submit">Registrarse</button>
    </form>

    <p><a href="../index.php">Volver al login</a></p>
</body>
</html>

