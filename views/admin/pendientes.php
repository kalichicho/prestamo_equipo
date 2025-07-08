<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Tareas pendientes por revisar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/tema.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-light">
<div class="container py-5">

    <h2 class="mb-4 text-primary">📌 Tareas pendientes</h2>

    <!-- Botones: Volver + Modo oscuro -->
    <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
        <a href="index.php?c=prestamo&a=dashboard" class="btn btn-outline-secondary">← Volver</a>
        <button id="toggle-dark-mode" class="btn btn-outline-secondary btn-dark-mode"><i class="bi bi-moon-fill"></i></button>
    </div>

    <!-- 🔎 Filtros por fecha -->
    <form method="GET" class="row g-3 mb-4" action="index.php">
        <input type="hidden" name="c" value="pendiente">
        <input type="hidden" name="a" value="index">

        <div class="col-md-4">
            <label for="fecha_inicio" class="form-label">Desde:</label>
            <input type="date" class="form-control" name="fecha_inicio" value="<?= $_GET['fecha_inicio'] ?? '' ?>">
        </div>

        <div class="col-md-4">
            <label for="fecha_fin" class="form-label">Hasta:</label>
            <input type="date" class="form-control" name="fecha_fin" value="<?= $_GET['fecha_fin'] ?? '' ?>">
        </div>

        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">🔍 Filtrar</button>
        </div>
    </form>

    <!-- Botón exportar -->
    <button id="btnExportar" class="btn btn-outline-success mb-3">📁 Exportar seleccionados</button>

    <!-- 📋 Tabla de resultados -->
    <?php if (!empty($tareas)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead class="table-dark">
                <tr>
                    <th><input type="checkbox" id="checkAll"> all</th>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Etiqueta</th>
                    <th>Tipo Dispositivo</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Usuario</th>
                    <th>Técnico</th>
                    <th>Fecha</th>
                    <th>Firma Técnico</th>
                    <th>Firma Usuario</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($tareas as $t): ?>
                    <tr>
                        <td><input type="checkbox" class="check-tarea" value="<?= $t['id'] ?>"></td>
                        <td><?= $t['id'] ?></td>
                        <td><?= ucfirst($t['tipo']) ?></td>
                        <td><?= $t['etiqueta_empresa'] ?></td>
                        <td><?= ucfirst($t['tipo_dispositivo']) ?></td>
                        <td><?= $t['marca'] ?></td>
                        <td><?= $t['modelo'] ?></td>
                        <td><?= $t['usuario'] ?></td>
                        <td><?= $t['tecnico'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($t['fecha'])) ?></td>
                        <td><?= $t['firma_tecnico'] ? '<span class="badge bg-success">✔</span>' : '<span class="badge bg-danger">✘</span>' ?></td>
                        <td><?= $t['firma_empleado'] ? '<span class="badge bg-success">✔</span>' : '<span class="badge bg-danger">✘</span>' ?></td>
                        <td>
                            <?= $t['revisado']
                                ? '<span class="badge bg-success">✔ Revisado</span>'
                                : '<span class="badge bg-warning text-dark">Pendiente</span>' ?>
                        </td>
                        <td>
                            <?php if (!$t['revisado']): ?>
                                <form method="POST" action="index.php?c=pendiente&a=marcarRevisada"
                                      onsubmit="return confirm('¿Marcar esta tarea como revisada?');">
                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-success">✔ Marcar revisada</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">No hay tareas pendientes en este rango de fechas.</div>
    <?php endif; ?>

</div>

<!-- JS para modo oscuro -->
<script src="public/js/tema.js"></script>

<!-- JS para selección -->
<script>
    document.getElementById('checkAll').addEventListener('change', function () {
        document.querySelectorAll('.check-tarea').forEach(cb => cb.checked = this.checked);
    });

    document.getElementById('btnExportar').addEventListener('click', function () {
        const seleccionados = Array.from(document.querySelectorAll('.check-tarea:checked'))
            .map(cb => cb.value);

        if (seleccionados.length === 0) {
            alert("Selecciona al menos una tarea para exportar.");
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?c=pendiente&a=exportarSimple';

        seleccionados.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'tareas[]';
            input.value = id;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    });
</script>

</body>
</html>
