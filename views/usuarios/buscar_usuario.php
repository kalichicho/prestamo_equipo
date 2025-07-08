<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Buscar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS para modo oscuro -->
    <link rel="stylesheet" href="public/css/tema.css">
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Buscar usuario</h2>
        <!-- Botón para cambiar modo -->
        <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
            <a href="index.php?c=prestamo&a=dashboard" class="btn btn-outline-secondary">
                ← Volver
            </a>
            <button id="toggle-dark-mode" aria-label="Modo oscuro" class="btn btn-outline-secondary btn-dark-mode">
                <i class="bi bi-moon-fill"></i>
            </button>
        </div>


        <div class="mb-3">
            <input type="text" id="buscar_usuario" class="form-control" placeholder="Buscar por nombre o email">
            <input type="hidden" id="usuario_id">
            <div id="resultados_usuario" class="list-group mt-2"></div>
        </div>

        
    </div>
    <!--Sirve para que el técnico pueda buscar usuarios por nombre o email en tiempo real, 
y al hacer clic sobre uno de ellos, se redirige automáticamente a la vista que muestra sus dispositivos asignados.-->
    <script src="public/js/buscar_usuario.js"></script>

    <!-- JS para modo oscuro -->
    <script src="public/js/tema.js"></script>

</body>

</html>