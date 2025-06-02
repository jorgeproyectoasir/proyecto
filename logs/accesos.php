<?php
include '../includes/auth.php';
include '../includes/header.php';
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

include '../conexion.php';
require_once '../includes/log.php';

$rol = $_SESSION['rol'];

if ($rol !== 'admin' && $rol !== 'tecnico') {
    echo "<div class='alert alert-danger'>Acceso denegado.</div>";
    include '../includes/footer.php';
    exit();
}

$sql = "SELECT logs.fecha, usuarios.nombre AS usuario, logs.accion
        FROM logs
        LEFT JOIN usuarios ON logs.usuario_id = usuarios.id
        ORDER BY fecha DESC
        LIMIT 30";
$resultado = $conexion->query($sql);
?>

<div class="contenido-flex">
    <div class="panel-container">
        <h2 class="titulos">Registro de actividad</h2>

        <table class="table-accesos">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php $contador = 1; ?>
                <?php while ($log = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= $contador++ ?></td>
                        <td><?= $log['fecha'] ?></td>
                        <td><?= htmlspecialchars($log['usuario'] ?? 'Desconocido') ?></td>
                        <td><?= htmlspecialchars($log['accion']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="botones-centrados">
            <a href='../panel.php' class='btn btn-secondary mt-3' style='font-weight:bold'>Volver al panel</a>
        </div>
    </div>

    <?php include_once '../includes/aside.php'; ?>
</div>

<?php include '../includes/footer.php'; ?>

