<?php

class Prestamo
{
    // Obtiene todos los datos de un préstamo con sus dispositivos asociados y datos del usuario
    public static function obtenerPorId($id)
    {
        require 'config/database.php';

        // 1. Consultar los datos del préstamo y el usuario
        $stmt = $conn->prepare("
            SELECT p.*, u.nombre, u.email
            FROM prestamos p
            JOIN usuarios u ON p.usuario_id = u.id
            WHERE p.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $prestamo = $stmt->get_result()->fetch_assoc();

        if (!$prestamo) {
            return false;
        }

        // 2. Consultar los dispositivos asociados al préstamo
        $stmt2 = $conn->prepare("
            SELECT d.tipo, d.etiqueta_empresa, d.marca, d.modelo
            FROM prestamos_dispositivos pd
            JOIN dispositivos d ON pd.dispositivo_id = d.id
            WHERE pd.prestamo_id = ?
        ");
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $prestamo['dispositivos'] = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

        return $prestamo;
    }
}
