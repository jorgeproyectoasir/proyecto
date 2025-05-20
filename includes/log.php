<?php
function registrar_log($conexion, $usuario_id, $accion, $entidad = null, $detalle = null) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'desconocido';

    $stmt = $conexion->prepare("INSERT INTO logs (usuario_id, ip, user_agent, accion, entidad_afectada, detalle_extra) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $usuario_id, $ip, $user_agent, $accion, $entidad, $detalle);
    
    if (!$stmt->execute()) {
        error_log("Error en registrar_log: " . $stmt->error);
    }
}

?>

