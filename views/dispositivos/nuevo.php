<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Dispositivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS para modo oscuro -->
    <link rel="stylesheet" href="public/css/tema.css">
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Registrar nuevo dispositivo</h2>

        <!-- Botón para cambiar modo -->
        <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
            <a href="index.php?c=dispositivo&a=index" class="btn btn-outline-secondary">
                ← Volver
            </a>
            <button id="toggle-dark-mode" class="btn btn-outline-secondary btn-dark-mode">
                <i class="bi bi-moon-fill"></i>
            </button>
        </div>


        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" action="index.php?c=dispositivo&a=guardar" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label class="form-label">Etiqueta empresa</label>
                <input type="text" name="etiqueta_empresa" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo</label>
                <select name="tipo" id="tipo" class="form-select" required>
                    <?php
                    $tipos = [
                        'portatil',
                        'monitor',
                        'docking',
                        'surface',
                        'disco',
                        'teclado',
                        'ratón',
                        'mochila',
                        'auriculares',
                        'tablet',
                        'telefono',
                        'cargador',
                        'adaptador',
                        'otros'
                    ];
                    foreach ($tipos as $t):
                    ?>
                        <option value="<?= strtolower($t) ?>"><?= $t ?></option>
                    <?php endforeach; ?>

                </select>

                <div class="mt-2" id="tipo_personalizado_container" style="display: none;">
                    <label class="form-label">Especificar tipo:</label>
                    <input type="text" name="tipo_personalizado" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Marca</label>
                <input type="text" name="marca" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Modelo</label>
                <input type="text" name="modelo" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Número de serie</label>
                <input type="text" name="num_serie" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Fecha de compra</label>
                <input type="date" name="fecha_compra" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Fin de garantía</label>
                <input type="date" name="fin_garantia" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success w-100">Registrar</button>
        </form>

    </div>

    <script>
        document.getElementById('tipo').addEventListener('change', function() {
            const esOtro = this.value === 'otros';
            document.getElementById('tipo_personalizado_container').style.display = esOtro ? 'block' : 'none';
        });
    </script>

    <!-- JS para modo oscuro -->
    <script src="public/js/tema.js"></script>

</body>

</html>