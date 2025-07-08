<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Préstamo / Devolución</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- CSS para modo oscuro -->
    <link rel="stylesheet" href="public/css/tema.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
      /* Tarjeta del formulario */
      .form-card {
        border-radius: 10px;
        overflow: hidden;
      }
      /* Botones superiores */
      .btn-top {
        transition: transform .2s;
      }
      .btn-top:hover {
        transform: translateY(-2px);
      }
      /* Lista de resultados */
      .list-group-item-action {
        cursor: pointer;
      }
      /* Tarjeta de dispositivo seleccionado */
      .device-card {
        border-radius: 6px;
      }
      .device-card .remove-device {
        width: 1rem;
        height: 1rem;
        cursor: pointer;
      }
    </style>
</head>

<body class="bg-light">
  <div class="container mt-5">

    <!-- Título -->
    <h2 class="mb-4">Registrar Préstamo / Devolución</h2>

    <!-- Botones: Volver + Modo oscuro -->
    <div class="d-flex justify-content-end align-items-center gap-2 mb-4">
      <a href="index.php?c=prestamo&a=dashboard" class="btn btn-outline-secondary btn-top">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
      <button id="toggle-dark-mode" class="btn btn-outline-secondary btn-top">
        <i class="bi bi-moon-fill"></i>
      </button>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?c=prestamo&a=guardar" class="card p-4 shadow-sm form-card">
      <!-- Tipo de operación -->
      <div class="mb-3">
        <label for="tipo_operacion" class="form-label">Tipo de operación</label>
        <select name="tipo_operacion" id="tipo_operacion" class="form-select" required>
          <option value="prestamo">Préstamo</option>
          <option value="devolucion">Devolución</option>
        </select>
      </div>

      <!-- Empleado -->
      <div class="mb-3">
        <label for="busqueda_empleado" class="form-label">Empleado</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
          <input type="text" id="busqueda_empleado" class="form-control" placeholder="Buscar por nombre o correo" autocomplete="off">
        </div>
        <input type="hidden" name="usuario_id" id="usuario_id_hidden">
        <div id="resultados_empleado" class="list-group mt-1"></div>
      </div>

      <!-- Tabla de dispositivos (para devolución) -->
      <div id="tabla-dispositivos" class="mb-3"></div>

      <!-- Buscador de dispositivos (para préstamo) -->
      <div id="buscador-dispositivos" class="mb-3">
        <label for="busqueda_dispositivo" class="form-label">Buscar dispositivo (etiqueta o serie)</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-hdd-stack"></i></span>
          <input type="text" id="busqueda_dispositivo" class="form-control" autocomplete="off">
        </div>
        <div id="resultados_dispositivo" class="list-group mt-1"></div>
        <div id="dispositivos_seleccionados" class="mt-3"></div>
      </div>

      <!-- Template para dispositivos seleccionados -->
      <template id="device-card-tpl">
        <div class="card mb-2 device-card">
          <div class="card-body d-flex align-items-center p-2">
            <span class="badge-label"></span>
            <svg class="remove-device ms-auto bi bi-x-circle-fill text-danger" viewBox="0 0 16 16" role="button" title="Eliminar">
              <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.146 4.146a.5.5 0 1 0-.708.708L7.293 8l-1.855 1.854a.5.5 0 1 0 .708.708L8 8.707l1.854 1.855a.5.5 0 1 0 .708-.708L8.707 8l1.855-1.854a.5.5 0 0 0-.708-.708L8 7.293 6.146 5.439z"/>
            </svg>
            <input type="hidden" name="dispositivos[]" class="badge-input" value="">
          </div>
        </div>
      </template>

      <!-- Fecha -->
      <div class="mb-3">
        <label for="fecha" class="form-label">Fecha</label>
        <input type="date" id="fecha" name="fecha" class="form-control" required>
      </div>

      <!-- Unidad -->
      <div class="mb-3">
        <label for="unidad" class="form-label">Unidad</label>
        <input type="text" id="unidad" name="unidad" class="form-control" required>
      </div>

      <!-- Ubicación -->
      <div class="mb-3">
        <label for="ubicacion" class="form-label">Ubicación</label>
        <input type="text" id="ubicacion" name="ubicacion" class="form-control" required>
      </div>

      <!-- Botón Guardar -->
      <button type="submit" class="btn btn-success w-100">
        <i class="bi bi-save"></i> Guardar
      </button>
    </form>
  </div>

  <!-- JS de la página -->
  <script src="public/js/prestamo.js"></script>
  <!-- JS para modo oscuro -->
  <script src="public/js/tema.js"></script>
</body>
</html>
