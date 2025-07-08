<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Dispositivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS para modo oscuro -->
    <link rel="stylesheet" href="public/css/tema.css">
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Editar dispositivo</h2>
        <!-- Botón para cambiar modo -->
        <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
            <a href="index.php?c=dispositivo&a=index" class="btn btn-outline-secondary">
                ← Volver
            </a>
            <button id="toggle-dark-mode" aria-label="Modo oscuro" class="btn btn-outline-secondary btn-dark-mode">
                <i class="bi bi-moon-fill"></i>
            </button>
        </div>


        <form method="POST" action="index.php?c=dispositivo&a=actualizar" class="card p-4 shadow-sm">
            <input type="hidden" name="id" value="<?= $dispositivo['id'] ?>">

            <div class="mb-3">
                <label class="form-label">Etiqueta empresa</label>
                <input type="text" name="etiqueta_empresa" class="form-control" value="<?= $dispositivo['etiqueta_empresa'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo</label>
                <select name="tipo" id="tipo" class="form-select" required>
                    <?php
                    $tipos = ['portatil', 'monitor', 'teclado', 'raton', 'mochila', 'auricular', 'tablet', 'telefono', 'otros'];
                    $tipo_actual = $dispositivo['tipo'];
                    $es_otro = !in_array($tipo_actual, $tipos);
                    foreach ($tipos as $t):
                        $selected = ($t === $tipo_actual || ($t === 'otros' && $es_otro)) ? 'selected' : '';
                    ?>
                        <option value="<?= $t ?>" <?= $selected ?>><?= ucfirst($t) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3" id="tipo_personalizado_container" style="display: none;">
                <label class="form-label">Especificar tipo:</label>
                <input type="text" name="tipo_personalizado" id="tipo_personalizado" class="form-control" value="<?= $es_otro ? htmlspecialchars($tipo_actual) : '' ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Marca</label>
                <input type="text" name="marca" class="form-control" value="<?= $dispositivo['marca'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Modelo</label>
                <input type="text" name="modelo" class="form-control" value="<?= $dispositivo['modelo'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Número de serie</label>
                <input type="text" name="num_serie" class="form-control" value="<?= $dispositivo['num_serie'] ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Fecha de compra</label>
                <input type="date" name="fecha_compra" class="form-control" value="<?= $dispositivo['fecha_compra'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Fin de garantía</label>
                <input type="date" name="fin_garantia" class="form-control" value="<?= $dispositivo['fin_garantia'] ?>" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Actualizar</button>
        </form>


    </div>

    <!-- Script para tipo personalizado "otro" -->
    <script src="public/js/editar_dispositivo.js"></script>

    <!-- JS para modo oscuro -->
    <script src="public/js/tema.js"></script>

</body>

</html>