<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header("Location: panel.php");
    exit();
}

session_regenerate_id(true);
$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
$_SESSION['navegador'] = $_SERVER['HTTP_USER_AGENT'];

include 'includes/header.php';
?>

<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['flash']['mensaje'] ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<?php if (isset($_GET['expirado']) && $_GET['expirado'] == 1): ?>
    <div class="alert">⚠️ Tu sesión ha expirado por inactividad. Inicia sesión nuevamente.</div>
<?php endif; ?>

<div class="login-container">
    <div class="login-image">
        <img src="img/index.jpg" alt="Plataforma IT" class="img-lateral">
    </div>

    <div>

        <div>
            <h1 class="titulo-principal">Plataforma IT</h1>
            <h2 class="login-subtitulo">Iniciar Sesión</h2>
        </div>

        <form action="usuarios/login.php" method="POST" class="login-form">
    <div>
        <label for="email">Correo electrónico:</label>
        <input type="email" name="email" id="email" class="form-control" required>
    </div>

    <div>
        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>

    <!-- Contenedor para los botones -->
    <div class="button-container">
        <button type="button" id="togglePassword" class="btn btn-secondary mt-4">Mostrar</button>
        <button type="submit" class="btn btn-primary mb-3">Iniciar sesión</button>
    </div>
</form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
