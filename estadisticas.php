<?php
include 'includes/auth.php';
include 'includes/header.php';
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
    .graficos-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        margin-top: 30px;
        justify-items: center;
    }
    .chart-container {
        width: 100%;
        max-width: 700px;
        height: auto;
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        text-align: center;
    }
    canvas {
        max-width: 100% !important;
        max-height: 300px !important;
    }
</style>";

require_once 'conexion.php';
require_once 'includes/log.php';

if ($_SESSION['rol'] !== 'admin') {
    echo "<p class='alert alert-danger'>Acceso denegado.</p>";
    include '../includes/footer.php';
    exit();
}

registrar_log($conexion, $_SESSION["usuario_id"], "Accedió al panel de estadísticas");

// Datos de estadísticas
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

$tareas_estado = ['pendiente' => 0, 'completada' => 0];
$sql = "SELECT estado, COUNT(*) AS cantidad FROM tareas GROUP BY estado";
$resultado = $conexion->query($sql);
while ($row = $resultado->fetch_assoc()) {
    $tareas_estado[$row['estado']] = $row['cantidad'];
}

$servicios_estado = ['activo' => 0, 'inactivo' => 0, 'error' => 0];
$sql = "SELECT estado, COUNT(*) AS cantidad FROM servicios GROUP BY estado";
$resultado = $conexion->query($sql);
while ($row = $resultado->fetch_assoc()) {
    $servicios_estado[$row['estado']] = $row['cantidad'];
}

$dispositivos_por_tipo = [];
$sql = "SELECT tipo, COUNT(*) AS cantidad FROM dispositivos GROUP BY tipo";
$resultado = $conexion->query($sql);
while ($row = $resultado->fetch_assoc()) {
    $dispositivos_por_tipo[$row['tipo']] = $row['cantidad'];
}

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

$roles = $conexion->query("SELECT r.nombre, COUNT(u.id) AS cantidad
                           FROM roles r
                           LEFT JOIN usuarios u ON r.id = u.rol_id
                           GROUP BY r.nombre");
$datos_roles = [];
while ($r = $roles->fetch_assoc()) {
    $datos_roles[$r['nombre']] = $r['cantidad'];
}
?>

<h2 class="titulos">Estadísticas generales</h2>
<div class="graficos-grid">
    <div class="chart-container">
        <h3>Distribución de usuarios por rol</h3>
        <canvas id="graficoRoles"></canvas>
    </div>
    <div class="chart-container">
        <h3>Evolución de incidencias (últimos 7 días)</h3>
        <canvas id="graficoIncidencias"></canvas>
    </div>
    <div class="chart-container">
        <h3>Tareas por estado</h3>
        <canvas id="graficoTareas"></canvas>
    </div>
    <div class="chart-container">
        <h3>Servicios activos/inactivos/error</h3>
        <canvas id="graficoServicios"></canvas>
    </div>
    <div class="chart-container">
        <h3>Dispositivos por tipo</h3>
        <canvas id="graficoDispositivos"></canvas>
    </div>
    <div class="chart-container">
        <h3>Accesos al sistema (últimos 7 días)</h3>
        <canvas id="graficoAccesos"></canvas>
    </div>
</div>
<div class="botones-centrados">
<a href="../panel.php" class="btn btn-secondary mt-4" style='font-weight:bold'>Volver al panel</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const fontSize = 16;

const coloresAleatorios = (cantidad) => {
    const colores = [];
    for (let i = 0; i < cantidad; i++) {
        const color = `hsl(${Math.floor(Math.random() * 360)}, 70%, 60%)`;
        colores.push(color);
    }
    return colores;
};

new Chart(document.getElementById('graficoRoles').getContext('2d'), {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_keys($datos_roles)) ?>,
        datasets: [{
            label: 'Usuarios por rol',
            data: <?= json_encode(array_values($datos_roles)) ?>,
            backgroundColor: coloresAleatorios(<?= count($datos_roles) ?>),
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { font: { size: fontSize } }
            }
        }
    }
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
    options: {
        responsive: true,
        plugins: {
            legend: {
                labels: { font: { size: fontSize } }
            }
        },
        scales: {
            x: { ticks: { font: { size: fontSize } } },
            y: { beginAtZero: true, ticks: { font: { size: fontSize } } }
        }
    }
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
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { font: { size: fontSize } }
            }
        }
    }
});

new Chart(document.getElementById('graficoServicios').getContext('2d'), {
    type: 'bar',
    data: {
        labels: ['Activos', 'Inactivos', 'Error'],
        datasets: [{
            label: 'Servicios',
            data: <?= json_encode([
                $servicios_estado['activo'],
                $servicios_estado['inactivo'],
                $servicios_estado['error']
            ]) ?>,
            backgroundColor: ['#17a2b8', '#6c757d', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false,
                labels: { font: { size: fontSize } }
            }
        },
        scales: {
            x: { ticks: { font: { size: fontSize } } },
            y: { beginAtZero: true, ticks: { font: { size: fontSize } } }
        }
    }
});
new Chart(document.getElementById('graficoDispositivos').getContext('2d'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_keys($dispositivos_por_tipo)) ?>,
        datasets: [{
            label: 'Dispositivos por tipo',
            data: <?= json_encode(array_values($dispositivos_por_tipo)) ?>,
            backgroundColor: coloresAleatorios(<?= count($dispositivos_por_tipo) ?>)
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                labels: { font: { size: fontSize } }
            }
        },
        scales: {
            x: { ticks: { font: { size: fontSize } } },
            y: { beginAtZero: true, ticks: { font: { size: fontSize } } }
        }
    }
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
    options: {
        responsive: true,
        plugins: {
            legend: {
                labels: { font: { size: fontSize } }
            }
        },
        scales: {
            x: { ticks: { font: { size: fontSize } } },
            y: { beginAtZero: true, ticks: { font: { size: fontSize } } }
        }
    }
});
</script>
<?php include 'includes/footer.php'; ?>
