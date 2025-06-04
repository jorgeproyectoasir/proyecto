<?php
require_once("../conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $rol_id = intval($_POST['rol_id']);

    if ($nombre && $email && $password && $rol_id) {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        // Comprobamos si el email ya está registrado
        $check_stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error = "El correo ya está registrado.";
        } else {
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, contraseña, rol_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $nombre, $email, $password_hashed, $rol_id);

            if ($stmt->execute()) {
                header("Location: ../usuarios/listar.php");
                exit();
            } else {
                $error = "Error al registrar el usuario.";
            }
        }

        $check_stmt->close();
        $stmt->close();
    } else {
        $error = "Por favor, completa todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Plataforma IT</title>
  <link rel="stylesheet" href="/css/estilo.css?v=1748951008">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="../js/app.js" defer></script>
</head>
<body>

<header>
  <div class="header-container d-flex justify-content-between align-items-center px-4">
    <h1 class="display-5 mb-0" style="font-weight: bold; font-size: 4.5em; margin-top: -10px;">Plataforma IT</h1>
    <div class="text-end fs-5">
      <div><strong>Usuario:</strong> Jorge Admin</div>
      <div><strong>Rol:</strong> admin</div>
      <div><strong>Fecha:</strong> 03/06/2025 13:43</div>
    </div>
  </div>
</header>

<div class="w-100 px-4 mt-4">
<aside class="aside-estandar">
    <h3>Acerca de Plataforma IT</h3>
    <div class="botones-centrados" style="text-align: center;">
        <p>Esta plataforma permite gestionar incidencias, tareas y dispositivos de manera eficiente.</p>
        <p>Diseñada para facilitar el trabajo diario en entornos IT.</p>
    </div>
    <img src="/img/aside.jpg" alt="Nuestra Plataforma" class="img-fluid">
    
    <h3 style="margin-top: 20px; display:flex; justify-content: center;">Beneficios clave</h3>
    <ul style="font-size: 21px;">
        <li>Automatización de procesos IT</li>
        <li>Integración con múltiples sistemas</li>
        <li>Interfaz intuitiva y fácil de usar</li>
    </ul>
</aside>

<div class="contenido">
    <h1>Registrar nuevo usuario</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <form method="post" class="formulario-estandar">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" required>

        <label for="email">Correo electrónico:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required>

        <label for="rol_id">Rol:</label>
        <select name="rol_id" id="rol_id" required>
            <option value="">Selecciona un rol</option>
            <option value="1">Admin</option>
            <option value="2">Técnico</option>
            <option value="3">Usuario</option>
        </select>

        <div class="botones-centrados">
            <button type="submit" class="boton-accion">Registrar</button>
        </div>
    </form>
</div>
</div>

<footer class="mt-5 text-white py-4">
  <div class="container text-center" style="margin-top: 10px;">
    <p class="mb-2" style="font-size: 1.5rem;">&copy; 2025 Plataforma IT. Todos los derechos reservados.</p>
    <p class="mb-0" style="font-size: 1.4rem;">Desarrollado por <strong>Jorge Juncá López</strong> | Proyecto ASIR</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Lógica de inactividad
const tiempoLimite = 900;
const avisoAntes = 60;
let contador = tiempoLimite;

const alerta = document.createElement("div");
alerta.textContent = "⚠️ Tu sesión está a punto de expirar por inactividad.";
alerta.style.cssText = `
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #004085;
    color: black;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
    font-weight: bold;
    display: none;
    z-index: 9999;
`;
document.body.appendChild(alerta);

const intervalo = setInterval(() => {
    contador--;
    if (contador === avisoAntes) alerta.style.display = 'block';
    if (contador <= 0) {
        clearInterval(intervalo);
        window.location.href = '/proyecto/index.php?expirado=1';
    }
}, 1000);

['mousemove', 'keydown', 'click', 'scroll'].forEach(evento => {
    document.addEventListener(evento, () => {
        contador = tiempoLimite;
        alerta.style.display = 'none';
    });
});
</script>

</body>
</html>
