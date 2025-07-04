<?php

class Dispositivo
{
    // Obtiene todos los dispositivos disponibles (activos o no prestados)
    public static function obtenerDisponibles()
    {
        require 'config/database.php';
        $sql    = "SELECT * FROM dispositivos ORDER BY tipo, etiqueta_empresa";
        $result = $conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Obtiene todos los dispositivos sin ningún filtro
    public static function obtenerTodos()
    {
        require 'config/database.php';
        $sql    = "SELECT * FROM dispositivos ORDER BY tipo, etiqueta_empresa";
        $result = $conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Crea un nuevo dispositivo
    public static function crear($etiqueta, $tipo, $marca, $modelo, $num_serie, $fecha_compra, $fin_garantia)
    {
        require 'config/database.php';
        $stmt = $conn->prepare("
            INSERT INTO dispositivos
                (etiqueta_empresa, tipo, marca, modelo, num_serie, fecha_compra, fin_garantia)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssss",
            $etiqueta, $tipo, $marca,
            $modelo, $num_serie,
            $fecha_compra, $fin_garantia
        );
        $stmt->execute();
    }

    // Busca un dispositivo por su ID
    public static function buscarPorId($id)
    {
        require 'config/database.php';
        $stmt = $conn->prepare("SELECT * FROM dispositivos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Actualiza un dispositivo
    public static function actualizar($id, $etiqueta, $tipo, $marca, $modelo, $num_serie, $fecha_compra, $fin_garantia)
    {
        require 'config/database.php';
        $stmt = $conn->prepare("
            UPDATE dispositivos
            SET etiqueta_empresa = ?, tipo = ?, marca = ?, modelo = ?, num_serie = ?, fecha_compra = ?, fin_garantia = ?
            WHERE id = ?
        ");
        $stmt->bind_param("sssssssi",
            $etiqueta, $tipo, $marca,
            $modelo, $num_serie,
            $fecha_compra, $fin_garantia,
            $id
        );
        $stmt->execute();
    }

    // Elimina un dispositivo si no tiene préstamos
    public static function eliminar($id)
    {
        require 'config/database.php';
        $check = $conn->prepare("
            SELECT COUNT(*) FROM prestamos_dispositivos WHERE dispositivo_id = ?
        ");
        $check->bind_param("i", $id);
        $check->execute();
        $check->bind_result($existe);
        $check->fetch();
        $check->close();
        if ($existe > 0) return false;

        $stmt = $conn->prepare("DELETE FROM dispositivos WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Búsqueda parcial
    public static function buscarPorEtiquetaOSerie($texto)
    {
        require 'config/database.php';
        $like = "%$texto%";
        $stmt = $conn->prepare("
            SELECT * FROM dispositivos
            WHERE etiqueta_empresa LIKE ? OR num_serie LIKE ?
        ");
        $stmt->bind_param("ss", $like, $like);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Comprueba serie
    public static function existeNumSerie($num_serie)
    {
        require 'config/database.php';
        $stmt = $conn->prepare("
            SELECT id FROM dispositivos WHERE num_serie = ?
        ");
        $stmt->bind_param("s", $num_serie);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    // Comprueba etiqueta
    public static function existeEtiquetaEmpresa($etiqueta)
    {
        require 'config/database.php';
        $stmt = $conn->prepare("
            SELECT id FROM dispositivos WHERE etiqueta_empresa = ?
        ");
        $stmt->bind_param("s", $etiqueta);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    // Baja lógica
    public static function darDeBaja($id, $motivo)
    {
        require 'config/database.php';
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("UPDATE dispositivos SET estado = 'baja' WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $stmt = $conn->prepare("
                INSERT INTO bajas_dispositivos
                    (dispositivo_id, fecha_baja, motivo)
                VALUES (?, CURDATE(), ?)
            ");
            $stmt->bind_param("is", $id, $motivo);
            $stmt->execute();

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollback();
            return false;
        }
    }

    // Reactivar
    public static function reactivar($id)
    {
        require 'config/database.php';
        $stmt = $conn->prepare("UPDATE dispositivos SET estado = 'activo' WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Historial bajas
    public static function obtenerHistorialBajas()
    {
        require 'config/database.php';
        $sql = "
            SELECT
                d.id AS dispositivo_id,
                d.etiqueta_empresa,
                d.marca,
                d.modelo,
                b.fecha_baja,
                b.motivo
            FROM bajas_dispositivos b
            JOIN dispositivos d ON d.id = b.dispositivo_id
            ORDER BY b.fecha_baja DESC
        ";
        $result = $conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Último movimiento prestamo?
    public static function estaPrestadoActualmente($id)
    {
        require 'config/database.php';
        $sql = "
            SELECT p.tipo_operacion
            FROM prestamos_dispositivos pd
            JOIN prestamos p ON pd.prestamo_id = p.id
            WHERE pd.dispositivo_id = ?
            ORDER BY p.fecha_prestamo DESC, p.id DESC
            LIMIT 1
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row && $row['tipo_operacion'] === 'prestamo';
    }

    // Filtro básico
    public static function filtrar($buscar, $marca, $tipo)
    {
        require 'config/database.php';
        $conds  = [];
        $params = [];
        $types  = '';

        if ($buscar !== '') {
            $conds[]  = "(etiqueta_empresa LIKE ? OR num_serie LIKE ?)";
            $params[] = "%$buscar%";
            $params[] = "%$buscar%";
            $types  .= 'ss';
        }
        if ($marca !== '') {
            $conds[]  = "marca = ?";
            $params[] = $marca;
            $types  .= 's';
        }
        if ($tipo !== '') {
            $conds[]  = "tipo = ?";
            $params[] = $tipo;
            $types  .= 's';
        }

        $where = $conds ? 'WHERE ' . implode(' AND ', $conds) : '';
        $sql   = "SELECT * FROM dispositivos $where ORDER BY tipo, etiqueta_empresa";

        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Filtro con nombre usuario
    public static function filtrarConNombreAsignado($buscar, $marca, $tipo)
    {
        require 'config/database.php';
        $sql    = "
            SELECT d.*, u.nombre AS usuario_nombre
            FROM dispositivos d
            LEFT JOIN usuarios u ON d.usuario_id_prestamo_actual = u.id
            WHERE 1=1
        ";
        $params = [];
        $types  = '';

        if ($buscar) {
            $sql      .= " AND (d.etiqueta_empresa LIKE ? OR d.num_serie LIKE ?)";
            $params[] = "%$buscar%";
            $params[] = "%$buscar%";
            $types   .= 'ss';
        }
        if ($marca) {
            $sql      .= " AND d.marca = ?";
            $params[] = $marca;
            $types   .= 's';
        }
        if ($tipo) {
            $sql      .= " AND d.tipo = ?";
            $params[] = $tipo;
            $types   .= 's';
        }

        $stmt = $conn->prepare($sql);
        if ($types) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Tipos únicos
    public static function obtenerTiposUnicos()
    {
        require 'config/database.php';
        $sql    = "
            SELECT DISTINCT tipo
            FROM dispositivos
            WHERE estado = 'activo'
            ORDER BY tipo
        ";
        $result = $conn->query($sql);
        return array_column($result->fetch_all(MYSQLI_ASSOC), 'tipo');
    }

    // Marcas por tipo
    public static function obtenerMarcasPorTipo($tipo = '')
    {
        require 'config/database.php';
        if ($tipo) {
            $stmt = $conn->prepare("
                SELECT DISTINCT marca
                FROM dispositivos
                WHERE tipo = ?
                ORDER BY marca
            ");
            $stmt->bind_param("s", $tipo);
        } else {
            $stmt = $conn->prepare("
                SELECT DISTINCT marca
                FROM dispositivos
                ORDER BY marca
            ");
        }
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_NUM);
        return array_map(fn($r) => $r[0], $rows);
    }

    // Marcas únicas (legacy)
    public static function obtenerMarcasUnicas()
    {
        require 'config/database.php';
        $sql    = "
            SELECT DISTINCT marca
            FROM dispositivos
            WHERE estado = 'activo'
            ORDER BY marca
        ";
        $result = $conn->query($sql);
        return array_column($result->fetch_all(MYSQLI_ASSOC), 'marca');
    }

    // Estadísticas globales
    public static function obtenerEstadisticas($tipo = '', $fecha_inicio = '', $fecha_fin = '')
    {
        require 'config/database.php';
        $conds  = [];
        $params = [];
        $types  = '';

        if ($tipo) {
            $conds[]  = "tipo = ?";
            $params[] = $tipo;
            $types  .= 's';
        }
        if ($fecha_inicio && $fecha_fin) {
            $conds[]  = "fecha_compra BETWEEN ? AND ?";
            $params[] = $fecha_inicio;
            $params[] = $fecha_fin;
            $types  .= 'ss';
        }

        $where = $conds ? 'WHERE ' . implode(' AND ', $conds) : '';
        $sql   = "
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN estado='activo' THEN 1 ELSE 0 END) AS activos,
                SUM(CASE WHEN estado='baja' THEN 1 ELSE 0 END) AS bajas,
                SUM(CASE WHEN usuario_id_prestamo_actual IS NOT NULL THEN 1 ELSE 0 END) AS prestados,
                SUM(CASE WHEN usuario_id_prestamo_actual IS NULL AND estado='activo' THEN 1 ELSE 0 END) AS sin_asignar
            FROM dispositivos
            $where
        ";
        $stmt = $conn->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Estadísticas filtradas
    public static function obtenerEstadisticasFiltradas($tipo = '', $fecha_inicio = '', $fecha_fin = '', $marca = '')
    {
        require 'config/database.php';
        $conds  = [];
        $params = [];
        $types  = '';

        if ($tipo) {
            $conds[]  = "tipo = ?";
            $params[] = $tipo;
            $types  .= 's';
        }
        if ($marca) {
            $conds[]  = "marca = ?";
            $params[] = $marca;
            $types  .= 's';
        }
        if ($fecha_inicio && $fecha_fin) {
            $conds[]  = "fecha_compra BETWEEN ? AND ?";
            $params[] = $fecha_inicio;
            $params[] = $fecha_fin;
            $types  .= 'ss';
        }

        $where = $conds ? 'WHERE ' . implode(' AND ', $conds) : '';
        $sql   = "
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN estado='activo' THEN 1 ELSE 0 END) AS activos,
                SUM(CASE WHEN estado='baja' THEN 1 ELSE 0 END) AS bajas,
                SUM(CASE WHEN usuario_id_prestamo_actual IS NOT NULL THEN 1 ELSE 0 END) AS prestados
            FROM dispositivos
            $where
        ";
        $stmt = $conn->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res ?: ['total'=>0,'activos'=>0,'bajas'=>0,'prestados'=>0];
    }

    // Conteo por tipo con filtros
    public static function obtenerConteoPorTipo(string $tipo = '', string $fecha_inicio = '', string $fecha_fin = '', string $marca = ''): array
    {
        require 'config/database.php';
        $conds  = [];
        $params = [];
        $types  = '';

        if ($tipo) {
            $conds[]  = "tipo = ?";
            $params[] = $tipo;
            $types  .= 's';
        }
        if ($marca) {
            $conds[]  = "marca = ?";
            $params[] = $marca;
            $types  .= 's';
        }
        if ($fecha_inicio && $fecha_fin) {
            $conds[]  = "fecha_compra BETWEEN ? AND ?";
            $params[] = $fecha_inicio;
            $params[] = $fecha_fin;
            $types  .= 'ss';
        }

        $where = $conds ? 'WHERE ' . implode(' AND ', $conds) : '';
        $sql   = "SELECT tipo, COUNT(*) AS cantidad FROM dispositivos $where GROUP BY tipo";
        $stmt  = $conn->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Movimientos recientes (12 meses)
    public static function obtenerMovimientosRecientes(string $tipo = '', string $fecha_inicio = '', string $fecha_fin = '', string $marca = ''): array
    {
        require 'config/database.php';
        $conds  = [];
        $params = [];
        $types  = '';

        if ($tipo) {
            $conds[]  = "tipo = ?";
            $params[] = $tipo;
            $types  .= 's';
        }
        if ($marca) {
            $conds[]  = "marca = ?";
            $params[] = $marca;
            $types  .= 's';
        }
        if ($fecha_inicio && $fecha_fin) {
            $conds[]  = "fecha_compra BETWEEN ? AND ?";
            $params[] = $fecha_inicio;
            $params[] = $fecha_fin;
            $types  .= 'ss';
        }

        $where = $conds ? 'WHERE ' . implode(' AND ', $conds) : '';
        $sql   = "
            SELECT YEAR(fecha_compra) AS anio, MONTH(fecha_compra) AS mes, COUNT(*) AS altas
            FROM dispositivos
            $where
            GROUP BY anio, mes
            ORDER BY anio DESC, mes DESC
            LIMIT 12
        ";
        $stmt = $conn->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Contar sin asignar
    public static function contarSinAsignar(string $tipo = '', string $fecha_inicio = '', string $fecha_fin = '', string $marca = ''): int
    {
        require 'config/database.php';
        $conds  = ["estado='activo'", "usuario_id_prestamo_actual IS NULL"];
        $params = [];
        $types  = '';

        if ($tipo) {
            $conds[]  = "tipo = ?";
            $params[] = $tipo;
            $types  .= 's';
        }
        if ($marca) {
            $conds[]  = "marca = ?";
            $params[] = $marca;
            $types  .= 's';
        }
        if ($fecha_inicio && $fecha_fin) {
            $conds[]  = "fecha_compra BETWEEN ? AND ?";
            $params[] = $fecha_inicio;
            $params[] = $fecha_fin;
            $types  .= 'ss';
        }

        $where = 'WHERE ' . implode(' AND ', $conds);
        $sql   = "SELECT COUNT(*) AS sin_asignar FROM dispositivos $where";
        $stmt  = $conn->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int) ($row['sin_asignar'] ?? 0);
    }

    // Movimientos mensuales
    public static function obtenerMovimientosMensuales(string $tipo = '', string $fecha_inicio = '', string $fecha_fin = '', string $marca = ''): array
    {
        require 'config/database.php';
        $conds  = [];
        $params = [];
        $types  = '';

        if ($tipo) {
            $conds[]  = "tipo = ?";
            $params[] = $tipo;
            $types  .= 's';
        }
        if ($marca) {
            $conds[]  = "marca = ?";
            $params[] = $marca;
            $types  .= 's';
        }
        if ($fecha_inicio && $fecha_fin) {
            $conds[]  = "fecha_compra BETWEEN ? AND ?";
            $params[] = $fecha_inicio;
            $params[] = $fecha_fin;
            $types  .= 'ss';
        }

        $where = $conds ? 'WHERE ' . implode(' AND ', $conds) : '';
        $sql   = "
            SELECT YEAR(fecha_compra) AS anio, MONTH(fecha_compra) AS mes, COUNT(*) AS altas
            FROM dispositivos
            $where
            GROUP BY anio, mes
            ORDER BY anio, mes
        ";
        $stmt = $conn->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Estado por tipo
    public static function obtenerEstadoPorTipo(string $tipo = '', string $fecha_inicio = '', string $fecha_fin = '', string $marca = ''): array
    {
        require 'config/database.php';
        $conds  = ["estado='activo'"];
        $params = [];
        $types  = '';

        if ($tipo) {
            $conds[]  = "tipo = ?";
            $params[] = $tipo;
            $types  .= 's';
        }
        if ($marca) {
            $conds[]  = "marca = ?";
            $params[] = $marca;
            $types  .= 's';
        }
        if ($fecha_inicio && $fecha_fin) {
            $conds[]  = "fecha_compra BETWEEN ? AND ?";
            $params[] = $fecha_inicio;
            $params[] = $fecha_fin;
            $types  .= 'ss';
        }

        $where = 'WHERE ' . implode(' AND ', $conds);
        $sql   = "
            SELECT
              tipo,
              SUM(CASE WHEN usuario_id_prestamo_actual IS NULL THEN 1 ELSE 0 END) AS disponibles,
              SUM(CASE WHEN usuario_id_prestamo_actual IS NOT NULL THEN 1 ELSE 0 END) AS prestados
            FROM dispositivos
            $where
            GROUP BY tipo
            ORDER BY tipo
        ";
        $stmt = $conn->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
