<?php
// controllers/AlertasController.php

class AlertasController {
    public function index() {
        // 1) Carga la conexión existente (config/database.php define $conn = new mysqli(...))
        require __DIR__ . '/../config/database.php';

        // 2) Recupera las 5 alertas más recientes
        $alertas = [];
        $sql  = "SELECT software, fuente, titulo, enlace, descripcion, fecha
                 FROM notificaciones
                 ORDER BY fecha DESC
                 LIMIT 5";
        if ($res = $conn->query($sql)) {
            while ($row = $res->fetch_assoc()) {
                $alertas[] = $row;
            }
            $res->free();
        }

        // 3) Carga la vista
        require __DIR__ . '/../views/alertas/index.php';
    }
}
