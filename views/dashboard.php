
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- CSS para modo oscuro -->
  <link rel="stylesheet" href="public/css/tema.css">

  <style>
    .dashboard-card {
      transition: transform 0.2s ease-in-out;
      cursor: pointer;
      border-radius: 10px;
      background-color: #fff;
    }
    .dashboard-card:hover {
      transform: scale(1.03);
    }
    .dashboard-icon {
      font-size: 2rem;
      margin-bottom: 10px;
      color: #0d6efd;
    }
    /* Texto botón modo oscuro en dark-mode */
    body.dark-mode #toggle-dark-mode {
      color: #d1d1d1;
      border-color: #333;
    }
  </style>
</head>

<body class="bg-light">
  <div class="container py-5">

    <!-- Título de bienvenida -->
    <h2 class="mb-5 text-center">
      Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?> (<?= htmlspecialchars($_SESSION['rol']) ?>)
    </h2>

    <!-- Botones superiores: Pendientes, Firmar y Modo oscuro -->
    <div class="d-flex justify-content-end align-items-center gap-2 mb-4">
      <?php if ($_SESSION['rol'] === 'administrador'): ?>
        <a href="index.php?c=pendiente&a=index" class="btn btn-outline-dark position-relative">
          <i class="bi bi-folder2-open"></i> Pendientes
          <?php if (!empty($pendientes_no_revisados)): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
              <?= intval($pendientes_no_revisados) ?>
            </span>
          <?php endif; ?>
        </a>
      <?php endif; ?>

      <?php if (in_array($_SESSION['rol'], ['administrador','tecnico'])): ?>
        <a href="index.php?c=usuario&a=firma" class="btn btn-outline-dark">
          <i class="bi bi-pencil-square"></i> Firmar
        </a>
      <?php endif; ?>

      <button id="toggle-dark-mode" class="btn btn-outline-dark">
        <i class="bi bi-moon-stars"></i>
      </button>
    </div>

    <!-- Grid de tarjetas -->
    <div class="row g-4">
      <?php if (in_array($_SESSION['rol'], ['administrador','tecnico'])): ?>
        <!-- Registrar préstamo/devolución -->
        <div class="col-md-4">
          <a href="index.php?c=prestamo&a=crear" class="text-decoration-none text-dark">
            <div class="card text-center p-4 dashboard-card shadow-sm">
              <div class="dashboard-icon"><i class="bi bi-journal-plus"></i></div>
              <h5 class="card-title">Registrar préstamo/devolución</h5>
            </div>
          </a>
        </div>

        <!-- Historial de movimientos -->
        <div class="col-md-4">
          <a href="index.php?c=prestamo&a=historial" class="text-decoration-none text-dark">
            <div class="card text-center p-4 dashboard-card shadow-sm">
              <div class="dashboard-icon"><i class="bi bi-clock-history"></i></div>
              <h5 class="card-title">Historial de movimientos</h5>
            </div>
          </a>
        </div>

        <!-- Gestión de dispositivos -->
        <div class="col-md-4">
          <a href="index.php?c=dispositivo&a=index" class="text-decoration-none text-dark">
            <div class="card text-center p-4 dashboard-card shadow-sm">
              <div class="dashboard-icon"><i class="bi bi-laptop"></i></div>
              <h5 class="card-title">Gestión de dispositivos</h5>
            </div>
          </a>
        </div>

        <!-- Buscar dispositivos por usuario -->
        <div class="col-md-4">
          <a href="index.php?c=usuario&a=asignados" class="text-decoration-none text-dark">
            <div class="card text-center p-4 dashboard-card shadow-sm">
              <div class="dashboard-icon"><i class="bi bi-person-lines-fill"></i></div>
              <h5 class="card-title">Buscar dispositivos por usuario</h5>
            </div>
          </a>
        </div>

        <!-- Mis dispositivos asignados -->
        <div class="col-md-4">
          <a href="index.php?c=usuario&a=asignadosActuales" class="text-decoration-none text-dark">
            <div class="card text-center p-4 dashboard-card shadow-sm">
              <div class="dashboard-icon"><i class="bi bi-box-seam"></i></div>
              <h5 class="card-title">Mis dispositivos asignados</h5>
            </div>
          </a>
        </div>

        <!-- Buscar por etiqueta o serie -->
        <div class="col-md-4">
          <a href="index.php?c=dispositivo&a=buscarAsignado" class="text-decoration-none text-dark">
            <div class="card text-center p-4 dashboard-card shadow-sm">
              <div class="dashboard-icon"><i class="bi bi-search"></i></div>
              <h5 class="card-title">Buscar dispositivo por etiqueta o serie</h5>
            </div>
          </a>
        </div>

        <!-- Stats de dispositivos -->
        <div class="col-md-4">
          <a href="index.php?c=dispositivo&a=stats" class="text-decoration-none text-dark">
            <div class="card text-center p-4 dashboard-card shadow-sm">
              <div class="dashboard-icon"><i class="bi bi-bar-chart-line"></i></div>
              <h5 class="card-title">Stats de Dispositivos</h5>
            </div>
          </a>
        </div>

        <!-- Nueva: Alertas de Seguridad -->
        <div class="col-md-4">
          <a href="index.php?c=alertas&a=index" class="text-decoration-none text-dark">
            <div class="card text-center p-4 dashboard-card shadow-sm border-danger">
              <div class="dashboard-icon text-danger"><i class="bi bi-shield-lock"></i></div>
              <h5 class="card-title text-danger">Alertas de Seguridad</h5>
            </div>
          </a>
        </div>

      <?php else: ?>
        <!-- Empleado: Historial de movimientos -->
        <div class="col-md-4">
          <a href="index.php?c=usuario&a=misPrestamos" class="text-decoration-none text-dark">
            <div class="card text-center p-4 dashboard-card shadow-sm">
              <div class="dashboard-icon"><i class="bi bi-clock-history"></i></div>
              <h5 class="card-title">Historial de movimientos</h5>
            </div>
          </a>
        </div>

        <!-- Empleado: Mis dispositivos asignados -->
        <div class="col-md-4">
          <a href="index.php?c=usuario&a=asignadosActuales" class="text-decoration-none text-dark">
            <div class="card text-center p-4 dashboard-card shadow-sm">
              <div class="dashboard-icon"><i class="bi bi-box-seam"></i></div>
              <h5 class="card-title">Mis dispositivos asignados</h5>
            </div>
          </a>
        </div>
      <?php endif; ?>

      <!-- Cerrar sesión -->
      <div class="col-md-4 <?php if(!in_array($_SESSION['rol'], ['administrador','tecnico'])) echo 'offset-md-4'; ?>">
        <a href="index.php?c=auth&a=logout" class="text-decoration-none">
          <div class="card text-center p-4 dashboard-card shadow-sm border-danger">
            <div class="dashboard-icon text-danger"><i class="bi bi-box-arrow-right"></i></div>
            <h5 class="card-title text-danger">Cerrar sesión</h5>
          </div>
        </a>
      </div>

    </div>
  </div>

  <!-- JS para modo oscuro -->
  <script src="public/js/tema.js"></script>
</body>
</html>
