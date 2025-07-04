//script para poder agregar un dispositivo de tipo "otro"
const tipoSelect = document.getElementById('tipo');
const contenedorPersonalizado = document.getElementById('tipo_personalizado_container');
const inputPersonalizado = document.getElementById('tipo_personalizado');

function actualizarVisibilidadTipo() {
    const esOtro = tipoSelect.value === 'otros';
    contenedorPersonalizado.style.display = esOtro ? 'block' : 'none';
    inputPersonalizado.required = esOtro;
    if (!esOtro) {
        inputPersonalizado.value = '';
    }
}

actualizarVisibilidadTipo();
tipoSelect.addEventListener('change', actualizarVisibilidadTipo);