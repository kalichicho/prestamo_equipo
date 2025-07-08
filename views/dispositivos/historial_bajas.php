<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Historial de Bajas</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Estilos personalizados -->
  <link rel="stylesheet" href="public/css/historial_bajas.css">
  <!-- CSS para modo oscuro -->
  <link rel="stylesheet" href="public/css/tema.css">
  <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-light">
  <div class="container mt-5">
    <h2 class="mb-4">Historial de Bajas de Dispositivos</h2>
    <!-- Botón para cambiar modo -->
    <!-- Botones: Volver + Modo oscuro -->
    <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
      <a href="index.php?c=dispositivo&a=index" class="btn btn-outline-secondary">
        ← Volver 
      </a>
      <button id="toggle-dark-mode" aria-label="Modo oscuro" class="btn btn-outline-secondary btn-dark-mode">
        <i class="bi bi-moon-fill"></i>
      </button>
    </div>


    <?php if (!empty($bajas)): ?>
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
          <tr>
            <th>Etiqueta</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Fecha de Baja</th>
            <th>Motivo</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($bajas as $b): ?>
            <tr>
              <td><?= htmlspecialchars($b['etiqueta_empresa']) ?></td>
              <td><?= htmlspecialchars($b['marca']) ?></td>
              <td><?= htmlspecialchars($b['modelo']) ?></td>
              <td><?= htmlspecialchars($b['fecha_baja']) ?></td>
              <td><?= htmlspecialchars($b['motivo']) ?></td>
              <td>
                <?php if (Dispositivo::estaDadoDeBaja($b['dispositivo_id'])): ?>
                  <a href="index.php?c=dispositivo&a=reactivar&id=<?= $b['dispositivo_id'] ?>"
                    class="btn btn-sm btn-success"
                    onclick="return confirm('¿Reactivar este dispositivo?')">
                    🔼 Dar de alta
                  </a>
                <?php else: ?>
                  <span class="text-muted">✅ Activo</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="alert alert-info">No hay dispositivos dados de baja.</div>
    <?php endif; ?>

  </div>

  <!-- JS para modo oscuro -->
  <script src="public/js/tema.js"></script>

</body>

</html>