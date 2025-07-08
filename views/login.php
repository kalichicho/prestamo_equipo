<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Estilos para el SVG y sus elementos -->
  <link rel="stylesheet" href="public/css/login.css">
  <!-- CSS para modo oscuro -->
  <link rel="stylesheet" href="public/css/tema.css">
  <script src="https://cdn.tailwindcss.com"></script>


</head>

<body class="bg-light">
  <!-- Botón para cambiar modo -->
  <div class="text-end mb-3">
    <button id="toggle-dark-mode" aria-label="Modo oscuro" class="btn btn-outline-secondary btn-dark-mode"><i class="bi bi-moon-fill"></i></button>
  </div>


  <!-- Contenedor centrado vertical y horizontal -->
  <div class="container d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card shadow p-4 position-relative" style="min-width: 400px;">

      <!-- Título -->
      <h3 class="text-center mb-4">Inicio de sesión</h3>

      <!-- Mensaje de éxito (oculto por defecto) -->
      <div id="mensaje-exito" class="alert alert-success text-center"
        style="display: none; position: absolute; top: -40px; left: 50%; transform: translateX(-50%); z-index: 10;">
        ¡Bien hecho!
      </div>

      <!-- Avatar SVG incluido externamente -->
      <div class="text-center">
        <?php include 'public/img/avatar-login.svg'; ?>
      </div>

      <!-- Formulario de inicio de sesión -->
      <form method="POST" action="index.php?c=auth&a=validar">
        <div class="mb-3">
          <label for="email" class="form-label">Correo electrónico</label>
          <input type="email" class="form-control" name="email" id="email" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Contraseña</label>
          <input type="password" class="form-control" name="password" id="password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Entrar</button>
      </form>
    </div>
  </div>

  <!--Importamos el script del avatar ubicado en public/js/login.js-->
  <script src="public/js/login.js"></script>

  <!-- JS para modo oscuro -->
  <script src="public/js/tema.js"></script>

</body>

</html>