<?php
include '../includes/auth.php';
include '../includes/header.php';
include '../conexion.php';
require_once '../includes/log.php';
echo "<style>
    html, body {
        margin: 0;
        padding: 0;
        background-color: #B0D0FF; /* Fondo azul claro global */
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
     .table-accesos th, .table-accesos td {
    padding: 20px;
    font-size: 30px;
}
</style>";


echo '<div class="contenido-flex">';
echo '<div class="panel-container">';

// Mensaje flash
if (isset($_GET['msg'])) {
    echo "<div class='alert alert-success mt-3'>" . htmlspecialchars($_GET['msg']) . "</div>";
}

// Eliminar usuario (solo admin)
if (isset($_GET['eliminar']) && $_SESSION['rol'] === 'admin') {
    $id = intval($_GET['eliminar']);
    if ($id !== $_SESSION['usuario_id']) {
        $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        registrar_log($conexion, $_SESSION['usuario_id'], 'eliminar_usuario', "Eliminó al usuario con ID $id");
        echo "<div class='alert alert-success mt-3'>Usuario eliminado correctamente.</div>";
    } else {
        echo "<div class='alert alert-warning mt-3'>No puedes eliminar tu propio usuario.</div>";
    }
}

// Título
echo "<h2 class='titulos'>Listado de Usuarios</h2>";

// Consulta de usuarios con nombre de rol
$sql = "SELECT u.id, u.nombre, u.email, r.nombre AS rol_nombre
        FROM usuarios u
        LEFT JOIN roles r ON u.rol_id = r.id
        ORDER BY u.nombre ASC";

$resultado = $conexion->query($sql);

// Tabla de usuarios
// Tabla de usuarios
echo "<table class='table-accesos' style='margin-top: 30px;'>
<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Email</th>
    <th>Rol</th>";

if ($_SESSION['rol'] === 'admin') {
    echo "<th>Acciones</th>";
}

echo "</tr>";

while ($row = $resultado->fetch_assoc()) {
    echo "<tr>
        <td>" . $row['id'] . "</td>
        <td>" . htmlspecialchars($row['nombre']) . "</td>
        <td>" . htmlspecialchars($row['email']) . "</td>
        <td>" . htmlspecialchars($row['rol_nombre'] ?? 'Sin rol') . "</td>";

    if ($_SESSION['rol'] === 'admin') {
        echo "<td>";

        // Editar
        echo "<a href='asignar_roles.php?id={$row['id']}' class='btn me-2' style='background-color: #0d6efd; color: white; font-weight: bold;'>Editar</a>";

	        // Eliminar

	        // Eliminar
        if ($row['id'] !== $_SESSION['usuario_id']) {
            echo "<a href='?eliminar={$row['id']}' class='btn btn-danger' style='font-weight: bold;' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este usuario?\");'>Eliminar</a>";
        }

        echo "</td>";
    }

    echo "</tr>";
}

echo "</table>";

// Botones centrados y alineados horizontalmente
echo "<div style='text-align: center; margin-top: 20px;'>";

if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico') {
    echo "<a href='registro.php' class='btn btn-success me-2' style='min-width: 180px; font-weight: bold; font-size:1.2em; margin-top: 10px;'>Agregar nuevo usuario</a>";
}

echo "<a href='../panel.php' class='btn btn-secondary' style='min-width: 180px; font-weight: bold; font-size:1.2em; margin-top: 10px;'>Volver al panel</a>";
echo "</div>";

echo '</div>'; // panel-container
include_once '../includes/aside.php';
echo '</div>'; // contenido-flex
include '../includes/footer.php';

?>

<script>
  const alertEl = document.querySelector('.alert-success');
  if (alertEl) {
    setTimeout(() => {
      alertEl.style.transition = 'opacity 0.5s ease';
      alertEl.style.opacity = '0';
      setTimeout(() => alertEl.remove(), 500);
    }, 1500);
  }
</script>

