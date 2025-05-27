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

    // Incidencias
    $stmt = $conexion->prepare("SELECT id, descripcion FROM incidencias WHERE descripcion LIKE ? OR titulo LIKE ?");
    $stmt->bind_param("ss", $q, $q);
    $stmt->execute();
    $resultados['Incidencias'] = $stmt->get_result();
    $stmt->close();

    // Dispositivos
    $stmt = $conexion->prepare("SELECT id, nombre FROM dispositivos WHERE nombre LIKE ?");
    $stmt->bind_param("s", $q);
    $stmt->execute();
    $resultados['Dispositivos'] = $stmt->get_result();
    $stmt->close();

    // Servicios
    $stmt = $conexion->prepare("SELECT id, nombre FROM servicios WHERE nombre LIKE ? OR descripcion LIKE ?");
    $stmt->bind_param("ss", $q, $q);
    $stmt->execute();
    $resultados['Servicios'] = $stmt->get_result();
    $stmt->close();

    // Tareas
    $stmt = $conexion->prepare("SELECT id, titulo FROM tareas WHERE titulo LIKE ? OR descripcion LIKE ?");
    $stmt->bind_param("ss", $q, $q);
    $stmt->execute();
    $resultados['Tareas'] = $stmt->get_result();
    $stmt->close();

    // Usuarios
    if ($rol === 'admin' || $rol === 'tecnico') {
        $stmt = $conexion->prepare("SELECT id, nombre FROM usuarios WHERE nombre LIKE ? OR email LIKE ?");
        $stmt->bind_param("ss", $q, $q);
        $stmt->execute();
        $resultados['Usuarios'] = $stmt->get_result();
        $stmt->close();
    }
}
?>

<div class="contenido-flex">
    <div class="panel-container">
        <h2>Buscador global</h2>
        <p>Introduce una palabra o término para buscar en incidencias, dispositivos, servicios, tareas y, si tienes permisos, también en usuarios.</p>

        <form method="GET" action="" class="formulario ancho-medio">
            <input type="text" name="q" class="form-control" placeholder="Buscar por descripción, nombre, correo, etc." value="<?= htmlspecialchars($busqueda) ?>" required>
            <button type="submit" class="btn btn-primary mt-2">Buscar</button>
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
                        echo "<li class='list-group-item'><a href='$url'><strong>ID $id</strong> - $texto</a></li>";
                    }
                    echo "</ul>";
                }
            }
            if (!$encontrado) {
                echo "<p class='mt-3'>No se encontraron resultados.</p>";
            }
            ?>
        <?php else: ?>
            <p class="mt-3">Usa el formulario para buscar en el sistema.</p>
        <?php endif; ?>

        <a href="panel.php" class="btn btn-secondary mt-4">Volver al panel</a>
    </div>

    <?php include_once 'includes/aside.php'; ?>
</div>

<?php include 'includes/footer.php'; ?>
