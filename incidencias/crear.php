<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Plataforma IT</title>
  <link rel="stylesheet" href="/css/estilo.css?v=1748867319">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="../js/app.js" defer></script>
  <style>
    body {
        margin: 0;
        padding: 0;
        background-color: #B0D0FF;
        font-family: Arial, sans-serif;
    }

    .titulos {
        font-size: 60px !important;
        margin-top: 40px !important;
        text-align: center;
        margin-top: 20px;
        font-size: 2em;
        color: #333;
    }

    form {
        max-width: 700px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        padding: 30px;
        box-sizing: border-box;
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

    textarea.form-control {
        resize: vertical;
        height: 120px;
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

    /* Contenedor flex para formulario + aside */
    .contenido-flex {
      display: flex;
      justify-content: center;
      gap: 30px;
      max-width: 1200px;
      margin: 30px auto 60px auto;
      padding: 0 15px;
      box-sizing: border-box;
      align-items: flex-start;
    }

    /* Aside con estilo unificado */
    aside.aside-estandar {
      width: 380px;
      background-color: #f9f9f9;
      padding: 25px 30px;
      font-size: 21px;
      border-radius: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      border-left: 3px solid #ccc;
      box-sizing: border-box;
    }

    aside.aside-estandar h3 {
      margin-top: 0;
      margin-bottom: 15px;
      font-weight: 700;
      font-size: 1.8rem;
      color: #333;
    }

    aside.aside-estandar ul {
      padding-left: 20px;
    }

    aside.aside-estandar ul li {
      margin-bottom: 10px;
      line-height: 1.3;
    }

    aside.aside-estandar img {
      width: 100%;
      margin-top: 20px;
      border-radius: 12px;
      object-fit: cover;
    }
  </style>
</head>
<body>

<header>
  <div class="header-container d-flex justify-content-between align-items-center px-4">
    <h1 class="display-5 mb-0" style="font-weight: bold; font-size: 4.5em; margin-top: -10px;">Plataforma IT</h1>

    <div class="text-end fs-5">
      <div><strong>Usuario:</strong> Jorge Admin</div>
      <div><strong>Rol:</strong> admin</div>
      <div><strong>Fecha:</strong> 02/06/2025 14:28</div>
    </div>
  </div>
</header>

<!-- === CONTENIDO PRINCIPAL === -->
<div class="w-100 px-4 mt-4">

  <h2 class="titulos">Crear nueva incidencia</h2>

  <div class="contenido-flex">

    <form method="POST" novalidate>
        <div class="mb-3">
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label for="tipo">Tipo:</label>
            <select id="tipo" name="tipo" class="form-control" required>
                <option value="error">Error</option>
                <option value="mantenimiento">Mantenimiento</option>
                <option value="aviso">Aviso</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="dispositivo">Dispositivo afectado:</label>
            <select id="dispositivo" name="dispositivo" class="form-control" required>
                <option value="3">Teléfono Araña Reuniones</option>
                <option value="24">Servidor Principal</option>
                <option value="25">Router Central</option>
                <option value="26">Portátil Juan Pérez</option>
                <option value="27">Impresora Oficina</option>
                <option value="28">NAS de Respaldo</option>
                <option value="29">Router Soporteu</option>
                <option value="39">sdadadsadsa</option>
                <option value="40">prueba1 santiagojunca</option>
            </select>
        </div>

        <div class="botones-centrados">
            <button type="submit" class="btn btn-success boton-accion">Guardar</button>
            <a href="listar.php" class="btn btn-secondary boton-accion">Cancelar</a>
        </div>
    </form>

    <aside class="aside-estandar">
      <h3>Información de la incidencia</h3>
      <p>Para crear una nueva incidencia, completa todos los campos con la información necesaria:</p>
      <ul>
        <li>Descripción detallada del problema o aviso.</li>
        <li>Tipo de incidencia: error, mantenimiento o aviso.</li>
        <li>Selecciona el dispositivo afectado para mejor seguimiento.</li>
      </ul>
      <img src="/img/incidencia.jpg" alt="Incidencia" />
    </aside>

  </div>

</div> <!-- Cierre del div principal -->

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
