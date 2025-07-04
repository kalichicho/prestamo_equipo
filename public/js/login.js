// Seleccionamos los elementos clave del SVG y otros elementos de la vista
const ojoIzq = document.getElementById("ojo-izquierdo");
const ojoDer = document.getElementById("ojo-derecho");
const parpadoIzq = document.getElementById("parpado-izquierdo");
const parpadoDer = document.getElementById("parpado-derecho");
const mensaje = document.getElementById("mensaje-exito");

// -----------------------------
// Movimiento de los ojos con el ratón
// -----------------------------
document.addEventListener("mousemove", (e) => {
  const rect = ojoIzq.closest("svg").getBoundingClientRect();
  const cx = rect.left + rect.width / 2;
  const cy = rect.top + rect.height / 2;
  const dx = Math.max(Math.min((e.clientX - cx) / 25, 5), -5);
  const dy = Math.max(Math.min((e.clientY - cy) / 25, 4), -4);
  ojoIzq.setAttribute("transform", `translate(${135 + dx} ${140 + dy})`);
  ojoDer.setAttribute("transform", `translate(${165 + dx} ${140 + dy})`);
});

// -----------------------------
// Mostrar párpados al escribir contraseña
// -----------------------------
const password = document.getElementById("password");
password.addEventListener("focus", () => {
  ojoIzq.style.display = "none";
  ojoDer.style.display = "none";
  parpadoIzq.style.display = "block";
  parpadoDer.style.display = "block";
});
password.addEventListener("blur", () => {
  ojoIzq.style.display = "block";
  ojoDer.style.display = "block";
  parpadoIzq.style.display = "none";
  parpadoDer.style.display = "none";
});

// -----------------------------
// GUIÑO al iniciar sesión exitosamente
// -----------------------------
// 👁️‍🗨️ GUIÑO: al detectar ?ok=1 en la URL, cerrar y abrir solo el ojo derecho como guiño
const urlParams = new URLSearchParams(window.location.search);
const loginOk = urlParams.get('ok') === '1';

if (loginOk && ojoDer && parpadoDer) {
  // 👁️ Oculta el ojo derecho y muestra el párpado derecho durante 250ms
  ojoDer.style.display = "none";
  parpadoDer.style.display = "block";

  setTimeout(() => {
    ojoDer.style.display = "block";
    parpadoDer.style.display = "none";
  }, 250);
}



