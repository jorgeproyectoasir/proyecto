<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../includes/auth.php';
include '../includes/header.php';

echo "<style>
    html, body {
        margin: 0;
        padding: 0;
        background-color: #B0D0FF;
    }

    .contenido-flex {
        display: flex;
        align-items: flex-start;
        gap: 30px;
    }

    .panel-container {
        flex: 1;
    }

    .aside-estandar {
        width: 300px;
        max-width: 100%;
        flex-shrink: 0;
        background-color: #f9f9f9;
        padding: 20px;
        font-size: 25px;
        border-radius: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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

    .titulos {
        text-align: center;
        font-size: 3.5em;
        font-weight: bold;
        margin-top: 20px;
    }

    .table-wrapper {
        overflow-x: auto;
        max-width: 100%;
        margin-top: 20px;
    }

    .table-accesos {
        width: 100%;
        border-collapse: collapse;
        font-size: 1.3em;
        margin-top: 10px;
    }

    .table-accesos th, .table-accesos td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #ccc;
    }

    .table-accesos tr:nth-child(even) {
        background-color: #d6e9f9;
    }

    .botones-centrados {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
        margin-top: 30px;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }

    .boton-accion {
        min-width: 180px;
        font-weight: bold;
    }
</style>";

if (isset($_SESSION['flash'])) {
    echo "<div class='alert alert-{$_SESSION['flash']['tipo']} text-center'>{$_SESSION['flash']['mensaje']}</div>";
    unset($_SESSION['flash']);
}

require_once '../includes/log.php';
include '../conexion.php';
?>

<div class="contenido-flex">
    <div class="panel-container">
        <h2 class="titulos">Listado de Dispositivos</h2>

        <?php
        if (
            isset($_GET['eliminar']) &&
            ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico')
        ) {
            $id = intval($_GET['eliminar']);
            $conexion->query("DELETE FROM dispositivos WHERE id = $id");
            echo "<div class='alert alert-success'>Dispositivo eliminado correctamente.</div>";
        }

        $sql = "SELECT dispositivos.id, dispositivos.nombre AS dispositivo_nombre, dispositivos.ip, dispositivos.tipo, dispositivos.estado, usuarios.nombre AS responsable
                FROM dispositivos
                LEFT JOIN usuarios ON dispositivos.responsable = usuarios.id";
        $resultado = $conexion->query($sql);
        ?>

        <div class="table-wrapper">
            <table class='table-accesos'>
                <tr>
                    <th>Nombre</th>
                    <th>IP</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Responsable</th>
                    <th>Acciones</th>
                </tr>
                <?php while ($row = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['dispositivo_nombre']) ?></td>
                        <td><?= htmlspecialchars($row['ip']) ?></td>
                        <td><?= htmlspecialchars($row['tipo']) ?></td>
                        <td><?= htmlspecialchars($row['estado']) ?></td>
                        <td><?= htmlspecialchars($row['responsable'] ?? '') ?></td>
                        <td>
                            <?php if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico'): ?>
				<div class="d-flex justify-content-center gap-2">
    <a href="editar.php?id=<?= $row['id'] ?>" class="btn btn-primary fw-bold">Editar</a>
    <a href="eliminar.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar este dispositivo?');">Eliminar</a>
</div>

                            <?php else: ?>
                                <span class="text-muted">Sin permiso</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <div class="botones-centrados">
            <?php if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico'): ?>
                <a href='agregar.php' class='btn btn-success boton-accion' style='font-size:1.2em;'>Agregar nuevo dispositivo</a>
            <?php endif; ?>
            <a href='../panel.php' class='btn btn-secondary boton-accion' style='font-size:1.2em;'>Volver al panel</a>
        </div>
    </div>

    <?php include_once __DIR__ . '/../includes/aside.php'; ?>
</div>

<?php include '../includes/footer.php'; ?>
<script>
document.querySelectorAll('.eliminar-enlace').forEach(function(enlace) {
    enlace.addEventListener('click', function(event) {
        if (!confirm('¿Estás seguro de que quieres eliminar este dispositivo?')) {
            event.preventDefault(); // Solo se cancela si el usuario dice que NO
        }
    });
});
</script>
