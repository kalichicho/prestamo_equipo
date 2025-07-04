<?php

class AuthController
{
    // 🟦 Muestra el formulario de login
    public function login()
    {
        // Si ya hay sesión activa, redirige al dashboard
        if (isset($_SESSION['usuario_id'])) {
            header('Location: index.php?c=prestamo&a=dashboard');
            exit;
        }

        // Carga la vista del formulario de login
        require_once 'views/login.php';
    }

    // 🔐 Valida las credenciales enviadas desde el formulario
    public function validar()
    {
        // Solo procesa si es una solicitud POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Buscar al usuario por email
            $usuario = Usuario::buscarPorEmail($email);

            // Verificar que el usuario existe y la contraseña sea correcta
            if ($usuario && password_verify($password, $usuario['contraseña'])) {
                // ✅ Si es válido, se crea sesión con los datos necesarios
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['rol'] = $usuario['rol'];
                $_SESSION['nombre'] = $usuario['nombre'];

                // ✅ Redirige al dashboard con parámetro "ok=1" para activar animación
                header('Location: index.php?c=prestamo&a=dashboard&ok=1');
                exit;
            } else {
                // ❌ Si no coinciden, muestra mensaje de error
                $error = "Correo o contraseña incorrectos.";
                require_once 'views/login.php';
            }
        }
    }

    // 🔓 Cierra la sesión actual y vuelve al login
    public function logout()
    {
        session_destroy();
        header('Location: index.php?c=auth&a=login');
        exit;
    }
}
