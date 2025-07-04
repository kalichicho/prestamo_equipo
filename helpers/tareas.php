<?php
// 📌 Registra una nueva tarea pendiente
function registrarTareaPendiente($tipo, $dispositivo_id, $usuario_id, $prestamo_id) {
    require 'config/database.php';
    $stmt = $conn->prepare("INSERT INTO tareas_pendientes (tipo, dispositivo_id, usuario_id, fecha, revisado, prestamo_id) VALUES (?, ?, ?, NOW(), 0, ?)");
    $stmt->bind_param("siii", $tipo, $dispositivo_id, $usuario_id, $prestamo_id);
    $stmt->execute();
}

?>