<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Buscar Dispositivo</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- CSS personalizado -->
  <link rel="stylesheet" href="public/css/buscar_asignado.css">
  <!-- CSS para modo oscuro -->
  <link rel="stylesheet" href="public/css/tema.css">

</head>

<body class="bg-light">
  <div class="container mt-5">

    <!-- Título -->
    <h2 class="mb-4">Buscar dispositivo por etiqueta o Nº de serie</h2>
    <!-- Botón para cambiar modo -->
    <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
      <a href="index.php?c=prestamo&a=dashboard" class="btn btn-outline-secondary">
        ← Volver
      </a>
      <button id="toggle-dark-mode" class="btn btn-outline-secondary">
        🌓 Modo oscuro
      </button>
    </div>


    <!-- Formulario de búsqueda -->
    <form method="GET" action="index.php">
      <input type="hidden" name="c" value="dispositivo">
      <input type="hidden" name="a" value="buscarAsignado">

      <div class="row mb-3">
        <div class="col-md-6">
          <input type="text" name="busqueda" class="form-control" placeholder="Introduce número de etiqueta o serie..." required>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
      </div>
    </form>

    <!-- Resultados -->
    <?php if (!empty($resultados)): ?>
      <table class="table table-bordered">
        <thead class="table-dark">
          <tr>
            <th>Etiqueta</th>
            <th>Tipo</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Asignado a</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($resultados as $d): ?>
            <tr>
              <td><?= htmlspecialchars($d['etiqueta_empresa']) ?></td>
              <td><?= htmlspecialchars($d['tipo']) ?></td>
              <td><?= htmlspecialchars($d['marca']) ?></td>
              <td><?= htmlspecialchars($d['modelo']) ?></td>
              <td>
                <?php if (!empty($d['usuario_nombre'])): ?>
                  <?= htmlspecialchars($d['usuario_nombre']) ?> (<?= htmlspecialchars($d['email']) ?>)
                <?php else: ?>
                  <span class="text-muted">No asignado</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php elseif (isset($_GET['busqueda'])): ?>
      <div class="alert alert-warning">No se encontró ningún dispositivo con ese dato.</div>
    <?php endif; ?>

    

  </div>

  <!-- JS para modo oscuro -->
  <script src="public/js/tema.js"></script>

</body>

</html>