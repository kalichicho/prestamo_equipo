<?php

// 🔐 Comprueba si hay un usuario autenticado mediante sesión
function usuarioAutenticado()
{
    return isset($_SESSION['usuario_id']);
}

// 🔐 Comprueba si el usuario actual tiene rol de técnico o administrador
function esTecnico()
{
    return usuarioAutenticado() && in_array($_SESSION['rol'], ['tecnico', 'administrador']);
}

// 🔐 Comprueba si el usuario actual tiene rol de administrador
function esAdministrador()
{
    return usuarioAutenticado() && $_SESSION['rol'] === 'administrador';
}

// 🔐 Comprueba si el usuario actual tiene rol de empleado
function esEmpleado()
{
    return usuarioAutenticado() && $_SESSION['rol'] === 'empleado';
}
