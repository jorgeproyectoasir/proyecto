<?php
function registrar_log($conn, $usuario_id, $accion, $descripcion = "") {
    $stmt = $conn->prepare("INSERT INTO logs (usuario_id, accion, descripcion) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $usuario_id, $accion, $descripcion);
    $stmt->execute();
    $stmt->close();
}
?>

