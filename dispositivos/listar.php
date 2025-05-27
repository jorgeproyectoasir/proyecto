<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../includes/auth.php';
include '../includes/header.php';

echo "<style>
    h2 {
        font-size: 3rem;
        font-weight: 900;
    }
    .table-wrapper {
        overflow-x: auto;
        max-width: 100%;
        margin-top: 20px;
    }
    .table-accesos {
        font-size: 1em;
	padding: 0px;
        min-width: 700px;
        border-collapse: collapse;
    }
    .table-accesos th {
        background-color: #004085;
        color: white;
        padding: 0px;
        text-align: left;
    }
    .table-accesos td {
        padding: 5px;
        border: 1px solid #ccc;
    }
    .table-accesos tr:nth-child(even) {
        background-color: #d6e9f9;
    }
    .btn {
        font-size: 1.1rem;
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
        <h2>Listado de Dispositivos</h2>

        <?php
        // Eliminar dispositivo (solo admin o técnico)
        if (
            isset($_GET['eliminar']) &&
            ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico')
        ) {
            $id = intval($_GET['eliminar']);
            $conexion->query("DELETE FROM dispositivos WHERE id = $id");
            echo "<p class='text-success'>Dispositivo eliminado correctamente.</p>";
        }

        // Obtener dispositivos
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
                                <a href='editar.php?id=<?= $row['id'] ?>' class='btn btn-warning btn-sm' style="background-color: #0d6efd; border: none; color: white;";>Editar</a>
                                <a href='listar.php?eliminar=<?= $row['id'] ?>' class='btn btn-danger btn-sm'>Eliminar</a>
                            <?php else: ?>
                                <span class="text-muted">Sin permiso</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <?php if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico'): ?>
            <a href='agregar.php' class='btn btn-success mt-3'>Agregar nuevo dispositivo</a>
        <?php endif; ?>

        <br>
        <a href='../panel.php' class='btn btn-secondary mt-3' style="width: 100px;">Volver</a>
    </div>

<?php include_once __DIR__ . '/../includes/aside.php'; ?>
</div>

<?php include '../includes/footer.php'; ?>
