// === TOGGLE DEL MODO OSCURO ===
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('toggle-dark-mode');
    if (localStorage.getItem('modo_oscuro') === 'true') {
      document.body.classList.add('dark-mode');
    }
  
    if (toggle) {
      toggle.addEventListener('click', function () {
        document.body.classList.toggle('dark-mode');
        localStorage.setItem('modo_oscuro', document.body.classList.contains('dark-mode'));
      });
    }
  });
  