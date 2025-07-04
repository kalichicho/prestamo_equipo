<?php

class Usuario
{
    // Buscar un usuario en la base de datos por su correo electrónico
    // Retorna un array asociativo con los datos del usuario si existe
    public static function buscarPorEmail($email)
    {
        require 'config/database.php';
        global $conn; // Asegura el uso de la conexión global

        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Obtener todos los usuarios con su id, nombre y email
    // Utilizado en la vista para seleccionar usuarios disponibles
    public static function obtenerUsuarios()
    {
        require 'config/database.php';
        $sql = "SELECT id, nombre, email FROM usuarios";
        return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
}
