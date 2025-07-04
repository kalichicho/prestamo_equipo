//script para firmar con el empleado

document.addEventListener('DOMContentLoaded', function () {
    let canvas, ctx;
    let dibujando = false;

    var modalFirma = document.getElementById('modalFirma');
    modalFirma.addEventListener('shown.bs.modal', function () {
        canvas = document.getElementById('canvasFirmaEmpleado');
        ctx = canvas.getContext('2d');

        function getMousePos(e) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            return {
                x: (e.clientX - rect.left) * scaleX,
                y: (e.clientY - rect.top) * scaleY
            };
        }

        function getTouchPos(e) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            const touch = e.touches[0];
            return {
                x: (touch.clientX - rect.left) * scaleX,
                y: (touch.clientY - rect.top) * scaleY
            };
        }

        // Eventos de ratón
        canvas.addEventListener('mousedown', function (e) {
            dibujando = true;
            const pos = getMousePos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        });

        canvas.addEventListener('mousemove', function (e) {
            if (!dibujando) return;
            const pos = getMousePos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
        });

        ['mouseup', 'mouseout'].forEach(event => 
            canvas.addEventListener(event, () => {
                dibujando = false;
                ctx.beginPath();
            })
        );

        // Eventos táctiles
        canvas.addEventListener('touchstart', function (e) {
            e.preventDefault();
            dibujando = true;
            const pos = getTouchPos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        });

        canvas.addEventListener('touchmove', function (e) {
            e.preventDefault();
            if (!dibujando) return;
            const pos = getTouchPos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
        });

        canvas.addEventListener('touchend', function (e) {
            e.preventDefault();
            dibujando = false;
            ctx.beginPath();
        });

        document.getElementById('limpiarFirmaEmpleado').addEventListener('click', function () {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });
    });

    document.querySelectorAll('[data-bs-target="#modalFirma"]').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            document.getElementById('firmaMovimientoId').value = id;
        });
    });

    document.getElementById('formFirmaEmpleado').addEventListener('submit', function (e) {
        document.getElementById('firmaCanvasEmpleado').value = canvas.toDataURL('image/png');
    });
});

