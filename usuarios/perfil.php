
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Plataforma IT</title>
  <link rel="stylesheet" href="/css/estilo.css?v=1748939754">
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
      <div><strong>Fecha:</strong> 03/06/2025 10:35</div>
    </div>
      </div>
</header>

<!-- === CONTENIDO PRINCIPAL === -->
<div class="w-100 px-4 mt-4">



<!-- ✅ ESTILO EMBEBIDO -->
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
</style>

<!-- ✅ FORMULARIO -->
<div class="contenido-flex">
<div class="panel-container">

    <h2 class="titulos">Mi perfil</h2>

    <form method="POST">
        <div class="mb-3">
            <label>Nombre:</label>
            <input type="text" name="nombre" class="form-control" value="Tu nombre" required>
        </div>

        <div class="mb-3">
            <label>Correo:</label>
            <input type="email" name="correo" class="form-control" value="Tu correo" required>
        </div>

        <div class="mb-3">
            <label>Contraseña nueva:</label>
		<input type="password" name="contraseña" class="form-control" value="Tu contraseña" required>
		<input type="button" name="boton" class="form-control" value="Mostrar">
            </select>
        </div>


        <div class="botones-centrados">
            <button type="submit" class="btn btn-success boton-accion">Guardar cambios</button>
            <a href="listar.php" class="btn btn-secondary boton-accion">Volver</a>
        </div>
    </form>

</div>
<aside class="aside-estandar">
    <h3>Acerca de Plataforma IT</h3>
    <div class="botones-centrados" style="text-align: center;">
    <p>Esta plataforma permite gestionar incidencias, tareas y dispositivos de manera eficiente.</p>
    <p>Diseñada para facilitar el trabajo diario en entornos IT.</p>
    </div>
    <img src="/img/aside.jpg" alt="Nuestra Plataforma" class="img-fluid">
    
    <h3 style="margin-top: 20px; display:flex; justify-content: center;">Beneficios clave</h4>
    <ul style="font-size: 21px;">
        <li>Automatización de procesos IT</li>
        <li>Integración con múltiples sistemas</li>
        <li>Interfaz intuitiva y fácil de usar</li>
    </ul>
</aside>

</div>
</div> <!-- Cierre del div principal abierto en header.php -->

<footer class="mt-5 text-white py-4">
  <div class="container text-center" style="margin-top: 10px;">
    <p class="mb-2" style="font-size: 1.5rem;">&copy; 2025 Plataforma IT. Todos los derechos reservados.</p>
    <p class="mb-0" style="font-size: 1.4rem;">Desarrollado por <strong>Jorge Juncá López</strong> | Proyecto ASIR</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Tiempo de inactividad antes de la expiración (en segundos)
const tiempoLimite = 900; // 15 minutos
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

    if (contador === avisoAntes) {
        alerta.style.display = 'block';
    }

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
