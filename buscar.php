
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Plataforma IT</title>
  <link rel="stylesheet" href="/css/estilo.css?v=1748944952">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="js/app.js" defer></script>
</head>
<body>


<header>
  <div class="header-container d-flex justify-content-between align-items-center px-4">
    <h1 class="display-5 mb-0" style="font-weight: bold; font-size: 4.5em; margin-top: -10px;">Plataforma IT</h1>

        <div class="text-end fs-5">
      <div><strong>Usuario:</strong> Jorge Admin</div>
      <div><strong>Rol:</strong> admin</div>
      <div><strong>Fecha:</strong> 03/06/2025 12:02</div>
    </div>
      </div>
</header>

<!-- === CONTENIDO PRINCIPAL === -->
<div class="w-100 px-4 mt-4">



<style>
  body, html {
    margin: 0;
    padding: 0;
    background-color: #B0D0FF;
    font-family: Arial, sans-serif;
  }

  /* Contenedor principal centrado y con ancho máximo */
  .panel-container {
    max-width: 900px;
    margin: 30px auto;
    padding: 30px;
    background-color: white;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    border-radius: 8px;
  }

  .titulos {
    color: #004085;
    font-weight: bold;
    margin-bottom: 15px;
  }

  .formulario {
    display: flex;
    flex-direction: column;
  }

  .form-control {
    padding: 10px;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 4px;
    width: 100%;
    box-sizing: border-box;
  }

  .btn {
    cursor: pointer;
    background-color: #6c757d;
    color: white;
    border: none;
    padding: 10px 20px;
    font-weight: bold;
    border-radius: 4px;
    margin-top: 10px;
    align-self: flex-start;
  }

  .btn:hover {
    background-color: #6c757d;
    color: white;
  }

  ul.list-group {
    list-style: none;
    padding-left: 0;
    margin-top: 0.5rem;
  }

  ul.list-group li {
    background-color: #e9ecef;
    margin-bottom: 0.3rem;
    padding: 10px;
    border-radius: 4px;
  }

  ul.list-group li a {
    color: #004085;
    text-decoration: none;
  }

  ul.list-group li a:hover {
    text-decoration: underline;
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

  .botones-centrados {
    text-align: center;
  }
</style>

<div class="panel-container">
  <h2 class="titulos">Buscador global</h2>
  <p style="font-size: 2em;">Introduce una palabra o término para buscar en incidencias, dispositivos, servicios, tareas y, si tienes permisos, también en usuarios.</p>

  <form method="GET" action="" class="formulario">
    <input type="text" name="q" class="form-control" style="font-size:2em;" placeholder="Buscar por descripción, nombre, correo, etc." value="" required>
    <button type="submit" class="btn" style="background-color: #198754;">Buscar</button>
  </form>

      <p class="mt-3" style="font-size: 2em;">Usa el formulario para buscar en el sistema.</p>
  
  <div class="botones-centrados">
    <a href="panel.php" class="btn btn-secondary" style="font-weight:bold;">Volver al panel</a>
  </div>
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
