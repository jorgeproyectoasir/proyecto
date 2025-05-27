<?php
include '../includes/auth.php';
include '../includes/header.php';
include '../conexion.php';
require_once '../includes/log.php';

session_start();

// Verificación de permisos: solo admins pueden asignar roles
if ($_SESSION['rol'] !== 'admin') {
    echo "<p class='alert alert-danger'>❌ Acceso denegado.</p>";
    include '../includes/footer.php';
    exit();
}

// Procesar cambio de rol
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_id = intval($_POST['usuario_id']);
    $nuevo_rol = intval($_POST['rol_id']);

    $stmt = $conexion->prepare("UPDATE usuarios SET rol_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $nuevo_rol, $usuario_id);

    if ($stmt->execute()) {
        registrar_log($conexion, $_SESSION["usuario_id"], 'Cambió el rol del usuario ID ' . $usuario_id);

        // Mostrar mensaje y redirigir con retraso de 2 segundos
        echo "<div class='alert alert-success text-center'>✅ Rol actualizado correctamente. Redirigiendo...</div>";
        echo "<script>
                setTimeout(() => { window.location.href = 'asignar_roles.php'; }, 750);
              </script>";
        exit();
    } else {
        echo "<div class='alert alert-danger text-center'>❌ Error al actualizar el rol.</div>";
    }

    $stmt->close();
}

// Obtener usuarios
$usuarios = $conexion->query("SELECT u.id, u.nombre, u.email, r.nombre AS rol
                              FROM usuarios u
                              LEFT JOIN roles r ON u.rol_id = r.id
                              ORDER BY u.id DESC
                              LIMIT 15");


// Obtener roles
$roles = $conexion->query("SELECT * FROM roles");
$lista_roles = [];
while ($r = $roles->fetch_assoc()) {
    $lista_roles[$r['id']] = $r['nombre'];
}
?>

<h2>Asignar roles a usuarios</h2>
<style>
  h2 {
    font-size: 3rem;
    font-weight: 900;
  }
</style>


<table class="table-accesos">
<tr><th>Nombre</th><th>Email</th><th>Rol actual</th><th>Nuevo rol</th><th>Acción</th></tr>
<?php while ($u = $usuarios->fetch_assoc()): ?>
<tr>
    <form method="POST">
        <td><?= htmlspecialchars($u['nombre']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= htmlspecialchars($u['rol']) ?></td>
        <td>
            <select name="rol_id" class="form-select">
                <?php foreach ($lista_roles as $id => $nombre): ?>
                    <option value="<?= $id ?>" <?= $u['rol'] === $nombre ? 'selected' : '' ?>>
                        <?= htmlspecialchars(ucfirst($nombre)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input type="hidden" name="usuario_id" value="<?= $u['id'] ?>">
            <button type="submit" class="btn btn-primary btn-sm">Actualizar</button>
        </td>
    </form>
</tr>
<?php endwhile; ?>
</table>

<div style="text-align: center; margin-top: 20px;">
    <a href="listar.php" class="btn btn-secondary mt-3" style="width: 100px; display: inline-flex; justify-content: center; align-items: center; font-size: 20px;">Volver</a>
</div>

<?php include '../includes/footer.php'; ?>

