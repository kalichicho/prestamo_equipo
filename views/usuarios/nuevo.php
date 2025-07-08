<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrar Usuario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- CSS para modo oscuro -->
  <link rel="stylesheet" href="public/css/tema.css">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <h2 class="mb-4">Dar de alta nuevo usuario</h2>
    <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
      <a href="index.php?c=prestamo&a=dashboard" class="btn btn-outline-secondary">← Volver</a>
      <button id="toggle-dark-mode" class="btn btn-outline-secondary">🌓 Modo oscuro</button>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?c=usuario&a=guardar" class="card p-4 shadow-sm">
      <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input type="password" name="contrasena" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Rol</label>
        <select name="rol" class="form-select">
          <option value="empleado">Empleado</option>
          <option value="tecnico">Técnico</option>
          <option value="administrador">Administrador</option>
        </select>
      </div>
      <button type="submit" class="btn btn-success w-100">Registrar</button>
    </form>
  </div>

  <script src="public/js/tema.js"></script>
</body>
</html>
