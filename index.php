<?php
session_start();

// Si ya inició sesión, redirigir
if (isset($_SESSION['usuario_id'])) {
    header("Location: panel.php");
    exit();
}

include 'includes/header.php';
?>

<h2>Iniciar Sesión</h2>


<form action="usuarios/login.php" method="POST">
    <div class="mb-3">
        <label>Email:</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Contraseña:</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Entrar</button>
</form>

<?php include 'includes/footer.php'; ?>

