<?php
require_once "conexion.php";
require_once "includes/auth.php";
require_once "includes/header.php";
require_once "includes/aside.php";

$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
?>

<main class="contenido">
    <h1>Buscador Global</h1>
    <form method="get" action="buscar.php" class="formulario-busqueda">
        <input type="text" name="q" placeholder="Buscar en la plataforma..." value="<?= htmlspecialchars($busqueda) ?>" required>
        <button type="submit">Buscar</button>
    </form>

<?php
if (!empty($busqueda)) {
    $like = '%' . $conn->real_escape_string($busqueda) . '%';

    echo "<h2>Resultados para: <em>" . htmlspecialchars($busqueda) . "</em></h2>";

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
}
?>

</main>

<?php require_once "includes/footer.php"; ?>
