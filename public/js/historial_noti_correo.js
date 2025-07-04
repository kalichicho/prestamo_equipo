
  // Espera a que el DOM esté completamente cargado
  document.addEventListener("DOMContentLoaded", () => {
    const alerta = document.querySelector(".alert");
    if (alerta) {
      // Oculta la alerta después de 3 segundos (3000 milisegundos)
      setTimeout(() => {
        alerta.style.transition = "opacity 0.5s ease";
        alerta.style.opacity = "0";
        setTimeout(() => alerta.remove(), 500); // Elimina del DOM tras el desvanecido
      }, 3000);
    }
  });

