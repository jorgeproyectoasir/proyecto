<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'includes/auth.php';
include 'includes/header.php';
include 'conexion.php';

$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
$rol = $_SESSION['rol'];
$resultados = [];

if (!empty($busqueda)) {
    $q = "%{$busqueda}%";

    $stmt = $conexion->prepare("SELECT id, descripcion FROM incidencias WHERE descripcion LIKE ? OR titulo LIKE ?");
    $stmt->bind_param("ss", $q, $q);
    $stmt->execute();
    $resultados['Incidencias'] = $stmt->get_result();
    $stmt->close();

    $stmt = $conexion->prepare("SELECT id, nombre FROM dispositivos WHERE nombre LIKE ?");
    $stmt->bind_param("s", $q);
    $stmt->execute();
    $resultados['Dispositivos'] = $stmt->get_result();
    $stmt->close();

    $stmt = $conexion->prepare("SELECT id, nombre FROM servicios WHERE nombre LIKE ? OR descripcion LIKE ?");
    $stmt->bind_param("ss", $q, $q);
    $stmt->execute();
    $resultados['Servicios'] = $stmt->get_result();
    $stmt->close();

    $stmt = $conexion->prepare("SELECT id, titulo FROM tareas WHERE titulo LIKE ? OR descripcion LIKE ?");
    $stmt->bind_param("ss", $q, $q);
    $stmt->execute();
    $resultados['Tareas'] = $stmt->get_result();
    $stmt->close();

    if ($rol === 'admin' || $rol === 'tecnico') {
        $stmt = $conexion->prepare("SELECT id, nombre FROM usuarios WHERE nombre LIKE ? OR email LIKE ?");
        $stmt->bind_param("ss", $q, $q);
        $stmt->execute();
        $resultados['Usuarios'] = $stmt->get_result();
        $stmt->close();
    }
}
?>

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
    <input type="text" name="q" class="form-control" style="font-size:2em;" placeholder="Buscar por descripción, nombre, correo, etc." value="<?= htmlspecialchars($busqueda) ?>" required>
    <button type="submit" class="btn" style="background-color: #198754;">Buscar</button>
  </form>

  <?php if (!empty($busqueda)): ?>
    <h4 class="mt-4">Resultados para: <em>"<?= htmlspecialchars($busqueda) ?>"</em></h4>
    <?php
    $encontrado = false;
    foreach ($resultados as $titulo => $rs) {
        if ($rs->num_rows > 0) {
            $encontrado = true;
            echo "<h5 class='mt-3'>$titulo</h5><ul class='list-group'>";
            while ($fila = $rs->fetch_assoc()) {
                $id = $fila['id'];
                $texto = htmlspecialchars($fila[array_keys($fila)[1]]);
                switch ($titulo) {
                    case 'Incidencias': $url = "incidencias/detalle.php?id=$id"; break;
                    case 'Dispositivos': $url = "dispositivos/detalle.php?id=$id"; break;
                    case 'Servicios': $url = "servicios/detalle.php?id=$id"; break;
                    case 'Tareas': $url = "tareas/detalle.php?id=$id"; break;
                    case 'Usuarios': $url = "usuarios/detalle.php?id=$id"; break;
                    default: $url = "#"; break;
                }
                echo "<li><a href='$url'><strong>ID $id</strong> - $texto</a></li>";
            }
            echo "</ul>";
        }
    }
    if (!$encontrado) {
        echo "<p class='mt-3'>No se encontraron resultados.</p>";
    }
    ?>
  <?php else: ?>
    <p class="mt-3" style="font-size: 2em;">Usa el formulario para buscar en el sistema.</p>
  <?php endif; ?>

  <div class="botones-centrados">
    <a href="panel.php" class="btn btn-secondary" style="font-weight:bold;">Volver al panel</a>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
