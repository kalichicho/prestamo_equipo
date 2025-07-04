
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Alertas de Seguridad</title>
  <!-- HTML5 y Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- SVG Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- CSS modo oscuro -->
  <link rel="stylesheet" href="public/css/tema.css">
  <style>
    /* Misma estética de dashboard */
    .dashboard-card {
      transition: transform 0.2s ease-in-out;
      cursor: pointer;
      border-radius: 10px;
      background-color: #fff;
      height: 100%;
    }
    .dashboard-card:hover { transform: scale(1.03); }
  </style>
</head>
<body class="bg-light">
  <div class="container py-5">

    <h2 class="mb-4 text-center">Alertas de Seguridad</h2>

    <div class="row g-4">
      <?php if (empty($alertas)): ?>
        <div class="col-12">
          <div class="alert alert-secondary text-center">
            No hay alertas recientes.
          </div>
        </div>
      <?php else: ?>
        <?php foreach ($alertas as $a): ?>
          <div class="col-md-6">
            <div class="card dashboard-card shadow-sm border-danger">
              <div class="card-body d-flex flex-column">
                <div class="mb-2">
                  <i class="bi bi-shield-lock text-danger"></i>
                  <span class="h5 align-middle"><?= htmlspecialchars($a['software']) ?></span>
                  <span class="badge bg-secondary ms-2"><?= htmlspecialchars($a['fuente']) ?></span>
                </div>
                <h6 class="card-subtitle mb-2 text-dark">
                  <?= htmlspecialchars($a['titulo']) ?>
                </h6>
                <p class="card-text flex-grow-1">
                  <?= htmlspecialchars($a['descripcion']) ?>
                </p>
                <div class="mt-3 d-flex justify-content-between align-items-center">
                  <a href="<?= htmlspecialchars($a['enlace']) ?>"
                     target="_blank"
                     class="card-link">
                    Ver detalle
                  </a>
                  <small class="text-muted">
                    <?= date('d/m/Y H:i', strtotime($a['fecha'])) ?>
                  </small>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>

  <!-- JS modo oscuro -->
  <script src="public/js/tema.js"></script>
</body>
</html>
