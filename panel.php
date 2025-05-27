<?php
session_start();
include 'includes/auth.php';
include 'includes/header.php';

?>

<div class= 'contenido-flex'>
	<div class="panel-container">
    <h2 class="bienvenida">Bienvenido <?= htmlspecialchars($_SESSION['nombre']) ?> al panel de navegación</h2>


    <div class="panel-grid">
	<?php $rol = $_SESSION['rol']; ?>



	<?php if ($rol === 'admin'): ?>
            <a href="usuarios/listar.php" class="panel-card">📇 Usuarios</a>
            <a href="dispositivos/listar.php" class="panel-card">🖥️ Dispositivos</a>
            <a href="incidencias/listar.php" class="panel-card">🚨 Incidencias</a>
            <a href="servicios/listar.php" class="panel-card">🛠️ Servicios</a>
            <a href="tareas/listar.php" class="panel-card">📋 Tareas</a>
            <a href="logs/accesos.php" class="panel-card">📜 Logs</a>
            <a href="estadisticas.php" class="panel-card">📊 Estadísticas</a>
        <?php elseif ($rol === 'tecnico'): ?>
            <a href="dispositivos/listar.php" class="panel-card">🖥️ Dispositivos</a>
            <a href="tareas/listar.php" class="panel-card">📋 Mis Tareas</a>
            <a href="incidencias/listar.php" class="panel-card">🚨 Incidencias</a>
            <a href="servicios/listar.php" class="panel-card">🛠️ Servicios</a>
            <a href="usuarios/listar.php" class="panel-card">📇 Ver Usuarios</a>
        <?php elseif ($rol === 'usuario'): ?>
            <a href="incidencias/crear.php" class="panel-card">📝 Crear Incidencia</a>
            <a href="incidencias/listar.php" class="panel-card">📋 Mis Incidencias</a>
        <?php endif; ?>

        <a href="usuarios/perfil.php" class="panel-card">🏷️ Mi Perfil</a>
        <a href="buscar.php" class="panel-card">🔍 Buscar</a>
    </div>

    <!-- Botón de cerrar sesión centrado debajo -->
    <div class="logout-container">
        <a href="logout.php" class="panel-card logout">🔓 Cerrar sesión</a>
    </div>
</div>

<?php include_once __DIR__ . '/includes/aside.php'; ?>

</div>

<?php include 'includes/footer.php'; ?>
