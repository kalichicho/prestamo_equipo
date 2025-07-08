<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Configurar Firma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #canvasFirma {
            border: 2px dashed #ccc;
            background-color: #fff;
            width: 100%;
            height: 200px;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container py-4">
        <h2 class="mb-4">✍️ Configurar Firma</h2>


        <!--Firma guardada vista previa-->
        <?php if (!empty($firma)) : ?>
            <div class="mb-3">
                <p class="mb-1"><strong>Tu firma actual:</strong></p>
                <img src="public/firmas/<?php echo htmlspecialchars($firma); ?>?v=<?php echo time(); ?>" alt="Firma actual" height="100">

            </div>
        <?php endif; ?>


        <form id="formFirma" method="post" action="index.php?c=usuario&a=guardarFirma" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Subir imagen de firma (PNG):</label>
                <input type="file" name="firmaArchivo" class="form-control" accept="image/png">
            </div>

            <div class="mb-4">
                <label class="form-label">O dibuja tu firma aquí:</label>
                <canvas id="canvasFirma" width="400" height="120" class="border w-100" style="max-width: 400px;"></canvas>


                <input type="hidden" name="firmaCanvasBase64" id="firmaCanvasBase64">
            </div>

            <div class="d-flex gap-2 mb-3">
                <button type="button" id="limpiarCanvas" class="btn btn-outline-secondary">🧹 Limpiar</button>
                <button type="button" id="guardarCanvas" class="btn btn-primary">💾 Guardar firma dibujada</button>
            </div>
        </form>

        <a href="index.php?c=prestamo&a=dashboard" class="btn btn-outline-secondary">
            ← Volver
        </a>

    </div>

    <script src="public/js/firma.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>