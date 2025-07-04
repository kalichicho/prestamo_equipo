<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Dispositivos</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">

        <!-- 🏷️ Título -->
        <h2 class="mb-4 text-primary">
            📊 Estadísticas de Dispositivos
        </h2>

        <!-- 🔵 Tarjetas estadísticas -->
        <div class="row mb-4">

            <div class="col-md-3 mb-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <h3 class="text-primary"><?= $totales['total'] ?? 0 ?></h3>
                        <p class="text-muted mb-0">Total dispositivos</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <h3 class="text-success"><?= $totales['activos'] ?? 0 ?></h3>
                        <p class="text-muted mb-0">Activos</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <h3 class="text-danger"><?= $totales['bajas'] ?? 0 ?></h3>
                        <p class="text-muted mb-0">Dados de baja</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <h3 class="text-warning"><?= $totales['prestados'] ?? 0 ?></h3>
                        <p class="text-muted mb-0">Actualmente prestados</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- 🔍 Tabla de dispositivos -->
        <div class="table-responsive">
            <table class="table table-striped table-hover shadow-sm">
                <thead class="table-primary">
                    <tr>
                        <th>Etiqueta</th>
                        <th>Tipo</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Estado</th>
                        <th>Asignado</th>
                        <th>Ubicación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dispositivos as $d): ?>
                        <tr>
                            <td><?= htmlspecialchars($d['etiqueta_empresa']) ?></td>
                            <td><?= ucfirst($d['tipo']) ?></td>
                            <td><?= htmlspecialchars($d['marca']) ?></td>
                            <td><?= htmlspecialchars($d['modelo']) ?></td>
                            <td>
                                <?php if ($d['estado'] === 'activo'): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Baja</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($d['usuario_id_prestamo_actual'])): ?>
                                    <span class="badge bg-warning text-dark">Asignado</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Libre</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($d['ubicacion']) ?></td>
                            <td>
                                <a href="index.php?c=dispositivo&a=editar&id=<?= $d['id'] ?>" class="btn btn-sm btn-outline-primary">✏️ Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- 🔙 Botón para volver -->
        <div class="mt-4">
            <a href="index.php?c=prestamo&a=dashboard" class="btn btn-secondary">← Volver al Dashboard</a>
        </div>

    </div>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
