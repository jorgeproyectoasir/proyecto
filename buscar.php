<?php
require_once "conexion.php";
require_once "includes/auth.php";
require_once "includes/header.php";

$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
?>

<!-- ✅ ESTILO EMBEBIDO -->
<style>
    body {
        margin: 0;
        padding: 0;
        background-color: #B0D0FF;
        font-family: Arial, sans-serif;
    }

    .contenido-principal {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
    }

    .titulos {
        text-align: center;
        margin-top: 20px;
        font-size: 2em;
        color: #333;
    }

    .panel-container {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        padding: 30px;
        margin-top: 30px;
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

    .resultados h3 {
        margin-top: 30px;
        color: #0056b3;
    }

    .resultados ul {
        list-style: none;
        padding-left: 0;
    }

    .resultados li {
        background: #f2f2f2;
        margin-bottom: 8px;
        padding: 10px;
        border-radius: 6px;
    }
</style>

<div class="contenido-principal">
    <main class="panel-container">
        <h1 class="titulos">Buscador Global</h1>
	<p style="text-align:center; font-size:1.8em; margin-top:10px; color:#555;">
    Utiliza este buscador para localizar tareas, incidencias, dispositivos, servicios, usuarios y registros del sistema. 
    Escribe una palabra clave relacionada con lo que estás buscando.
</p>
<p style="text-align:center; font-size:1.5em; margin-top:10px; color:#555;">
    Puedes buscar por nombres, IPs, tipos, correos o descripciones. Por ejemplo: <em>“Firewall”, “usuario@empresa.com” o “Incidencia red”.</em>
</p>

        <form method="get" action="buscar.php" class="mb-3">
            <input type="text" name="q" placeholder="Buscar en la plataforma..." class="form-control" value="<?= htmlspecialchars($busqueda) ?>" required>
            <div class="botones-centrados">
                <button type="submit" class="boton-accion btn-success">Buscar</button>
		<a href="index.php" class="boton-accion btn-secondary" style="min-width: 120px; text-align: center; display: inline-block; text-decoration: none; background-color: grey;">
        Volver
    </a>
            </div>
        </form>

        <?php if (!empty($busqueda)) : ?>
            <?php
            $like = '%' . $conn->real_escape_string($busqueda) . '%';
            echo "<div class='resultados'><h2>Resultados para: <em>" . htmlspecialchars($busqueda) . "</em></h2>";

            // === TAREAS ===
            $query = "SELECT * FROM tareas WHERE titulo LIKE ? OR descripcion LIKE ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $like, $like);
            $stmt->execute();
            $tareas = $stmt->get_result();
            if ($tareas->num_rows > 0) {
                echo "<h3>Tareas</h3><ul>";
                while ($row = $tareas->fetch_assoc()) {
                    echo "<li><strong>" . $row['titulo'] . "</strong> — " . $row['descripcion'] . "</li>";
                }
                echo "</ul>";
            }

            // === INCIDENCIAS ===
            $query = "SELECT * FROM incidencias WHERE descripcion LIKE ? OR tipo LIKE ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $like, $like);
            $stmt->execute();
            $incidencias = $stmt->get_result();
            if ($incidencias->num_rows > 0) {
                echo "<h3>Incidencias</h3><ul>";
                while ($row = $incidencias->fetch_assoc()) {
                    echo "<li><strong>" . $row['tipo'] . "</strong>: " . $row['descripcion'] . "</li>";
                }
                echo "</ul>";
            }

            // === DISPOSITIVOS ===
            $query = "SELECT * FROM dispositivos WHERE nombre LIKE ? OR ip LIKE ? OR tipo LIKE ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $like, $like, $like);
            $stmt->execute();
            $dispositivos = $stmt->get_result();
            if ($dispositivos->num_rows > 0) {
                echo "<h3>Dispositivos</h3><ul>";
                while ($row = $dispositivos->fetch_assoc()) {
                    echo "<li><strong>" . $row['nombre'] . "</strong> — " . $row['ip'] . " — " . $row['tipo'] . "</li>";
                }
                echo "</ul>";
            }

            // === SERVICIOS ===
            $query = "SELECT * FROM servicios WHERE nombre LIKE ? OR tipo LIKE ? OR descripcion LIKE ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $like, $like, $like);
            $stmt->execute();
            $servicios = $stmt->get_result();
            if ($servicios->num_rows > 0) {
                echo "<h3>Servicios</h3><ul>";
                while ($row = $servicios->fetch_assoc()) {
                    echo "<li><strong>" . $row['nombre'] . "</strong> — " . $row['descripcion'] . "</li>";
                }
                echo "</ul>";
            }

            // === USUARIOS (solo si es admin) ===
            if ($_SESSION['rol'] == 1) {
                $query = "SELECT * FROM usuarios WHERE nombre LIKE ? OR email LIKE ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $like, $like);
                $stmt->execute();
                $usuarios = $stmt->get_result();
                if ($usuarios->num_rows > 0) {
                    echo "<h3>Usuarios</h3><ul>";
                    while ($row = $usuarios->fetch_assoc()) {
                        echo "<li><strong>" . $row['nombre'] . "</strong> — " . $row['email'] . "</li>";
                    }
                    echo "</ul>";
                }
            }

            // === LOGS ===
            $query = "SELECT * FROM logs WHERE accion LIKE ? OR entidad_afectada LIKE ? OR descripcion LIKE ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $like, $like, $like);
            $stmt->execute();
            $logs = $stmt->get_result();
            if ($logs->num_rows > 0) {
                echo "<h3>Logs</h3><ul>";
                while ($row = $logs->fetch_assoc()) {
                    echo "<li><strong>" . $row['accion'] . "</strong> — " . $row['descripcion'] . "</li>";
                }
                echo "</ul>";
            }

            // Si no se encontraron resultados
            if (
                $tareas->num_rows == 0 &&
                $incidencias->num_rows == 0 &&
                $dispositivos->num_rows == 0 &&
                $servicios->num_rows == 0 &&
                (!isset($usuarios) || $usuarios->num_rows == 0) &&
                $logs->num_rows == 0
            ) {
                echo "<p>No se encontraron resultados para la búsqueda.</p>";
            }

            $stmt->close();
            echo "</div>";
            ?>
        <?php endif; ?>
    </main>
</div>

<?php require_once "includes/footer.php"; ?>

