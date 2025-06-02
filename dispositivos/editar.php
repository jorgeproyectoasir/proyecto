<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar dispositivo</title>
  <link rel="stylesheet" href="/css/estilo.css?v=1748854650">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="../js/app.js" defer></script>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #B0D0FF;
      font-family: Arial, sans-serif;
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

    /* Contenedor flex para form + aside */
    .contenido-flex {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      gap: 20px;
      max-width: 1120px;
      margin: 30px auto;
      padding: 0 15px;
      box-sizing: border-box;
    }

    /* Estilo unificado del aside, igual que en registro */
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
      /* Eliminado display: flex para que sea igual al registro */
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
      <div><strong>Fecha:</strong> 02/06/2025 10:57</div>
    </div>
  </div>
</header>

<!-- === CONTENIDO PRINCIPAL === -->
<div class="w-100 px-4 mt-4">

<div class="contenido-flex">

  <div class="panel-container">
    <h2 class="titulos">Editar dispositivo</h2>

    <form method="POST" action="procesar_editar.php">
      <div class="mb-3">
        <label for="nombre">Nombre dispositivo:</label>
        <input type="text" id="nombre" name="nombre" class="form-control" value="Nombre actual" required>
      </div>

      <div class="mb-3">
        <label for="tipo">Tipo:</label>
        <select id="tipo" name="tipo" class="form-control" required>
          <option value="pc">PC</option>
          <option value="impresora">Impresora</option>
          <option value="monitor">Monitor</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="estado">Estado:</label>
        <select id="estado" name="estado" class="form-control" required>
          <option value="activo">Activo</option>
          <option value="inactivo">Inactivo</option>
        </select>
      </div>

      <div class="botones-centrados">
        <button type="submit" class="btn btn-success boton-accion">Guardar cambios</button>
        <a href="listar.php" class="btn btn-secondary boton-accion">Cancelar</a>
      </div>
    </form>
  </div>

  <!-- ASIDE -->
  <aside class="aside-estandar">
    <h3>Información del dispositivo</h3>
    <p>Este formulario permite editar los datos básicos del dispositivo seleccionado.</p>
    <ul style="font-size: 21px;">
      <li>Nombre visible para identificar</li>
      <li>Tipo del dispositivo</li>
      <li>Estado actual del dispositivo</li>
    </ul>
    <img src="/img/aside.jpg" alt="Dispositivo" class="img-fluid" style="margin-top: 20px; border-radius: 12px;">
  </aside>

</div>

</div> <!-- cierre div principal -->

<footer class="mt-5 text-white py-4">
  <div class="container text-center" style="margin-top: 10px;">
    <p class="mb-2" style="font-size: 1.5rem;">&copy; 2025 Plataforma IT. Todos los derechos reservados.</p>
    <p class="mb-0" style="font-size: 1.4rem;">Desarrollado por <strong>Jorge Juncá López</strong> | Proyecto ASIR</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
