<?php
include '../includes/auth.php';
include '../conexion.php';
require_once '../includes/log.php';

registrar_log($conexion, $_SESSION["usuario_id"], "Consultó tareas programadas");

$rol = $_SESSION['rol'];
$usuario_id = $_SESSION['usuario_id'];

// Mensajes de éxito o error
$mensaje = '';
$tipo_mensaje = 'success';

if (isset($_GET['creada'])) {
    $mensaje = "Tarea creada correctamente.";
} elseif (isset($_GET['editada'])) {
    $mensaje = "Tarea editada correctamente.";
} elseif (isset($_GET['completada'])) {
    $mensaje = "Tarea marcada como completada.";
} elseif (isset($_GET['eliminada'])) {
    $mensaje = "Tarea eliminada correctamente.";
} elseif (isset($_GET['error'])) {
    $mensaje = "Ha ocurrido un error. Intenta de nuevo.";
    $tipo_mensaje = 'danger';
}

// Completar tarea
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['completar_id'])) {
    $id = intval($_POST['completar_id']);
    $stmt = $conexion->prepare("UPDATE tareas SET estado = 'completada' WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        registrar_log($conexion, $usuario_id, "Completó tarea ID $id");
        header("Location: listar.php?completada=1");
        exit();
    } else {
        header("Location: listar.php?error=1");
        exit();
    }
}

// Consultar tareas
if ($rol === 'admin' || $rol === 'tecnico') {
    $sql = "SELECT tareas.*, usuarios.nombre AS tecnico, tareas.creador_id FROM tareas
            LEFT JOIN usuarios ON tareas.tecnico_id = usuarios.id
            ORDER BY programada_para ASC";
    $resultado = $conexion->query($sql);
} else {
    $sql = "SELECT * FROM tareas WHERE tecnico_id = ? ORDER BY programada_para ASC";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
}
?>

<?php include '../includes/header.php'; ?>

<div class="contenido-flex">
    <div class="panel-container">
        <h2>Tareas programadas</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?= $tipo_mensaje ?> mt-3">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <table class="table-accesos">
            <tr>
                <th>Título</th>
                <th>Descripción</th>
                <th>Fecha</th>
                <th>Estado</th>
                <?php if ($rol === 'admin'): ?>
                    <th>Técnico</th>
                <?php endif; ?>
                <th>Acciones</th>
            </tr>

            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($fila['titulo']) ?></td>
                    <td><?= htmlspecialchars($fila['descripcion']) ?></td>
                    <td><?= $fila['programada_para'] ?></td>
                    <td>
                        <?= $fila['estado'] === 'completada'
                            ? "<span class='badge bg-success'>Completada</span>"
                            : "<span class='badge bg-warning text-dark'>Pendiente</span>" ?>
                    </td>

                    <?php if ($rol === 'admin'): ?>
                        <td><?= htmlspecialchars($fila['tecnico']) ?></td>
                    <?php endif; ?>

                    <td>
                        <?php
                        $creador_id = $fila['creador_id'] ?? null;
                        $puede_editar = (
                            $rol === 'admin' ||
                            ($rol === 'tecnico' && $fila['tecnico_id'] == $usuario_id) ||
                            ($rol === 'usuario' && $creador_id == $usuario_id)
                        );
                        ?>

                        <?php if ($puede_editar): ?>
                            <a href="editar.php?id=<?= $fila['id'] ?>" class="btn btn-warning btn-sm me-1">Editar</a>
                        <?php endif; ?>

                        <a href="comentarios.php?id=<?= $fila['id'] ?>" class="btn btn-info btn-sm me-1">Comentarios</a>

                        <?php if ($fila['estado'] === 'completada'): ?>
                            <a href="eliminar.php?id=<?= $fila['id'] ?>" class="btn btn-danger btn-sm eliminar">Eliminar</a>
                        <?php endif; ?>

                        <?php if ($fila['estado'] === 'pendiente' && ($rol === 'admin' || $rol === 'tecnico')): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="completar_id" value="<?= $fila['id'] ?>">
                                <button type="submit" class="btn btn-success btn-sm">✔ Completar</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <?php if ($rol === 'admin' || $rol === 'tecnico'): ?>
            <a href='crear.php' class='btn btn-success mt-2'>Crear nueva tarea</a>
        <?php endif; ?>

        <br><a href='../panel.php' class='btn btn-secondary mt-3'>Volver al panel</a>
    </div>

    <?php include_once '../includes/aside.php'; ?>
</div>

<?php include '../includes/footer.php'; ?>
