<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de Movimientos</title>

    <!-- Bootstrap CSS & DataTables -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Tema oscuro -->
    <link rel="stylesheet" href="public/css/tema.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
      .btn-top { transition: transform .2s; }
      .btn-top:hover { transform: translateY(-2px); }
      /* Ajustar badge de firma */
      .firma-indicador { font-size: .8rem; }
    </style>
</head>

<body class="bg-light">
  <div class="container mt-5">

    <h2 class="mb-4">Historial de Movimientos</h2>

    <?php if (!empty($_SESSION['success'])): ?>
      <div class="alert alert-success text-center"><?= htmlspecialchars($_SESSION['success']) ?></div>
      <?php unset($_SESSION['success']); ?>
    <?php elseif (!empty($_SESSION['error'])): ?>
      <div class="alert alert-danger text-center"><?= htmlspecialchars($_SESSION['error']) ?></div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="d-flex justify-content-end align-items-center gap-2 mb-4">
      <a href="index.php?c=prestamo&a=dashboard" class="btn btn-outline-secondary btn-top">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
      <button id="toggle-dark-mode" class="btn btn-outline-secondary btn-top">
        <i class="bi bi-moon-fill"></i>
      </button>
    </div>

    <form method="GET" action="index.php" class="mb-4 row g-2">
      <input type="hidden" name="c" value="prestamo">
      <input type="hidden" name="a" value="historial">

      <div class="col-md-6 position-relative">
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person-lines-fill"></i></span>
          <input type="text" id="buscar_usuario" name="usuario_query" class="form-control" placeholder="Buscar por nombre o email" autocomplete="off">
        </div>
        <input type="hidden" name="usuario_id" id="usuario_id">
        <div id="resultados_usuario" class="list-group position-absolute w-100"></div>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Buscar</button>
      </div>
    </form>

    <?php if (!empty($movimientos)): ?>
      <table id="tabla_historial" class="table table-striped table-bordered">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Tipo</th>
            <th>Fecha</th>
            <th>Usuario</th>
            <th>Unidad</th>
            <th>Ubicación</th>
            <th>Dispositivos</th>
            <th class="text-center">Acciones / Firma</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($movimientos as $m): ?>
            <tr>
              <td><?= htmlspecialchars($m['id']) ?></td>
              <td><?= ucfirst(htmlspecialchars($m['tipo_operacion'])) ?></td>
              <td><?= htmlspecialchars($m['fecha_prestamo']) ?></td>
              <td><?= htmlspecialchars($m['usuario']) ?></td>
              <td><?= htmlspecialchars($m['unidad']) ?></td>
              <td><?= htmlspecialchars($m['ubicacion']) ?></td>
              <td><?= htmlspecialchars($m['dispositivos']) ?></td>
              <td class="text-center">
                <div class="btn-group me-2" role="group">
                  <a class="btn btn-outline-danger btn-sm" href="index.php?c=pdf&a=ver&id=<?= $m['id'] ?>"
                     target="_blank" title="Ver PDF">
                    <i class="bi bi-file-earmark-pdf"></i>
                  </a>
                  <form method="POST" action="index.php?c=correo&a=enviar"
                        onsubmit="return confirm('¿Deseas reenviar el PDF?');">
                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                    <button type="submit" class="btn btn-outline-primary btn-sm" title="Reenviar por correo">
                      <i class="bi bi-envelope-paper"></i>
                    </button>
                  </form>
                  <?php if (!$m['firma_empleado']): ?>
                    <button type="button" class="btn btn-outline-success btn-sm"
                            data-id="<?= $m['id'] ?>"
                            data-bs-toggle="modal"
                            data-bs-target="#modalFirma"
                            title="Firmar">
                      <i class="bi bi-pencil-square"></i>
                    </button>
                  <?php else: ?>
                    <button class="btn btn-outline-secondary btn-sm" disabled title="Ya firmado">
                      <i class="bi bi-check2-circle"></i>
                    </button>
                  <?php endif; ?>
                </div>
                <i class="bi bi-circle-fill firma-indicador <?= $m['firma_empleado'] ? 'text-success' : 'text-danger' ?>"
                   title="<?= $m['firma_empleado'] ? 'Firmado' : 'No firmado' ?>"></i>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="alert alert-info">No hay movimientos registrados o no se encontraron resultados.</div>
    <?php endif; ?>

  </div>

  <!-- Modal de firma -->
  <div class="modal fade" id="modalFirma" tabindex="-1" aria-labelledby="modalFirmaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <form id="formFirmaEmpleado" method="POST" action="index.php?c=prestamo&a=guardarFirmaEmpleado">
          <div class="modal-header">
            <h5 class="modal-title" id="modalFirmaLabel">Firma del empleado</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body text-center">
            <canvas id="canvasFirmaEmpleado" width="400" height="150"
                    class="border w-100" style="max-width:400px;"></canvas>
            <input type="hidden" name="firmaCanvas" id="firmaCanvasEmpleado">
            <input type="hidden" name="prestamo_id" id="firmaMovimientoId">
          </div>
          <div class="modal-footer">
            <button type="button" id="limpiarFirmaEmpleado" class="btn btn-outline-secondary">
              <i class="bi bi-eraser"></i> Limpiar
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save2"></i> Guardar firma
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- JS libs -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

  <!-- Scripts personalizados -->
  <script src="public/js/historial.js"></script>
  <script src="public/js/historial_noti_correo.js"></script>
  <script src="public/js/historial_firma_empleado.js"></script>
  <script src="public/js/tema.js"></script>

  <script>
    // Inicializar DataTable solo una vez
    $(document).ready(function() {
      if (!$.fn.DataTable.isDataTable('#tabla_historial')) {
        $('#tabla_historial').DataTable({
          dom: 'Bfrtip',
          buttons: [
            { extend: 'pdfHtml5', text: '<i class="bi bi-file-earmark-pdf"></i> PDF', className: 'btn btn-outline-danger btn-sm' },
            { extend: 'excelHtml5', text: '<i class="bi bi-file-earmark-excel"></i> Excel', className: 'btn btn-outline-success btn-sm' }
          ],
          language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
        });
      }
    });

    // Modal firma: asignar prestamo_id
    var modalFirma = document.getElementById('modalFirma');
    modalFirma.addEventListener('show.bs.modal', function(e) {
      document.getElementById('firmaMovimientoId').value = e.relatedTarget.getAttribute('data-id');
    });
  </script>
</body>

</html>
