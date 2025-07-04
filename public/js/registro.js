document.addEventListener('DOMContentLoaded', function () {
    const tipoOperacion = document.getElementById('tipo_operacion');
    const usuario = document.getElementById('usuario_id');
  
    tipoOperacion.addEventListener('change', toggleDispositivosUsuario);
    usuario.addEventListener('change', toggleDispositivosUsuario);
  
    function toggleDispositivosUsuario() {
      const tipo = tipoOperacion.value;
      const user = usuario.value;
  
      if (tipo === 'devolucion' && user) {
        fetch(`index.php?c=prestamo&a=dispositivosPrestadosPorUsuario&usuario_id=${user}`)
          .then(res => res.json())
          .then(data => {
            const contenedor = document.getElementById('dispositivos-usuario');
            const tbody = document.getElementById('tabla-dispositivos-usuario');
            tbody.innerHTML = '';
  
            if (data.length > 0) {
              contenedor.style.display = 'block';
              data.forEach(d => {
                const row = `
                  <tr>
                    <td><input type="checkbox" name="dispositivos[]" value="${d.id}"></td>
                    <td>${d.etiqueta_empresa}</td>
                    <td>${d.tipo}</td>
                    <td>${d.marca}</td>
                    <td>${d.modelo}</td>
                  </tr>`;
                tbody.insertAdjacentHTML('beforeend', row);
              });
            } else {
              contenedor.style.display = 'none';
            }
          });
      } else {
        document.getElementById('dispositivos-usuario').style.display = 'none';
      }
    }
  });
  