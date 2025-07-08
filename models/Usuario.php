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

    // Comprueba si existe un usuario con el email indicado
    public static function existeEmail($email)
    {
        require 'config/database.php';
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    // Crea un nuevo usuario en la base de datos
    public static function crear($nombre, $email, $contrasena, $rol)
    {
        require 'config/database.php';
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);
        $stmt = $conn->prepare(
            "INSERT INTO usuarios (nombre, email, contraseña, rol) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $nombre, $email, $hash, $rol);
        $stmt->execute();
    }
}
