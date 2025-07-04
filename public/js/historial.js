// Inicialización de DataTables con botón de exportación PDF
$(document).ready(function () {
    $('#tabla_historial').DataTable({
      order: [[0, 'desc']],
      dom: 'Bfrtip',
      buttons: ['pdfHtml5'],
      language: {
        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
      }
    });
  });
  
  // Autocompletado de usuarios
  document.getElementById('buscar_usuario').addEventListener('input', function () {
    const query = this.value;
    if (query.length < 2) {
      document.getElementById('resultados_usuario').innerHTML = '';
      return;
    }
  
    fetch('index.php?c=usuario&a=buscarAjax&q=' + encodeURIComponent(query))
      .then(response => response.json())
      .then(data => {
        const resultados = document.getElementById('resultados_usuario');
        resultados.innerHTML = '';
        data.forEach(usuario => {
          const item = document.createElement('button');
          item.classList.add('list-group-item', 'list-group-item-action');
          item.textContent = `${usuario.nombre} (${usuario.email})`;
          item.addEventListener('click', () => {
            document.getElementById('buscar_usuario').value = usuario.nombre;
            document.getElementById('usuario_id').value = usuario.id;
            resultados.innerHTML = '';
          });
          resultados.appendChild(item);
        });
      });
  });









  
  


  