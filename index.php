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

    .botones-centrados {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-top: 20px;
      flex-wrap: nowrap;
    }

    .boton-login {
      width: 140px;
      font-family: Arial, sans-serif;
      font-weight: bold !important;
      white-space: nowrap;
      padding: 10px 0;
      box-sizing: border-box;
      text-align: center;
    }

    #togglePassword {
      color: #fff;
      background-color: #6c757d;
      border: none;
    }

    #togglePassword:hover {
      background-color: #5a6268;
    }
  </style>";
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
<script>
  const toggleBtn = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('password');
  const icon = document.getElementById('toggleIcon');

  toggleBtn.addEventListener('click', () => {
    const tipo = passwordInput.type === 'password' ? 'text' : 'password';
    passwordInput.type = tipo;
    icon.classList.toggle('bi-eye');
    icon.classList.toggle('bi-eye-slash');
  });
</script>

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
  <div class="mb-3">
    <label for="email" class="form-label">Correo electrónico:</label>
    <input type="email" name="email" id="email" class="form-control" required>
  </div>

  <div class="mb-3">
    <label for="password" class="form-label">Contraseña:</label>
    <div class="input-group">
      <input type="password" name="password" id="password" class="form-control" required>
      <button type="button" class="btn btn-secondary" id="togglePassword" tabindex="-1">
        <i class="bi bi-eye" id="toggleIcon"></i>
      </button>
    </div>
  </div>

  <div class="botones-centrados">
    <button type="submit" class="btn btn-primary boton-login">Iniciar sesión</button>
  </div>
</form>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
