<?php
require_once 'helpers/auth.php';

class UsuarioController
{
    // 📋 Muestra los préstamos y devoluciones del usuario autenticado (empleado)
    public function misPrestamos()
    {
        if (!esEmpleado()) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }

        require 'config/database.php';
        $usuario_id = $_SESSION['usuario_id'];

        // Consulta para obtener los movimientos del usuario actual
        $sql = "
            SELECT p.id, p.tipo_operacion, p.fecha_prestamo, p.unidad, p.ubicacion,
                   GROUP_CONCAT(CONCAT(d.tipo, ': ', d.etiqueta_empresa) SEPARATOR ', ') AS dispositivos
            FROM prestamos p
            JOIN prestamos_dispositivos pd ON pd.prestamo_id = p.id
            JOIN dispositivos d ON d.id = pd.dispositivo_id
            WHERE p.usuario_id = ?
            GROUP BY p.id
            ORDER BY p.fecha_prestamo DESC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $mis_prestamos = $result->fetch_all(MYSQLI_ASSOC);

        require_once 'views/usuario_movimientos.php';
    }

    // 🔍 Búsqueda AJAX de usuarios por nombre o correo electrónico (autocompletado)
    public function buscarAjax()
    {
        require 'config/database.php';
        $q = $_GET['q'] ?? '';
        $q = '%' . $q . '%';

        $stmt = $conn->prepare("SELECT id, nombre, email FROM usuarios WHERE nombre LIKE ? OR email LIKE ?");
        $stmt->bind_param("ss", $q, $q);
        $stmt->execute();
        $result = $stmt->get_result();

        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode($usuarios);
    }

    // 👤 Busca un usuario por ID y muestra sus dispositivos actualmente asignados
    public function asignados()
    {
        require 'config/database.php';

        $usuario_id = $_GET['id'] ?? null;

        if ($usuario_id) {
            // Consulta de dispositivos asignados actualmente a este usuario
            $stmt = $conn->prepare("
                SELECT etiqueta_empresa, tipo, marca, modelo
                FROM dispositivos
                WHERE usuario_id_prestamo_actual = ?
            ");
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $dispositivos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // Obtener los datos del usuario
            $stmt = $conn->prepare("SELECT nombre, email FROM usuarios WHERE id = ?");
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $usuario = $stmt->get_result()->fetch_assoc();

            // Obtener el ID del último préstamo de tipo 'prestamo' para este usuario
            $stmt = $conn->prepare("SELECT id FROM prestamos WHERE usuario_id = ? AND tipo_operacion = 'prestamo' ORDER BY fecha_prestamo DESC, id DESC LIMIT 1");
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();
            $prestamo_id_actual = $res['id'] ?? null;

            require 'views/usuarios/asignados.php';
        } else {
            // Si no hay ID, mostrar formulario de búsqueda
            require 'views/usuarios/buscar_usuario.php';
        }
    }

    // 📦 Muestra al usuario autenticado sus dispositivos actualmente asignados
    public function asignadosActuales()
    {
        if (!usuarioAutenticado()) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }

        require 'config/database.php';
        $usuario_id = $_SESSION['usuario_id'];

        $sql = "SELECT etiqueta_empresa, tipo, marca, modelo FROM dispositivos WHERE usuario_id_prestamo_actual = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $dispositivos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        require 'views/usuarios/asignados_actuales.php';
    }


    //funcion para guardar la firma de los tecnicos
    public function guardarFirma()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once 'config/database.php'; // Cargar la conexión a BD
            session_start();

            $usuario_id = $_SESSION['usuario_id'] ?? null;

            if (!$usuario_id) {
                die('Error: No hay usuario en sesión.');
            }

            // Carpeta destino
            $carpetaDestino = 'public/firmas/';
            if (!is_dir($carpetaDestino)) {
                mkdir($carpetaDestino, 0777, true);
            }

            // Si el usuario subió una imagen
            if (isset($_FILES['firmaArchivo']) && $_FILES['firmaArchivo']['error'] == 0) {
                $nombreArchivo = 'firma_usuario_' . $usuario_id . '.png';
                $rutaCompleta = $carpetaDestino . $nombreArchivo;
                move_uploaded_file($_FILES['firmaArchivo']['tmp_name'], $rutaCompleta);
            } elseif (!empty($_POST['firmaCanvasBase64'])) {
                // Si usó el canvas
                $base64 = $_POST['firmaCanvasBase64'];
                $base64 = str_replace('data:image/png;base64,', '', $base64);
                $imagen = base64_decode($base64);

                $nombreArchivo = 'firma_usuario_' . $usuario_id . '.png';
                $rutaCompleta = $carpetaDestino . $nombreArchivo;
                file_put_contents($rutaCompleta, $imagen);
            } else {
                die('Error: No se recibió firma.');
            }

            // Guardar nombre de archivo en la base de datos
            global $conn;
            $stmt = $conn->prepare("UPDATE usuarios SET firma = ? WHERE id = ?");
            $stmt->bind_param("si", $nombreArchivo, $usuario_id);
            $stmt->execute();

            // Redirigir al dashboard o mostrar mensaje
            header('Location: index.php?c=prestamo&a=dashboard&firma=ok');
            exit;
        }
    }

    // 🖋 Muestra el formulario para configurar la firma
    public function firma()
    {
        if (!usuarioAutenticado()) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }

        require 'config/database.php';
        $usuario_id = $_SESSION['usuario_id'];

        $stmt = $conn->prepare("SELECT firma FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $firma = $stmt->get_result()->fetch_assoc()['firma'] ?? null;

        require 'views/usuarios/firma.php';
    }
}
