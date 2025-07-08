// === TOGGLE DEL MODO OSCURO ===
document.addEventListener('DOMContentLoaded', function () {
  const toggle = document.getElementById('toggle-dark-mode');

  const actualizarIcono = () => {
    if (!toggle) return;
    if (document.body.classList.contains('dark-mode')) {
      toggle.innerHTML = '<i class="bi bi-sun-fill"></i>';
    } else {
      toggle.innerHTML = '<i class="bi bi-moon-fill"></i>';
    }
  };

  if (localStorage.getItem('modo_oscuro') === 'true') {
    document.body.classList.add('dark-mode');
  }

  actualizarIcono();

  if (toggle) {
    toggle.addEventListener('click', function () {
      document.body.classList.toggle('dark-mode');
      localStorage.setItem('modo_oscuro', document.body.classList.contains('dark-mode'));
      actualizarIcono();
    });
  }
});
