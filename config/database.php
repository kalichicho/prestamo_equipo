<?php
$host = 'localhost';
$usuario = 'root';
$contrasena = ''; // ← en XAMPP normalmente está vacío
$basedatos = 'prestamos_equipo'; // ← asegúrate que sea el nombre correcto de tu BD

$conn = new mysqli($host, $usuario, $contrasena, $basedatos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
