<?php
// includes/historial.php

function registrar_historial(mysqli $conexion, int $usuario_id, string $entidad, int $entidad_id, string $accion): void {
    $stmt = $conexion->prepare("INSERT INTO historial (usuario_id, entidad, entidad_id, accion) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $usuario_id, $entidad, $entidad_id, $accion);
    $stmt->execute();
    $stmt->close();
}
