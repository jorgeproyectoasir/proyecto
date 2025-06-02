<?php
include '../includes/auth.php';
include '../includes/header.php';

echo "<style>
    html, body {
        margin: 0;
        padding: 0;
        background-color: #B0D0FF;
    }
</style>";

include '../conexion.php';
?>

<div class="contenido-flex">
    <!-- Panel izquierdo -->
    <div class="panel-container">
        <?php
        if (isset($_SESSION['flash'])) {
            echo "<div class='alert alert-{$_SESSION['flash']['tipo']} alert-dismissible fade show text-center'>
                    {$_SESSION['flash']['mensaje']}
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Cerrar'></button>
                  </div>";
            unset($_SESSION['flash']);
        }

        echo "<h2 class='titulos'>Listado de Incidencias</h2>";

        $sql = "SELECT i.*, u.nombre AS usuario, d.nombre AS dispositivo
                FROM incidencias i
                LEFT JOIN usuarios u ON i.usuario_id = u.id
                LEFT JOIN dispositivos d ON i.dispositivo_id = d.id
                ORDER BY i.fecha DESC";
        $resultado = $conexion->query($sql);

        echo "<table class='table-accesos'>
            <tr>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Dispositivo</th>
                <th>Usuario</th>
                <th>Acciones</th>
            </tr>";

        while ($row = $resultado->fetch_assoc()):
        ?>
            <tr>
                <td><?= $row['fecha'] ?></td>
                <td><?= htmlspecialchars($row['descripcion']) ?></td>
                <td><?= htmlspecialchars($row['tipo']) ?></td>
                <td>
                    <span class='badge bg-<?= $row['estado'] === 'cerrada' ? 'secondary' : 'warning' ?>'>
                        <?= ucfirst($row['estado']) ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($row['dispositivo'] ?? 'No asignado') ?></td>
                <td><?= htmlspecialchars($row['usuario'] ?? 'No asignado') ?></td>
                <td>
                    <div class='botones-centrados' style='display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;'>
                        <?php if ($row['estado'] === 'abierta' && ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico')): ?>
                            <a href='cerrar.php?id=<?= $row['id'] ?>' class='btn btn-primary' style='min-width: 140px; font-weight: bold;'>Cerrar</a>
                        <?php endif; ?>

                        <?php if ($row['estado'] === 'cerrada' && ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico')): ?>
                            <a href='eliminar.php?id=<?= $row['id'] ?>' class='btn btn-danger' style='min-width: 140px; font-weight: bold;' onclick="return confirm('¿Estás seguro de que deseas eliminar esta incidencia?');">Eliminar</a>
                        <?php endif; ?>

                        <a href='comentarios.php?id=<?= $row['id'] ?>' class='btn btn-secondary' style='min-width: 140px; font-weight: bold; background-color: #0dcaf0; border: none;'>Comentarios</a>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
        </table>

        <?php
        // Botones al pie de la tabla
        if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico') {
            echo "<div class='botones-centrados' style='display: flex; gap: 15px; justify-content: center; margin-top: 25px; flex-wrap: wrap;'>
                    <a href='crear.php' class='btn btn-success' style='min-width: 180px; font-weight: bold;'>Crear nueva incidencia</a>
                    <a href='../panel.php' class='btn btn-secondary' style='min-width: 180px; font-weight: bold;'>Volver al panel</a>
                  </div>";
        } else {
            echo "<div class='botones-centrados' style='text-align: center; margin-top: 25px;'>
                    <a href='../panel.php' class='btn btn-secondary' style='min-width: 180px; font-weight: bold;'>Volver al panel</a>
                  </div>";
        }
        ?>
    </div>

    <!-- Aside derecho -->
    <?php include_once __DIR__ . '/../includes/aside.php'; ?>
</div>

<?php include '../includes/footer.php'; ?>

