<?php
include '../includes/auth.php';
include '../includes/header.php';
require_once '../includes/log.php';

include '../conexion.php';

$es_admin = $_SESSION['rol'] === 'admin';

if (!$es_admin && $_SESSION['rol'] !== 'tecnico') {
    echo "Acceso denegado.";
    include '../includes/footer.php';
    exit();
}

// Solo admin puede eliminar
if ($es_admin && isset($_GET['eliminar']) && $_GET['eliminar'] != $_SESSION['usuario_id']) {
    $id = intval($_GET['eliminar']);
    $conexion->query("DELETE FROM usuarios WHERE id = $id");
    echo "<p class='text-success'>Usuario eliminado.</p>";
}

// Obtener usuarios con roles
$sql = "SELECT usuarios.id, usuarios.nombre, email, roles.nombre AS rol
        FROM usuarios
        JOIN roles ON usuarios.rol_id = roles.id";
$resultado = $conexion->query($sql);
?>

<h2>Gestión de Usuarios</h2>

<table class='table table-striped'>
    <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Acción</th></tr>
    <?php while ($fila = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?= $fila['id'] ?></td>
            <td><?= htmlspecialchars($fila['nombre']) ?></td>
            <td><?= htmlspecialchars($fila['email']) ?></td>
            <td><?= htmlspecialchars(ucfirst($fila['rol'])) ?></td>
            <td>
                <?php if ($es_admin): ?>
                    <?php if ($fila['id'] != $_SESSION['usuario_id']): ?>
                        <a href='listar.php?eliminar=<?= $fila['id'] ?>' class='btn btn-danger btn-sm eliminar'>Eliminar</a>
                    <?php else: ?>
                        <span class='text-muted'>Tú mismo</span>
                    <?php endif; ?>
                <?php else: ?>
                    <span class='text-muted'>Sin permiso</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<a href='../panel.php' class='btn btn-secondary'>Volver</a>

<?php if ($es_admin): ?>
    <a href="registro.php" class="btn btn-success mt-3">Registrar nuevo usuario</a>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>

