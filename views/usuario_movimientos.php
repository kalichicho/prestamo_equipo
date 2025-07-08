<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mis Préstamos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

    <!-- CSS para modo oscuro -->
    <link rel="stylesheet" href="public/css/tema.css">
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Mis Préstamos y Devoluciones</h2>
        <!-- Botón para cambiar modo -->
        <div class="text-end mb-3">
            <button id="toggle-dark-mode" class="btn btn-outline-secondary btn-dark-mode"><i class="bi bi-moon-fill"></i></button>
        </div>


        <?php if (!empty($mis_prestamos)): ?>
            <table id="tabla_mis_prestamos" class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Fecha</th>
                        <th>Dispositivos</th>
                        <th>Unidad</th>
                        <th>Ubicación</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mis_prestamos as $m): ?>
                        <tr>
                            <td><?= $m['id'] ?></td>
                            <td><?= ucfirst($m['tipo_operacion']) ?></td>
                            <td><?= $m['fecha_prestamo'] ?></td>
                            <td><?= $m['dispositivos'] ?></td>
                            <td><?= $m['unidad'] ?></td>
                            <td><?= $m['ubicacion'] ?></td>
                            <td class="text-center">
                                <a href="index.php?c=pdf&a=ver&id=<?= $m['id'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    📄 PDF
                                </a>
                            </td>
                            

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">No tienes movimientos registrados.</div>
        <?php endif; ?>

        <a href="index.php?c=prestamo&a=dashboard" class="btn btn-secondary mt-3">← Volver al Menú</a>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    

    <!-- JS para modo oscuro -->
    <script src="public/js/tema.js"></script>

</body>

</html>