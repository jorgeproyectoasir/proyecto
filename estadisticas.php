<?php
include 'includes/auth.php';
include 'includes/header.php';
require_once 'conexion.php';
require_once 'includes/log.php';

if ($_SESSION['rol'] !== 'admin') {
    echo "<p class='alert alert-danger'>Acceso denegado.</p>";
    include '../includes/footer.php';
    exit();
}

registrar_log($conexion, $_SESSION["usuario_id"], "Accedió al panel de estadísticas");

// Incidencias por día (últimos 7 días)
$datos_incidencias = [];
$hoy = date('Y-m-d');
for ($i = 6; $i >= 0; $i--) {
    $fecha = date('Y-m-d', strtotime("$hoy -$i days"));
    $stmt = $conexion->prepare("SELECT COUNT(*) FROM incidencias WHERE DATE(fecha) = ?");
    $stmt->bind_param("s", $fecha);
    $stmt->execute();
    $count = $stmt->get_result()->fetch_row()[0];
    $datos_incidencias[$fecha] = $count;
}

// Tareas por estado
$tareas_estado = ['pendiente' => 0, 'completada' => 0];
$sql = "SELECT estado, COUNT(*) AS cantidad FROM tareas GROUP BY estado";
$resultado = $conexion->query($sql);
while ($row = $resultado->fetch_assoc()) {
    $tareas_estado[$row['estado']] = $row['cantidad'];
}

// Servicios activos e inactivos
$servicios_estado = ['activo' => 0, 'inactivo' => 0];
$sql = "SELECT estado, COUNT(*) AS cantidad FROM servicios GROUP BY estado";
$resultado = $conexion->query($sql);
while ($row = $resultado->fetch_assoc()) {
    $servicios_estado[$row['estado']] = $row['cantidad'];
}

// Dispositivos por tipo
$dispositivos_por_tipo = [];
$sql = "SELECT tipo, COUNT(*) AS cantidad FROM dispositivos GROUP BY tipo";
$resultado = $conexion->query($sql);
while ($row = $resultado->fetch_assoc()) {
    $dispositivos_por_tipo[$row['tipo']] = $row['cantidad'];
}

// Accesos por día (solo logins)
$accesos_dia = [];
$sql = "SELECT DATE(fecha) AS dia, COUNT(*) AS cantidad
        FROM logs
        WHERE accion LIKE '%Inicio de sesión%'
        GROUP BY dia
        ORDER BY dia DESC
        LIMIT 7";
$resultado = $conexion->query($sql);
while ($row = $resultado->fetch_assoc()) {
    $accesos_dia[$row['dia']] = $row['cantidad'];
}
$accesos_dia = array_reverse($accesos_dia);

// Totales
$total_usuarios = $conexion->query("SELECT COUNT(*) FROM usuarios")->fetch_row()[0];
$total_dispositivos = $conexion->query("SELECT COUNT(*) FROM dispositivos")->fetch_row()[0];
$total_incidencias = $conexion->query("SELECT COUNT(*) FROM incidencias")->fetch_row()[0];
$incidencias_abiertas = $conexion->query("SELECT COUNT(*) FROM incidencias WHERE estado = 'abierta'")->fetch_row()[0];
$total_tareas = $conexion->query("SELECT COUNT(*) FROM tareas")->fetch_row()[0];
$tareas_pendientes = $conexion->query("SELECT COUNT(*) FROM tareas WHERE estado = 'pendiente'")->fetch_row()[0];
$total_servicios = $conexion->query("SELECT COUNT(*) FROM servicios")->fetch_row()[0];
$servicios_activos = $conexion->query("SELECT COUNT(*) FROM servicios WHERE estado = 'activo'")->fetch_row()[0];

// Usuarios por rol
$roles = $conexion->query("SELECT r.nombre, COUNT(u.id) AS cantidad
                           FROM roles r
                           LEFT JOIN usuarios u ON r.id = u.rol_id
                           GROUP BY r.nombre");
$datos_roles = [];
while ($r = $roles->fetch_assoc()) {
    $datos_roles[$r['nombre']] = $r['cantidad'];
}
?>

<h2>Estadísticas generales</h2>
<div class="panel-container">
    <div class="panel-grid">
        <div class="panel-card">Usuarios: <strong><?= $total_usuarios ?></strong></div>
        <div class="panel-card">Dispositivos: <strong><?= $total_dispositivos ?></strong></div>
        <div class="panel-card">Incidencias: <strong><?= $total_incidencias ?> (<?= $incidencias_abiertas ?> abiertas)</strong></div>
        <div class="panel-card">Tareas: <strong><?= $total_tareas ?> (<?= $tareas_pendientes ?> pendientes)</strong></div>
        <div class="panel-card">Servicios: <strong><?= $total_servicios ?> (<?= $servicios_activos ?> activos)</strong></div>
    </div>
</div>

<h3 class="mt-5">Distribución de usuarios por rol</h3>
<canvas id="graficoRoles" width="400" height="200"></canvas>

<h3 class="mt-5">Evolución de incidencias (últimos 7 días)</h3>
<canvas id="graficoIncidencias" width="600" height="200"></canvas>

<h3 class="mt-5">Tareas por estado</h3>
<canvas id="graficoTareas" width="400" height="200"></canvas>

<h3 class="mt-5">Servicios activos/inactivos</h3>
<canvas id="graficoServicios" width="400" height="200"></canvas>

<h3 class="mt-5">Dispositivos por tipo</h3>
<canvas id="graficoDispositivos" width="400" height="200"></canvas>

<h3 class="mt-5">Accesos al sistema (últimos 7 días)</h3>
<canvas id="graficoAccesos" width="400" height="200"></canvas>

<a href="../panel.php" class="btn btn-secondary mt-4">Volver al panel</a>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('graficoRoles').getContext('2d'), {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_keys($datos_roles)) ?>,
        datasets: [{
            label: 'Usuarios por rol',
            data: <?= json_encode(array_values($datos_roles)) ?>,
            backgroundColor: ['#007bff', '#28a745', '#ffc107'],
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('graficoIncidencias').getContext('2d'), {
    type: 'line',
    data: {
        labels: <?= json_encode(array_keys($datos_incidencias)) ?>,
        datasets: [{
            label: 'Incidencias creadas',
            data: <?= json_encode(array_values($datos_incidencias)) ?>,
            fill: false,
            borderColor: '#007bff',
            tension: 0.1
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('graficoTareas').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Pendientes', 'Completadas'],
        datasets: [{
            label: 'Tareas',
            data: <?= json_encode(array_values($tareas_estado)) ?>,
            backgroundColor: ['#ffc107', '#28a745']
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('graficoServicios').getContext('2d'), {
    type: 'bar',
    data: {
        labels: ['Activos', 'Inactivos'],
        datasets: [{
            label: 'Servicios',
            data: <?= json_encode(array_values($servicios_estado)) ?>,
            backgroundColor: ['#17a2b8', '#6c757d']
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('graficoDispositivos').getContext('2d'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_keys($dispositivos_por_tipo)) ?>,
        datasets: [{
            label: 'Dispositivos por tipo',
            data: <?= json_encode(array_values($dispositivos_por_tipo)) ?>,
            backgroundColor: '#6610f2'
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('graficoAccesos').getContext('2d'), {
    type: 'line',
    data: {
        labels: <?= json_encode(array_keys($accesos_dia)) ?>,
        datasets: [{
            label: 'Accesos por día',
            data: <?= json_encode(array_values($accesos_dia)) ?>,
            borderColor: '#dc3545',
            backgroundColor: 'rgba(220,53,69,0.2)',
            tension: 0.3
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});
</script>

<?php include 'includes/footer.php'; ?>

