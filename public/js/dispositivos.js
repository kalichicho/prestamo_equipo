document.addEventListener("DOMContentLoaded", function () {
    $('#tabla_dispositivos').DataTable({
      order: [[0, 'asc']],
      language: {
        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
      }
    });
  });
  