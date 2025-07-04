<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Dispositivos asignados</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Estilo personalizado -->
  <link rel="stylesheet" href="public/css/asignados.css">
  <!-- CSS para modo oscuro -->
  <link rel="stylesheet" href="public/css/tema.css">

</head>

<body class="bg-light">
  <div class="container mt-5">

    <!-- Título con nombre y email del usuario -->
    <h2 class="mb-4">
      Dispositivos asignados a <?= htmlspecialchars($usuario['nombre']) ?>
      <small class="text-muted">(<?= htmlspecialchars($usuario['email']) ?>)</small>
    </h2>
    <!-- Botón para cambiar modo -->
    <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
      <a href="index.php?c=prestamo&a=dashboard" class="btn btn-outline-secondary">
        ← Volver
      </a>
      <button id="toggle-dark-mode" class="btn btn-outline-secondary">
        🌓 Modo oscuro
      </button>
    </div>

    <!-- Tabla o mensaje según resultados -->
    <?php if (!empty($dispositivos)): ?>
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
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
              <td><?= htmlspecialchars($d['etiqueta_empresa']) ?></td>
              <td><?= htmlspecialchars($d['tipo']) ?></td>
              <td><?= htmlspecialchars($d['marca']) ?></td>
              <td><?= htmlspecialchars($d['modelo']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Botón para enviar por correo la hoja de préstamo -->
      <?php if (isset($prestamo_id_actual)): ?>
        <form method="POST" action="index.php?c=correo&a=enviar" onsubmit="return confirm('¿Enviar correo con la hoja de préstamo actual?')">
          <input type="hidden" name="id" value="<?= $prestamo_id_actual ?>">
          <button type="submit" class="btn btn-outline-primary">✉️ Enviar hoja por correo</button>
        </form>
      <?php endif; ?>

    <?php else: ?>
      <div class="alert alert-warning">Este usuario no tiene dispositivos asignados actualmente.</div>
    <?php endif; ?>

    <!-- Botón volver -->

  </div>

  <!-- JS para modo oscuro -->
  <script src="public/js/tema.js"></script>

</body>

</html>