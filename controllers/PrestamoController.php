<?php
require_once 'helpers/auth.php';
require_once 'helpers/tareas.php';

class PrestamoController
{
    // 🏠 Muestra el dashboard según el rol del usuario
    public function dashboard()
    {
        if (!usuarioAutenticado()) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }

        require 'config/database.php';

        // Solo para administradores, contar tareas no revisadas
        $pendientes_no_revisados = 0;

        if ($_SESSION['rol'] === 'administrador') {
            $sql = "SELECT COUNT(*) AS total FROM tareas_pendientes WHERE revisado = 0";
            $res = $conn->query($sql);
            if ($res) {
                $pendientes_no_revisados = $res->fetch_assoc()['total'] ?? 0;
            }
        }

        // Pasamos la variable a la vista
        require_once 'views/dashboard.php';
    }



    // 📄 Muestra el formulario para crear un nuevo préstamo o devolución
    public function crear()
    {
        if (!esTecnico()) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }

        $empleados = Usuario::obtenerUsuarios();
        $dispositivos = Dispositivo::obtenerDisponibles();

        require_once 'views/formulario.php';
    }

    // ✅ Procesa y guarda una operación de préstamo o devolución
    public function guardar()
    {
        $tecnico_id = $_SESSION['usuario_id']; // Técnico logueado

        if (!esTecnico()) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require 'config/database.php';
            require_once 'helpers/tareas.php'; // 🆕 Cargar el helper de tareas pendientes

            // 🔵 Datos recibidos del formulario
            $tipo_operacion = $_POST['tipo_operacion'] ?? '';
            $usuario_id = intval($_POST['usuario_id'] ?? 0);
            $fecha = $_POST['fecha'] ?? '';
            $unidad = trim($_POST['unidad'] ?? '');
            $ubicacion = trim($_POST['ubicacion'] ?? '');
            $dispositivos = $_POST['dispositivos'] ?? [];

            // 🔵 Validación básica
            if (!in_array($tipo_operacion, ['prestamo', 'devolucion']) || !$usuario_id || empty($dispositivos)) {
                $error = "Datos inválidos o incompletos.";
                require 'views/formulario.php';
                return;
            }

            // 🔵 Insertar movimiento en tabla prestamos
            $stmt = $conn->prepare("INSERT INTO prestamos (tipo_operacion, usuario_id, tecnico_id, fecha_prestamo, unidad, ubicacion, firma_tecnico) VALUES (?, ?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("siisss", $tipo_operacion, $usuario_id, $tecnico_id, $fecha, $unidad, $ubicacion);
            $stmt->execute();
            $prestamo_id = $stmt->insert_id;

            // 🔵 Obtener nombre de archivo de firma del técnico
            $stmtFirma = $conn->prepare("SELECT firma FROM usuarios WHERE id = ?");
            $stmtFirma->bind_param("i", $tecnico_id);
            $stmtFirma->execute();
            $firma = $stmtFirma->get_result()->fetch_assoc()['firma'] ?? null;

            // 🔵 Copiar firma si existe
            if ($firma) {
                $origen = 'public/firmas/' . $firma;
                $destino = 'public/firmas/firma_tecnico_' . $prestamo_id . '.png';
                if (file_exists($origen)) {
                    copy($origen, $destino);
                }
            }

            $stmtDetalle = $conn->prepare("INSERT INTO prestamos_dispositivos (prestamo_id, dispositivo_id) VALUES (?, ?)");

            // 🔵 Procesar cada dispositivo seleccionado
            foreach ($dispositivos as $dispositivo_id) {
                $stmtCheck = $conn->prepare("SELECT etiqueta_empresa, estado, usuario_id_prestamo_actual FROM dispositivos WHERE id = ?");
                $stmtCheck->bind_param("i", $dispositivo_id);
                $stmtCheck->execute();
                $res = $stmtCheck->get_result()->fetch_assoc();

                if (!$res) {
                    $error = "Dispositivo no encontrado.";
                    require 'views/formulario.php';
                    return;
                }

                $etiqueta = $res['etiqueta_empresa'];

                if ($res['estado'] === 'baja') {
                    $error = "El dispositivo $etiqueta está dado de baja.";
                    require 'views/formulario.php';
                    return;
                }

                // 🔵 Prestamo
                if ($tipo_operacion === 'prestamo') {
                    if (!empty($res['usuario_id_prestamo_actual'])) {
                        $error = "El dispositivo $etiqueta ya está prestado.";
                        require 'views/formulario.php';
                        return;
                    }
                    $stmtUpdate = $conn->prepare("UPDATE dispositivos SET usuario_id_prestamo_actual = ? WHERE id = ?");
                    $stmtUpdate->bind_param("ii", $usuario_id, $dispositivo_id);
                    $stmtUpdate->execute();
                }

                // 🔵 Devolución
                if ($tipo_operacion === 'devolucion') {
                    if ($res['usuario_id_prestamo_actual'] != $usuario_id) {
                        $error = "El dispositivo $etiqueta no está asignado a este usuario.";
                        require 'views/formulario.php';
                        return;
                    }
                    $stmtUpdate = $conn->prepare("UPDATE dispositivos SET usuario_id_prestamo_actual = NULL WHERE id = ?");
                    $stmtUpdate->bind_param("i", $dispositivo_id);
                    $stmtUpdate->execute();
                }

                // 🔵 Insertar relación préstamo-dispositivo
                $stmtDetalle->bind_param("ii", $prestamo_id, $dispositivo_id);
                $stmtDetalle->execute();

                // 🆕 Registrar tarea pendiente
                // Línea dentro del foreach de dispositivos:
                registrarTareaPendiente($tipo_operacion, $dispositivo_id, $tecnico_id, $prestamo_id);
            }

            header("Location: index.php?c=prestamo&a=historial");
            exit;
        }
    }



    // 📜 Muestra el historial de préstamos y devoluciones
    public function historial()
    {
        if (!esTecnico()) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }

        require 'config/database.php';

        $usuario_id = $_GET['usuario_id'] ?? null;

        $sql = "
    SELECT p.id, p.tipo_operacion, p.fecha_prestamo, p.unidad, p.ubicacion, p.firma_empleado,
           u.nombre AS usuario,
           GROUP_CONCAT(CONCAT(d.tipo, ': ', d.etiqueta_empresa) SEPARATOR ', ') AS dispositivos
    FROM prestamos p
    JOIN usuarios u ON p.usuario_id = u.id
    JOIN prestamos_dispositivos pd ON pd.prestamo_id = p.id
    JOIN dispositivos d ON d.id = pd.dispositivo_id
";


        if ($usuario_id && is_numeric($usuario_id)) {
            $sql .= " WHERE p.usuario_id = " . intval($usuario_id);
        }

        $sql .= " GROUP BY p.id ORDER BY p.fecha_prestamo DESC";

        $result = $conn->query($sql);
        $movimientos = $result->fetch_all(MYSQLI_ASSOC);

        require_once 'views/historial.php';
    }

    public function dispositivosPrestadosPorUsuario()
    {
        require 'config/database.php';

        $usuario_id = $_GET['usuario_id'] ?? null;

        if (!$usuario_id) {
            echo json_encode([]);
            return;
        }

        $sql = "
            SELECT id, etiqueta_empresa, tipo, marca, modelo
            FROM dispositivos
            WHERE usuario_id_prestamo_actual = ?
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $dispositivos = $result->fetch_all(MYSQLI_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($dispositivos);
    }

    public function buscarUsuariosAjax()
    {
        require 'config/database.php';

        $q = $_GET['q'] ?? '';

        $stmt = $conn->prepare("
            SELECT id, nombre, email
            FROM usuarios
            WHERE nombre LIKE ? OR email LIKE ?
            ORDER BY nombre
            LIMIT 10
        ");

        $like = '%' . $q . '%';
        $stmt->bind_param('ss', $like, $like);
        $stmt->execute();

        $result = $stmt->get_result();
        $usuarios = $result->fetch_all(MYSQLI_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($usuarios);
    }

    //guardar firma del empleado

    public function guardarFirmaEmpleado()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require 'config/database.php';

            $prestamo_id = $_POST['prestamo_id'] ?? null;
            $firmaCanvas = $_POST['firmaCanvas'] ?? '';

            if (!$prestamo_id || !$firmaCanvas) {
                $_SESSION['error'] = 'Datos inválidos para firmar.';
                header('Location: index.php?c=prestamo&a=historial');
                exit;
            }

            // Guardar imagen
            $firmaCanvas = str_replace('data:image/png;base64,', '', $firmaCanvas);
            $firmaCanvas = str_replace(' ', '+', $firmaCanvas); // corregir espacios
            $imagen = base64_decode($firmaCanvas);

            $ruta = 'public/firmas/firma_empleado_' . $prestamo_id . '.png';
            file_put_contents($ruta, $imagen);

            // Marcar como firmado en la base de datos
            global $conn;
            $stmt = $conn->prepare("UPDATE prestamos SET firma_empleado = 1 WHERE id = ?");
            $stmt->bind_param("i", $prestamo_id);
            $stmt->execute();

            $_SESSION['success'] = 'Firma del empleado guardada correctamente.';
            header('Location: index.php?c=prestamo&a=historial');
            exit;
        }
    }
}
