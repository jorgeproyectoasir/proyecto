<?php
include '../includes/auth.php';
include '../includes/header.php';
include '../conexion.php';
require_once '../includes/log.php';

echo "<style>
    html, body {
        margin: 0;
        padding: 0;
        background-color: #B0D0FF;
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

</style>";
echo '<div class="contenido-flex">';
echo '<div class="panel-container">';

// Mensaje flash
if (isset($_GET['msg'])) {
    echo "<div class='alert alert-success mt-3'>" . htmlspecialchars($_GET['msg']) . "</div>";
}

// Eliminar servicio (solo admin)
if (isset($_GET['eliminar']) && $_SESSION['rol'] === 'admin') {
    $id = intval($_GET['eliminar']);
    $stmt = $conexion->prepare("DELETE FROM servicios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    registrar_log($conexion, $_SESSION['usuario_id'], 'eliminar_servicio', "Eliminó el servicio con ID $id");
    echo "<div class='alert alert-success mt-3'>Servicio eliminado correctamente.</div>";
}

// Título
echo "<h2 class='titulos'>Listado de Servicios</h2>";

// Consulta de servicios
$sql = "SELECT * FROM servicios";
$resultado = $conexion->query($sql);

// Tabla
echo "<table class='table-accesos'>
<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Descripción</th>
    <th>Tipo</th>
    <th>Estado</th>
    <th>Acciones</th>
</tr>";

while ($row = $resultado->fetch_assoc()) {
    echo "<tr>

        <td>{$row['id']}</td>
        <td>" . htmlspecialchars($row['nombre']) . "</td>
        <td>" . htmlspecialchars($row['descripcion']) . "</td>
        <td>" . ($row['tipo'] ?? '<em>No definido</em>') . "</td>
        <td>" . ucfirst($row['estado']) . "</td>
        <td>";

    // Editar
    if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico') {
        echo "<a href='editar.php?id={$row['id']}' class='btn me-2' style='background-color: #0d6efd; color: white; font-weight: bold;'>Editar</a>";
    }

    // Eliminar
    if ($_SESSION['rol'] === 'admin') {
        echo "<a href='listar.php?eliminar={$row['id']}' class='btn btn-danger eliminar' style='font-weight: bold;' onclick=\"return confirm('¿Estás seguro de eliminar este servicio?');\">Eliminar</a>";
    }

    echo "</td></tr>";
}

echo "</table>";

// Botones centrados como en usuarios
echo "<div style='text-align: center; margin-top: 20px;'>";

if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico') {
    echo "<a href='agregar.php' class='btn btn-success me-2' style='min-width: 180px; font-weight: bold; font-size:1.2em;'>Agregar nuevo servicio</a>";
}

echo "<a href='../panel.php' class='btn btn-secondary' style='min-width: 180px; font-weight: bold; font-size:1.2em;'>Volver al panel</a>";
echo "</div>";

echo '</div>'; // panel-container
include_once '../includes/aside.php';
echo '</div>'; // contenido-flex
include '../includes/footer.php';
?>

