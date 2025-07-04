
//Sirve para que el técnico pueda buscar usuarios por nombre o email en tiempo real, 
// y al hacer clic sobre uno de ellos, se redirige automáticamente a la vista que muestra sus dispositivos asignados.
document.getElementById('buscar_usuario').addEventListener('input', function() {
    const query = this.value;
    if (query.length < 2) {
        document.getElementById('resultados_usuario').innerHTML = '';
        return;
    }

    fetch(`index.php?c=usuario&a=buscarAjax&q=${encodeURIComponent(query)}`)
        .then(r => r.json())
        .then(data => {
            const lista = document.getElementById('resultados_usuario');
            lista.innerHTML = '';
            data.forEach(usuario => {
                const item = document.createElement('button');
                item.classList.add('list-group-item', 'list-group-item-action');
                item.textContent = `${usuario.nombre} (${usuario.email})`;
                item.onclick = () => {
                    window.location.href = `index.php?c=usuario&a=asignados&id=${usuario.id}`;
                };
                lista.appendChild(item);
            });
        });
});