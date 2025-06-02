<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Plataforma IT</title>
  <link rel="stylesheet" href="/css/estilo.css?v=1748867203">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="../js/app.js" defer></script>
  <style>
    html, body {
        margin: 0;
        padding: 0;
        background-color: #B0D0FF;
    }
    .titulos {
        text-align: center;
        font-size: 3.5em;
        font-weight: bold;
        margin-top: 20px;
        margin-bottom: 25px;
    }
    .contenedor-form {
        max-width: 600px;
        background-color: white;
        padding: 25px 30px;
        border-radius: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        font-size: 1.3em;
        box-sizing: border-box;
    }
    label {
        font-weight: 600;
        margin-bottom: 5px;
        display: block;
    }
    input[type=text], textarea, select {
        width: 100%;
        padding: 10px 12px;
        margin-bottom: 20px;
        border: 1.5px solid #ccc;
        border-radius: 10px;
        font-size: 1em;
        transition: border-color 0.3s ease;
    }
    input[type=text]:focus, textarea:focus, select:focus {
        border-color: #4a90e2;
        outline: none;
    }
    .botones-centrados {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 10px;
        flex-wrap: wrap;
    }
    .btn {
        min-width: 180px;
        font-weight: bold;
        padding: 12px 0;
        border-radius: 10px;
        cursor: pointer;
        border: none;
        color: white;
        text-decoration: none;
        text-align: center;
        display: inline-block;
        user-select: none;
        transition: background-color 0.3s ease;
    }
    .btn-success {
        background-color: #28a745;
    }
    .btn-success:hover {
        background-color: #218838;
    }
    .btn-secondary {
        background-color: #6c757d;
    }
    .btn-secondary:hover {
        background-color: #5a6268;
    }
    .alert {
        margin: 10px auto 20px auto;
        width: fit-content;
        padding: 12px 25px;
        background-color: #ffe6e6;
        color: #b30000;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
        font-size: 1.1em;
        max-width: 600px;
    }

    /* NUEVOS ESTILOS para contenedor flex que une formulario y aside */
    .contenido-flex {
      display: flex;
      justify-content: center;
      gap: 20px;
      max-width: 1120px;
      margin: 0 auto 50px auto;
      padding: 0 15px;
      box-sizing: border-box;
      align-items: flex-start;
    }

    /* Aside con el estilo unificado igual que en registro */
    .aside-estandar {
      width: 400px;
      flex: 0 0 400px;
      background-color: #f9f9f9;
      padding: 20px;
      font-size: 25px;
      border-radius: 20px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      border-left: 2px solid #ccc;
      margin-left: 20px;
      box-sizing: border-box;
      /* NO display:flex para mantener el mismo estilo */
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
      <div><strong>Fecha:</strong> 02/06/2025 14:26</div>
    </div>
  </div>
</header>

<!-- === CONTENIDO PRINCIPAL === -->
<div class="w-100 px-4 mt-4">

  <h2 class="titulos">Agregar nuevo servicio</h2>

  <div class="contenido-flex">
    <div class="contenedor-form">
      <form method="POST" action="">
          <label for="nombre">Nombre del servicio:</label>
          <input type="text" id="nombre" name="nombre" required>

          <label for="descripcion">Descripción:</label>
          <textarea id="descripcion" name="descripcion" rows="4"></textarea>

          <label for="tipo">Tipo:</label>
          <input type="text" id="tipo" name="tipo" required placeholder="Ej: mantenimiento">

          <label for="estado">Estado:</label>
          <select id="estado" name="estado">
              <option value="activo">Activo</option>
              <option value="inactivo">Inactivo</option>
          </select>

          <div class="botones-centrados">
              <button type="submit" class="btn btn-success">Guardar</button>
              <a href="listar.php" class="btn btn-secondary">Volver a la lista</a>
          </div>
      </form>
    </div>

    <aside class="aside-estandar">
      <h3>Información del servicio</h3>
      <p>Completa los campos para agregar un nuevo servicio a la plataforma IT.</p>
      <ul style="font-size: 21px;">
        <li>Nombre identificativo del servicio</li>
        <li>Descripción breve y clara</li>
        <li>Tipo de servicio (por ejemplo, mantenimiento)</li>
        <li>Estado actual del servicio</li>
      </ul>
      <img src="/img/aside.jpg" alt="Servicio" class="img-fluid" style="margin-top: 20px; border-radius: 12px;">
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
