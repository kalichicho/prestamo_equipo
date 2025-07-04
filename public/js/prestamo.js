/**
 * public/js/prestamo.js
 *
 * Lógica de búsqueda de empleados, búsqueda de dispositivos,
 * manejo de préstamos/devoluciones y dinámica de selección/deselección.
 */
document.addEventListener('DOMContentLoaded', () => {
  // ────────────────────────────────────────────────────────────────
  // 0) Gestión de tarjetas de dispositivos seleccionados
  // ────────────────────────────────────────────────────────────────
  const tpl = document.getElementById('device-card-tpl');            // <template> de la vista
  const selectedContainer = document.getElementById('dispositivos_seleccionados'); // contenedor de tarjetas

  /**
   * Clona la plantilla, rellena label y input hidden, evita duplicados.
   * @param {string|number} id
   * @param {string}       label
   */
  function addDeviceCard(id, label) {
    if (!tpl || !selectedContainer) return;
    // Si ya existe, no añadimos otra igual
    if (selectedContainer.querySelector(`[data-id="${id}"]`)) return;

    const frag = tpl.content.cloneNode(true);
    const card = frag.querySelector('div.card');
    card.dataset.id = id;
    card.querySelector('.badge-label').textContent = label;
    card.querySelector('.badge-input').value = id;
    selectedContainer.appendChild(frag);
  }

  // Delegación: si clicas en SVG o PATH dentro de .remove-device, elimina la tarjeta
  selectedContainer.addEventListener('click', e => {
    const removeBtn = e.target.closest('.remove-device');
    if (removeBtn) {
      const card = removeBtn.closest('.card');
      if (card) card.remove();
    }
  });

  // ────────────────────────────────────────────────────────────────
  // 1) Autocomplete de empleado
  // ────────────────────────────────────────────────────────────────
  const empInput = document.getElementById('busqueda_empleado');
  const empResults = document.getElementById('resultados_empleado');

  empInput.addEventListener('input', function () {
    const q = this.value.trim();
    if (q.length < 2) {
      empResults.innerHTML = '';
      return;
    }
    fetch('index.php?c=usuario&a=buscarAjax&q=' + encodeURIComponent(q))
      .then(r => r.json())
      .then(data => {
        empResults.innerHTML = '';
        data.forEach(u => {
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.classList.add('list-group-item', 'list-group-item-action');
          btn.textContent = `${u.nombre} (${u.email})`;
          btn.dataset.id = u.id;
          btn.addEventListener('click', () => {
            empInput.value = u.nombre;
            document.getElementById('usuario_id_hidden').value = u.id;
            empResults.innerHTML = '';
            manejarCambio();
          });
          empResults.appendChild(btn);
        });
      });
  });

  // ────────────────────────────────────────────────────────────────
  // 2) Autocomplete de dispositivos para préstamo
  // ────────────────────────────────────────────────────────────────
  // ────────────────────────────────────────────────────────────────
  // 2) Autocomplete de dispositivos para préstamo
  // ────────────────────────────────────────────────────────────────
  const devInput = document.getElementById('busqueda_dispositivo');
  const devResults = document.getElementById('resultados_dispositivo');

  devInput.addEventListener('input', function () {
    const q = this.value.trim();
    if (q.length < 2) {
      devResults.innerHTML = '';
      return;
    }
    fetch('index.php?c=dispositivo&a=buscarAjax&q=' + encodeURIComponent(q))
      .then(r => r.json())
      .then(data => {
        devResults.innerHTML = '';
        data.forEach(d => {
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.classList.add('list-group-item', 'list-group-item-action');

          if (d.usuario_id_prestamo_actual) {
            // 1) Si está prestado, lo grisamos y deshabilitamos
            btn.classList.add('disabled', 'text-muted');
            btn.textContent = `${d.etiqueta_empresa} – dispositivo en préstamo a ${d.usuario_nombre}`;
          } else {
            // 2) Si no, podemos seleccionarlo normalmente
            btn.textContent = `${d.etiqueta_empresa} – ${d.tipo} – ${d.marca} ${d.modelo}`;
            btn.dataset.id = d.id;
            btn.addEventListener('click', () => {
              addDeviceCard(d.id, btn.textContent.trim());
              devInput.value = '';
              devResults.innerHTML = '';
            });
          }

          devResults.appendChild(btn);
        });
      });
  });

  // ────────────────────────────────────────────────────────────────
  // 3) Mostrar/ocultar buscador y tabla según Tipo de operación
  // ────────────────────────────────────────────────────────────────
  function manejarCambio() {
    const tipo = document.getElementById('tipo_operacion').value;
    const usuarioId = document.getElementById('usuario_id_hidden').value;
    const tablaCont = document.getElementById('tabla-dispositivos');
    const buscadorBlock = document.getElementById('buscador-dispositivos');
    const cardsCont = selectedContainer; // contenedor de tarjetas de préstamo

    if (tipo === 'devolucion' && usuarioId) {
      // 1) Limpiar tarjetas de préstamos previos
      cardsCont.innerHTML = '';

      // 2) Ocultar buscador de nuevo préstamo
      buscadorBlock.style.display = 'none';

      // 3) Pintar tabla de devolución con checkboxes
      fetch(`index.php?c=prestamo&a=dispositivosPrestadosPorUsuario&usuario_id=${usuarioId}`)
        .then(r => r.json())
        .then(arr => {
          if (!arr.length) {
            tablaCont.innerHTML = "<div class='alert alert-warning'>Este usuario no tiene dispositivos prestados.</div>";
            return;
          }
          let html = `
                <table class="table table-bordered mt-3">
                  <thead>
                    <tr>
                      <th>Seleccionar</th><th>Etiqueta</th><th>Tipo</th><th>Marca</th><th>Modelo</th>
                    </tr>
                  </thead><tbody>`;
          arr.forEach(d => {
            html += `
                  <tr>
                    <td><input type="checkbox" name="dispositivos[]" value="${d.id}"></td>
                    <td>${d.etiqueta_empresa}</td>
                    <td>${d.tipo}</td>
                    <td>${d.marca}</td>
                    <td>${d.modelo}</td>
                  </tr>`;
          });
          html += `</tbody></table>`;
          tablaCont.innerHTML = html;
        });
    } else {
      // 1) Limpiar tabla de devolución previa
      tablaCont.innerHTML = '';
      // 2) Mostrar buscador de préstamo
      buscadorBlock.style.display = 'block';
      // 3) Limpiar tarjetas de devolución previas (aunque no haya, por si acaso)
      cardsCont.innerHTML = '';
    }
  }


  // Listener para el select de tipo de operación
  document.getElementById('tipo_operacion').addEventListener('change', manejarCambio);

  // Ejecuta al cargar para ajustar la vista según el valor inicial
  manejarCambio();
});
