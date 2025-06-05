<?php
// Solo aplicar configuración si la sesión no está activa
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS'])); // Solo si HTTPS está activo

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}
// -----------------------------
// Expiración por inactividad
// -----------------------------
$tiempo_limite = 900; // 15 minutos
if (isset($_SESSION['ultima_actividad']) && (time() - $_SESSION['ultima_actividad']) > $tiempo_limite) {
    session_unset();
    session_destroy();
	$host = $_SERVER['HTTP_HOST'];
header("Location: https://$host/index.php");
    exit();
}


$_SESSION['ultima_actividad'] = time();
// -----------------------------
// Validación opcional: IP y navegador
// -----------------------------
// Para mayor seguridad puedes activarlo

if (!isset($_SESSION['ip']) || $_SESSION['ip'] !== $_SERVER['REMOTE_ADDR'] ||
    !isset($_SESSION['navegador']) || $_SESSION['navegador'] !== $_SERVER['HTTP_USER_AGENT']) {
    session_unset();
    session_destroy();
    header("Location: /proyecto/index.php");
    exit();
}
// -----------------------------
// Redirección si no ha iniciado sesión
// -----------------------------
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /proyecto/index.php");
    exit();
}
