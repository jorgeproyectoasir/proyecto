<?php
include '../includes/auth.php';
include '../includes/header.php';
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
</style>";
include '../conexion.php';
require_once '../includes/log.php';

registrar_log($conexion, $_SESSION["usuario_id"], "Consultó tareas programadas");

$rol = $_SESSION['rol'];
$usuario_id = $_SESSION['usuario_id'];

// Mensajes flash
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

<div class="contenido-flex">
    <div class="panel-container">

        <h2 class="titulos">Listado de Tareas</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?= $tipo_mensaje ?> mt-3">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <table class="table-accesos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <?php if ($rol === 'admin'): ?>
                        <th>Técnico</th>
                    <?php endif; ?>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= $fila['id'] ?></td>
                    <td><?= htmlspecialchars($fila['titulo']) ?></td>
                    <td><?= htmlspecialchars($fila['descripcion']) ?></td>
                    <td><?= $fila['programada_para'] ?></td>
                    <td>
                        <span class="badge <?= $fila['estado'] === 'completada' ? 'bg-secondary' : 'bg-warning' ?>">
                            <?= ucfirst($fila['estado']) ?>
                        </span>
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
                            <a href="editar.php?id=<?= $fila['id'] ?>"
                               class="btn btn-sm me-2"
                               style="background-color: #0d6efd; color: white; font-weight: bold;">Editar</a>
                        <?php endif; ?>

                        <a href="comentarios.php?id=<?= $fila['id'] ?>"
                           class="btn btn-sm me-2"
                           style="background-color: #0dcaf0; color: white; font-weight: bold;">Comentarios</a>

                        <?php if ($fila['estado'] === 'completada' && $rol === 'admin'): ?>
			<a href="eliminar.php?id=<?= $fila['id'] ?>"
			   class="btn btn-danger btn-sm eliminar"
			   style="font-weight: bold;">Eliminar</a>

                        <?php endif; ?>

                        <?php if ($fila['estado'] === 'pendiente' && ($rol === 'admin' || $rol === 'tecnico')): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="completar_id" value="<?= $fila['id'] ?>">
                                <button type="submit" class="btn btn-sm"
                                        style="background-color: #198754; color: white; font-weight: bold;">Completar
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <div style="text-align: center; margin-top: 20px;">
            <?php if ($rol === 'admin' || $rol === 'tecnico'): ?>
                <a href="crear.php" class="btn btn-success me-2" style="min-width: 180px; font-weight: bold;">Crear nueva tarea</a>
            <?php endif; ?>
            <a href="../panel.php" class="btn btn-secondary" style="min-width: 180px; font-weight: bold;">Volver al panel</a>
        </div>

    </div>

    <?php include_once '../includes/aside.php'; ?>
</div>

<?php include '../includes/footer.php'; ?>

