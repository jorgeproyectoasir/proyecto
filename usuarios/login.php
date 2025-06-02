<?php
session_start();
require_once '../conexion.php';
require_once '../includes/log.php';

$ip = $_SERVER['REMOTE_ADDR'];

function estaBloqueado($conexion, $email, $ip) {
    $stmt = $conexion->prepare("SELECT intentos, bloqueado_hasta FROM intentos_login WHERE email = ? AND ip = ?");
    $stmt->bind_param("ss", $email, $ip);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($row = $resultado->fetch_assoc()) {
        if ($row['bloqueado_hasta'] && strtotime($row['bloqueado_hasta']) > time()) {
            return true;
        }
    }
    return false;
}

function registrarIntento($conexion, $email, $ip) {
    $stmt = $conexion->prepare("SELECT * FROM intentos_login WHERE email = ? AND ip = ?");
    $stmt->bind_param("ss", $email, $ip);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($row = $resultado->fetch_assoc()) {
        $intentos = $row['intentos'] + 1;
        $bloqueado = $intentos >= 5 ? date('Y-m-d H:i:s', strtotime('+15 minutes')) : null;

        $stmt2 = $conexion->prepare("UPDATE intentos_login SET intentos = ?, ultimo_intento = NOW(), bloqueado_hasta = ? WHERE id = ?");
        $stmt2->bind_param("isi", $intentos, $bloqueado, $row['id']);
        $stmt2->execute();
    } else {
        $intentos = 1;
        $stmt2 = $conexion->prepare("INSERT INTO intentos_login (email, ip, intentos, ultimo_intento) VALUES (?, ?, ?, NOW())");
        $stmt2->bind_param("ssi", $email, $ip, $intentos);
        $stmt2->execute();
    }
}

function limpiarIntentos($conexion, $email, $ip) {
    $stmt = $conexion->prepare("DELETE FROM intentos_login WHERE email = ? AND ip = ?");
    $stmt->bind_param("ss", $email, $ip);
    $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (estaBloqueado($conexion, $email, $ip)) {
        $_SESSION['flash'] = ['tipo' => 'error', 'mensaje' => "❌ Demasiados intentos fallidos. Inténtalo más tarde."];
        header("Location: ../index.php");
        exit();
    } else {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();

            if (password_verify($password, $usuario['contraseña'])) {
                limpiarIntentos($conexion, $email, $ip);
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol'] = obtenerRol($conexion, $usuario['rol_id']);
                registrar_log($conexion, $usuario['id'], 'Inicio de sesión');

                header("Location: ../panel.php");
                exit();
            } else {
                $_SESSION['flash'] = ['tipo' => 'error', 'mensaje' => "❌ Contraseña incorrecta."];
                registrarIntento($conexion, $email, $ip);
                header("Location: ../index.php");
                exit();
            }
        } else {
            $_SESSION['flash'] = ['tipo' => 'error', 'mensaje' => "❌ Usuario no encontrado."];
            registrarIntento($conexion, $email, $ip);
            header("Location: ../index.php");
            exit();
        }
    }
}

function obtenerRol($conexion, $rol_id) {
    $sql = "SELECT nombre FROM roles WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $rol_id);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    return $resultado['nombre'] ?? 'desconocido';
}
?>

