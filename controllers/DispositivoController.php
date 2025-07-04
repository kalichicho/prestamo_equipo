<?php
require_once 'helpers/auth.php';
require_once 'helpers/tareas.php';
require_once 'models/Dispositivo.php';

class DispositivoController
{
    public function index()
    {
        if (!esTecnico()) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }

        $buscar      = $_GET['buscar'] ?? '';
        $tipo        = $_GET['tipo']   ?? '';
        $marca       = $_GET['marca']  ?? '';

        $dispositivos  = Dispositivo::filtrarConNombreAsignado($buscar, $marca, $tipo);
        $tiposUnicos   = Dispositivo::obtenerTiposUnicos();
        $marcasUnicas  = Dispositivo::obtenerMarcasPorTipo($tipo);

        require 'views/dispositivos/index.php';
    }

    public function nuevo()
    {
        if (!esTecnico()) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }
        require 'views/dispositivos/nuevo.php';
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && esTecnico()) {
            $etiqueta     = $_POST['etiqueta_empresa'];
            $tipo         = $_POST['tipo'] === 'otros'
                                ? trim($_POST['tipo_personalizado'])
                                : $_POST['tipo'];
            $marca        = $_POST['marca'];
            $modelo       = $_POST['modelo'];
            $num_serie    = trim($_POST['num_serie']);
            $fecha_compra = $_POST['fecha_compra'];
            $fin_garantia = $_POST['fin_garantia'];

            if (empty($num_serie)) {
                $_SESSION['error'] = "El número de serie no puede estar vacío.";
                require 'views/dispositivos/nuevo.php';
                return;
            }
            if (Dispositivo::existeNumSerie($num_serie)) {
                $_SESSION['error'] = "Ya existe un dispositivo con ese número de serie.";
                require 'views/dispositivos/nuevo.php';
                return;
            }
            if (Dispositivo::existeEtiquetaEmpresa($etiqueta)) {
                $_SESSION['error'] = "Ya existe un dispositivo con esa etiqueta.";
                require 'views/dispositivos/nuevo.php';
                return;
            }

            Dispositivo::crear(
                $etiqueta, $tipo, $marca,
                $modelo, $num_serie,
                $fecha_compra, $fin_garantia
            );
            header('Location: index.php?c=dispositivo&a=index');
            exit;
        }
    }

    public function editar()
    {
        if (!esTecnico()) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }
        $id          = $_GET['id'];
        $dispositivo = Dispositivo::buscarPorId($id);
        require 'views/dispositivos/editar.php';
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && esTecnico()) {
            $id           = $_POST['id'];
            $etiqueta     = $_POST['etiqueta_empresa'];
            $tipo         = $_POST['tipo'] === 'otros'
                                ? trim($_POST['tipo_personalizado'])
                                : $_POST['tipo'];
            $marca        = $_POST['marca'];
            $modelo       = $_POST['modelo'];
            $num_serie    = $_POST['num_serie'];
            $fecha_compra = $_POST['fecha_compra'];
            $fin_garantia = $_POST['fin_garantia'];

            if ($tipo === '') {
                die("Error: Debes especificar el tipo de dispositivo.");
            }

            Dispositivo::actualizar(
                $id, $etiqueta, $tipo,
                $marca, $modelo,
                $num_serie, $fecha_compra,
                $fin_garantia
            );
            header('Location: index.php?c=dispositivo&a=index');
            exit;
        }
    }

    public function eliminar()
    {
        if (!esTecnico()) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }
        $id  = $_GET['id'];
        $res = Dispositivo::eliminar($id);
        if ($res) {
            header('Location: index.php?c=dispositivo&a=index');
            exit;
        }
        echo "<p>Error: No se puede eliminar un dispositivo con préstamos registrados.</p>";
        echo "<a href='index.php?c=dispositivo&a=index'>← Volver</a>";
    }

    // ────────────────────────────────────────────────────────────────
    // Autocomplete AJAX
    public function buscarAjax()
    {
        require 'config/database.php';
        $q    = $_GET['q'] ?? '';
        $like = '%' . $q . '%';

        $sql = "
            SELECT 
                d.id,
                d.etiqueta_empresa,
                d.tipo,
                d.marca,
                d.modelo,
                d.usuario_id_prestamo_actual,
                u.nombre AS usuario_nombre
            FROM dispositivos d
            LEFT JOIN usuarios u 
              ON d.usuario_id_prestamo_actual = u.id
            WHERE (d.etiqueta_empresa LIKE ? OR d.num_serie LIKE ?)
            LIMIT 10
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $like, $like);
        $stmt->execute();
        $result = $stmt->get_result();

        $dispositivos = [];
        while ($row = $result->fetch_assoc()) {
            $dispositivos[] = $row;
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dispositivos);
        exit;
    }

    // ────────────────────────────────────────────────────────────────
    // Dar de baja + tarea pendiente
    public function darBaja()
    {
        if (!esTecnico()) exit;
        $id     = $_POST['id'];
        $motivo = trim($_POST['motivo']);

        if (Dispositivo::estaPrestadoActualmente($id)) {
            $_SESSION['error']    = "No puedes dar de baja este dispositivo porque está prestado.";
            $dispositivos         = Dispositivo::obtenerTodos();
            require 'views/dispositivos/index.php';
            return;
        }

        if (Dispositivo::darDeBaja($id, $motivo)) {
            registrarTareaPendiente('baja', $id, $_SESSION['usuario_id'], 0);
            header('Location: index.php?c=dispositivo&a=index');
            exit;
        }
        echo "Error al dar de baja el dispositivo.";
    }

    // ────────────────────────────────────────────────────────────────
    // Reactivar + tarea pendiente
    public function reactivar()
    {
        if (!esTecnico()) exit;
        $id = $_GET['id'];
        if (Dispositivo::reactivar($id)) {
            registrarTareaPendiente('alta', $id, $_SESSION['usuario_id'], 0);
            header('Location: index.php?c=dispositivo&a=historialBajas');
            exit;
        }
        echo "Error al reactivar el dispositivo.";
    }

    public function historialBajas()
    {
        if (!esTecnico()) exit;
        $bajas = Dispositivo::obtenerHistorialBajas();
        require 'views/dispositivos/historial_bajas.php';
    }

    public function darAlta()
    {
        if (!esTecnico()) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }
        $id = $_GET['id'] ?? null;
        if ($id && is_numeric($id) && Dispositivo::reactivar($id)) {
            header('Location: index.php?c=dispositivo&a=index');
            exit;
        }
        echo "❌ ID inválido o no se pudo reactivar.";
    }

    // ────────────────────────────────────────────────────────────────
    // Buscar asignado (dashboard)
    public function buscarAsignado()
    {
        require 'config/database.php';
        $resultados = [];
        if (!empty($_GET['busqueda'])) {
            $b    = $_GET['busqueda'];
            $stmt = $conn->prepare("
                SELECT d.*, u.nombre AS usuario_nombre, u.email
                FROM dispositivos d
                LEFT JOIN usuarios u ON d.usuario_id_prestamo_actual = u.id
                WHERE d.etiqueta_empresa = ? OR d.num_serie = ?
            ");
            $stmt->bind_param("ss", $b, $b);
            $stmt->execute();
            $resultados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        require 'views/dispositivos/buscar_asignado.php';
    }

    // ────────────────────────────────────────────────────────────────
    // Gestión clásica
    public function gestion()
    {
        if (!usuarioAutenticado()) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }
        require 'config/database.php';
        $totales      = Dispositivo::obtenerEstadisticas();
        $dispositivos = Dispositivo::obtenerTodos();
        require 'views/dispositivos/gestion.php';
    }

    // ────────────────────────────────────────────────────────────────
    // Stats con filtros de tipo, marca y fechas
    public function stats()
    {
        if (!usuarioAutenticado()) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }

        $tipo            = $_GET['tipo']         ?? '';
        $marca           = $_GET['marca']        ?? '';
        $fecha_inicio    = $_GET['fecha_inicio'] ?? '';
        $fecha_fin       = $_GET['fecha_fin']    ?? '';

        // Listas para selects
        $tiposDisponibles  = Dispositivo::obtenerTiposUnicos();
        $marcasDisponibles = Dispositivo::obtenerMarcasPorTipo($tipo);

        // Estadísticas y datos
        $stats                = Dispositivo::obtenerEstadisticasFiltradas($tipo, $fecha_inicio, $fecha_fin, $marca);
        $dispositivosPorTipo  = Dispositivo::obtenerConteoPorTipo($tipo, $fecha_inicio, $fecha_fin, $marca);
        $movimientosRecientes = Dispositivo::obtenerMovimientosRecientes($tipo, $fecha_inicio, $fecha_fin, $marca);
        $stats['sin_asignar'] = Dispositivo::contarSinAsignar($tipo, $fecha_inicio, $fecha_fin, $marca);
        $movimientosMensuales= Dispositivo::obtenerMovimientosMensuales($tipo, $fecha_inicio, $fecha_fin, $marca);
        $estadoPorTipo       = Dispositivo::obtenerEstadoPorTipo($tipo, $fecha_inicio, $fecha_fin, $marca);

        require 'views/dispositivos/stats.php';
    }

    // ────────────────────────────────────────────────────────────────
    // AJAX para cargar marcas según tipo
    public function marcasAjax()
    {
        require 'config/database.php';
        $tipo   = $_GET['tipo'] ?? '';
        $marcas = Dispositivo::obtenerMarcasPorTipo($tipo);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($marcas);
        exit;
    }
}
