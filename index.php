<?php
session_start();

require_once 'config/database.php';
require_once 'helpers/auth.php';

// Autocargar modelos y controladores
spl_autoload_register(function ($class) {
    if (file_exists("controllers/$class.php")) {
        require_once "controllers/$class.php";
    } elseif (file_exists("models/$class.php")) {
        require_once "models/$class.php";
    }
});

// Definir ruta actual
$controller = $_GET['c'] ?? 'auth';
$action = $_GET['a'] ?? 'login';

// Construir nombre del controlador y método
$controllerName = ucfirst($controller) . 'Controller';
$method = $action;

// Verificar existencia
if (class_exists($controllerName) && method_exists($controllerName, $method)) {
    $ctrl = new $controllerName();
    $ctrl->$method();
} else {
    echo "Error 404: controlador o acción no encontrada.";
}
