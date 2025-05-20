<?php
include 'includes/auth.php';
include 'includes/header.php';
include 'conexion.php';

$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
$rol = $_SESSION['rol'];
$resultados = [];

if (!empty($busqueda)) {
    $q = "%{$busqueda}%";

    // Buscar en incidencias
    $stmt = $conexion->prepare("SELECT id, descripcion FROM incidencias WHERE descripcion LIKE ? OR tipo LIKE ?");
    $stmt->bind_param("ss", $q, $q);
    $stmt->execute();
    $resultados['Incidencias'] = $stmt->get_result();

    // Buscar en dispositivos
    $stmt = $conexion->prepare("SELECT id, nombre FROM dispositivos WHERE nombre LIKE ? OR ubicacion LIKE ?");
    $stmt->bind_param("ss", $q, $q);
    $stmt->execute();
    $resultados['Dispositivos'] = $stmt->get_result();

    // Buscar en servicios
    $stmt = $conexion->prepare("SELECT id, nombre FROM servicios WHERE nombre LIKE ? OR descripcion LIKE ?");
    $stmt->bind_param("ss", $q, $q);
    $stmt->execute();
    $resultados['Servicios'] = $stmt->get_result();

    // Buscar en tareas
    $stmt = $conexion->prepare("SELECT id, titulo FROM tareas WHERE titulo LIKE ? OR descripcion LIKE ?");
    $stmt->bind_param("ss", $q, $q);
    $stmt->execute();
    $resultados['Tareas'] = $stmt->get_result();

    // Buscar en usuarios (solo admin o técnico)
    if ($rol === 'admin' || $rol === 'tecnico') {
        $stmt = $conexion->prepare("SELECT id, nombre FROM usuarios WHERE nombre LIKE ? OR email LIKE ?");
        $stmt->bind_param("ss", $q, $q);
        $stmt->execute();
        $resultados['Usuarios'] = $stmt->get_result();
    }
}
?>

<h2>Buscador global</h2>
<form method="GET" action="">
    <input type="text" name="q" placeholder="Buscar..." value="<?= htmlspecialchars($busqueda) ?>" required>
    <button type="submit">Buscar</button>
</form>

<?php if (!empty($busqueda)): ?>
    <h4>Resultados para: "<?= htmlspecialchars($busqueda) ?>"</h4>
    <?php
    $encontrado = false;
    foreach ($resultados as $titulo => $rs) {
        if ($rs->num_rows > 0) {
            $encontrado = true;
            echo "<h5>$titulo</h5><ul>";
            while ($fila = $rs->fetch_assoc()) {
                echo "<li><strong>ID {$fila['id']}</strong> - " . htmlspecialchars($fila[array_keys($fila)[1]]) . "</li>";
            }
            echo "</ul>";
        }
    }
    if (!$encontrado) echo "<p>No se encontraron resultados.</p>";
    ?>
<?php endif; ?>

<a href="panel.php" class="btn btn-secondary mt-3">Volver al panel</a>

<?php include 'includes/footer.php'; ?>

