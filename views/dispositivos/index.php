<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Gestión de Dispositivos</title>

  <!-- Bootstrap & DataTables CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Tema oscuro -->
  <link rel="stylesheet" href="public/css/tema.css">

  <style>
    body.dark-mode #toggle-dark-mode { color: #d1d1d1; border-color: #333; }

    .table-container {
      background: var(--bs-body-bg);
      border-radius: .5rem;
      padding: 1.5rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    h2 { font-weight: 600; margin-bottom: 1rem; }

    .btn-top { transition: transform .15s; }
    .btn-top:hover { transform: translateY(-2px); }

    .action-btn {
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      font-size: .9rem;
      padding: .35rem .6rem;
      margin-bottom: .2rem;
    }
  </style>
</head>

<body class="bg-light">
  <div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Gestión de Dispositivos</h2>
      <div class="btn-group">
        <a href="index.php?c=prestamo&a=dashboard" class="btn btn-outline-secondary btn-top">
          <i class="bi bi-arrow-left"></i> Volver
        </a>
        <button id="toggle-dark-mode" class="btn btn-outline-secondary btn-top">
          <i class="bi bi-moon-stars"></i>
        </button>
      </div>
    </div>

    <form method="GET" action="index.php" class="row gx-2 gy-3 align-items-end mb-4">
      <input type="hidden" name="c" value="dispositivo">
      <input type="hidden" name="a" value="index">
      <div class="col-md-4">
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" name="buscar" class="form-control"
            placeholder="Buscar por etiqueta o nº de serie..."
            value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>">
        </div>
      </div>
      <div class="col-md-2">
        <select name="tipo" class="form-select">
          <option value="">-- Tipo --</option>
          <?php foreach ($tiposUnicos as $t): ?>
            <option value="<?= htmlspecialchars($t) ?>" <?= (($_GET['tipo'] ?? '') === $t) ? 'selected' : '' ?>>
              <?= htmlspecialchars(ucfirst($t)) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <select name="marca" class="form-select">
          <option value="">-- Marca --</option>
          <?php foreach ($marcasUnicas as $m): ?>
            <option value="<?= htmlspecialchars($m) ?>" <?= (($_GET['marca'] ?? '') === $m) ? 'selected' : '' ?>>
              <?= htmlspecialchars($m) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-1 d-grid">
        <button type="submit" class="btn btn-primary">Buscar</button>
      </div>
      <div class="col-md-2 d-grid">
        <a href="index.php?c=dispositivo&a=nuevo" class="btn btn-success">+ Nuevo dispositivo</a>
      </div>
      <div class="col-md-1 d-grid">
        <a href="index.php?c=dispositivo&a=historialBajas" class="btn btn-outline-secondary">
          <i class="bi bi-clock-history"></i>
        </a>
      </div>
    </form>

    <div class="table-container">
      <table id="tabla_dispositivos" class="table table-striped table-bordered align-middle mb-0">
        <thead class="table-dark">
          <tr>
            <th>Etiqueta</th>
            <th>Tipo</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Nº Serie</th>
            <th>Compra</th>
            <th>Fin Garantía</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($dispositivos as $d): ?>
            <tr>
              <td><?= htmlspecialchars($d['etiqueta_empresa']) ?></td>
              <td><?= htmlspecialchars(ucfirst($d['tipo'])) ?></td>
              <td><?= htmlspecialchars($d['marca']) ?></td>
              <td><?= htmlspecialchars($d['modelo']) ?></td>
              <td><?= htmlspecialchars($d['num_serie']) ?></td>
              <td><?= htmlspecialchars($d['fecha_compra']) ?></td>
              <td><?= htmlspecialchars($d['fin_garantia']) ?></td>
              <td class="text-center">
                <!-- Editar -->
                <a href="index.php?c=dispositivo&a=editar&id=<?= $d['id'] ?>"
                   class="btn btn-warning action-btn" title="Editar">
                  <i class="bi bi-pencil"></i> Editar
                </a>

                <?php if (!empty($d['usuario_nombre'])): ?>
                  <!-- Prestado -->
                  <span class="btn btn-secondary action-btn" title="Prestado a <?= htmlspecialchars($d['usuario_nombre']) ?>">
                    <i class="bi bi-box-seam"></i>
                    Prestado a <?= htmlspecialchars($d['usuario_nombre']) ?>
                  </span>
                <?php else: ?>
                  <!-- Eliminar -->
                  <a href="index.php?c=dispositivo&a=eliminar&id=<?= $d['id'] ?>"
                     onclick="return confirm('¿Eliminar este dispositivo?')"
                     class="btn btn-danger action-btn" title="Eliminar">
                    <i class="bi bi-trash"></i> Eliminar
                  </a>
                <?php endif; ?>

                <?php if ($d['estado'] === 'activo' && empty($d['usuario_nombre'])): ?>
                  <!-- Dar de baja -->
                  <form method="POST" action="index.php?c=dispositivo&a=darBaja" class="d-inline">
                    <input type="hidden" name="id" value="<?= $d['id'] ?>">
                    <button type="submit" class="btn btn-outline-danger action-btn" title="Dar de baja">
                      <i class="bi bi-dash-circle"></i> Baja
                    </button>
                  </form>
                <?php elseif ($d['estado'] === 'baja'): ?>
                  <!-- Estado dado de baja -->
                  <span class="badge bg-danger mt-1">Dado de baja</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div>

  <!-- JS libs -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script>
    $(function() {
      $('#tabla_dispositivos').DataTable({
        responsive: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        columnDefs: [{ orderable: false, targets: -1 }]
      });
    });

    // Modo oscuro persistente
    const btn = document.getElementById('toggle-dark-mode');
    btn.addEventListener('click', () => {
      document.body.classList.toggle('dark-mode');
      localStorage.setItem('modoOscuro', document.body.classList.contains('dark-mode'));
    });
    if (localStorage.getItem('modoOscuro') === 'true') {
      document.body.classList.add('dark-mode');
    }
  </script>
</body>

</html>
