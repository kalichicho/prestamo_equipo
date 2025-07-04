window.addEventListener('load', function () {
    const canvas = document.getElementById('canvasFirma');
    const ctx = canvas.getContext('2d');
    let dibujando = false;

    function getMousePos(e) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        return {
            x: (e.clientX - rect.left) * scaleX,
            y: (e.clientY - rect.top) * scaleY
        };
    }


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

    canvas.addEventListener('mouseup', function () {
        dibujando = false;
        ctx.beginPath(); // Evita la línea recta en el siguiente trazo
    });

    canvas.addEventListener('mouseout', function () {
        dibujando = false;
        ctx.beginPath();
    });

    document.getElementById('limpiarCanvas').addEventListener('click', function () {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.beginPath();
    });

    document.getElementById('guardarCanvas').addEventListener('click', function () {
        const dataURL = canvas.toDataURL('image/png');
        document.getElementById('firmaCanvasBase64').value = dataURL;
        document.getElementById('formFirma').submit();
    });
});
