<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dispositivos Asignados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS para modo oscuro -->
    <link rel="stylesheet" href="public/css/tema.css">
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Dispositivos actualmente asignados</h2>
        <!-- Botón para cambiar modo -->
        <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
            <a href="index.php?c=prestamo&a=dashboard" class="btn btn-outline-secondary">
                ← Volver
            </a>
            <button id="toggle-dark-mode" class="btn btn-outline-secondary">
                <i class="bi bi-moon-fill"></i>
            </button>
        </div>


        <?php if (!empty($dispositivos)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Etiqueta</th>
                        <th>Tipo</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dispositivos as $d): ?>
                        <tr>
                            <td><?= $d['etiqueta_empresa'] ?></td>
                            <td><?= $d['tipo'] ?></td>
                            <td><?= $d['marca'] ?></td>
                            <td><?= $d['modelo'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">No tienes dispositivos asignados actualmente.</div>
        <?php endif; ?>

        
    </div>
    <!-- JS para modo oscuro -->
    <script src="public/js/tema.js"></script>

</body>

</html>