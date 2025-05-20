<?php
session_start();

// Si ya inició sesión, redirigir al panel
if (isset($_SESSION['usuario_id'])) {
    header("Location: panel.php");
    exit();
}
// Tras validar al usuario y antes de redirigir:
session_regenerate_id(true);
$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
$_SESSION['navegador'] = $_SERVER['HTTP_USER_AGENT'];

include 'includes/header.php';
?>
<?php if (isset($_GET['expirado']) && $_GET['expirado'] == 1): ?>
    <div class="alert alert-warning">⚠️ Tu sesión ha expirado por inactividad. Inicia sesión nuevamente.</div>
<?php endif; ?>

<h2 class="mt-4">Iniciar Sesión</h2>

<form action="usuarios/login.php" method="POST" class="mt-3" id="formLogin">
    <div class="mb-3">
        <label for="email">Correo electrónico:</label>
        <input type="email" name="email" id="email" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="password">Contraseña:</label>
        <div style="display: flex; align-items: center; gap: 10px;">
            <input type="password" name="password" id="password" class="form-control" required>
            <button type="button" id="togglePassword" class="btn btn-outline-secondary btn-sm">Mostrar</button>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Iniciar sesión</button>
</form>

<div id="mensaje" class="alert" style="display: none;"></div>

<?php include 'includes/footer.php'; ?>

