<?php
require_once 'helpers/auth.php';

class PendienteController
{
    // ✅ Muestra la lista de tareas pendientes (solo accesible por administradores)
    public function index()
    {
        if (!usuarioAutenticado() || $_SESSION['rol'] !== 'administrador') {
            header('Location: index.php?c=auth&a=login');
            exit;
        }

        require 'config/database.php';

        // Filtrado por fechas
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('-7 days')); // por defecto 7 días atrás
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

        // Consulta mejorada con todos los datos solicitados
        $sql = "SELECT 
    t.id,
    t.tipo,
    t.dispositivo_id,
    t.fecha,
    t.revisado,
    d.etiqueta_empresa,
    d.tipo AS tipo_dispositivo,
    d.marca,
    d.modelo,
    p.id AS prestamo_id,
    p.firma_empleado,
    p.firma_tecnico,
    tecnico.nombre AS tecnico,
    usuario.nombre AS usuario
FROM tareas_pendientes t
JOIN dispositivos d ON d.id = t.dispositivo_id
JOIN prestamos p ON p.id = t.prestamo_id
JOIN usuarios tecnico ON p.tecnico_id = tecnico.id
JOIN usuarios usuario ON p.usuario_id = usuario.id
WHERE DATE(t.fecha) BETWEEN ? AND ?
ORDER BY t.fecha DESC
";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
        $stmt->execute();
        $tareas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        require 'views/admin/pendientes.php';
    }


    public function marcarRevisada()
    {
        // 🔐 Solo administradores pueden marcar tareas como revisadas
        if (!usuarioAutenticado() || $_SESSION['rol'] !== 'administrador') {
            header('Location: index.php?c=auth&a=login');
            exit;
        }

        // Validar ID recibido por POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            require 'config/database.php';

            $id = intval($_POST['id']);

            // ✅ Marcar tarea como revisada
            $stmt = $conn->prepare("UPDATE tareas_pendientes SET revisado = 1 WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }

        // 🔄 Redirigir nuevamente a la vista de pendientes
        header('Location: index.php?c=pendiente&a=index');
        exit;
    }

    public function exportarSimple()
    {
        if (!usuarioAutenticado() || $_SESSION['rol'] !== 'administrador') {
            header('Location: index.php?c=auth&a=login');
            exit;
        }

        require 'config/database.php';

        // Obtener IDs seleccionados por POST
        $ids = $_POST['tareas'] ?? [];

        if (empty($ids)) {
            echo "No se seleccionaron tareas.";
            exit;
        }

        // Preparar placeholders para IN
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));

        // Consulta básica
        $sql = "SELECT 
    t.tipo,
    d.etiqueta_empresa,
    p.fecha_prestamo AS fecha,
    u.email AS email_usuario
FROM tareas_pendientes t
JOIN dispositivos d ON d.id = t.dispositivo_id
JOIN prestamos p ON p.id = t.prestamo_id
JOIN usuarios u ON p.usuario_id = u.id
WHERE t.id IN ($placeholders)
";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        $result = $stmt->get_result();
        // Cabeceras para Excel
        // Cabeceras para exportar como Excel plano
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="tareas_exportadas.xls"');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        echo "<table border='1'>";
        echo "<thead>
        <tr>
            <th>Tipo</th>
            <th>Etiqueta</th>
            <th>Fecha</th>
            <th>Correo usuario</th>
        </tr>
      </thead>";
        echo "<tbody>";

        while ($fila = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . ucfirst($fila['tipo']) . "</td>";
            echo "<td>" . htmlspecialchars($fila['etiqueta_empresa']) . "</td>";
            echo "<td>" . date('d/m/Y', strtotime($fila['fecha'])) . "</td>";
            echo "<td>" . htmlspecialchars($fila['email_usuario']) . "</td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
        exit;
    }
}
