<?php
require_once 'libs/fpdf/fpdf.php';
require_once 'libs/phpMailer/PHPMailer.php';
require_once 'libs/phpMailer/SMTP.php';
require_once 'libs/phpMailer/Exception.php';
require_once 'helpers/auth.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CorreoController
{
    // ✉️ Envía por correo el resumen de un préstamo o devolución en formato PDF
    public function enviar()
    {
        // Solo técnicos pueden enviar correos
        if (!esTecnico()) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }

        // Verifica que se haya pasado un ID por GET
        $id = $_GET['id'] ?? null;

        // Verifica que se pase el ID del préstamo por POST
        if (!isset($_POST['id'])) {
            die("ID de préstamo no especificado");
        }

        $id = intval($_POST['id']);


        // Importamos el modelo y helpers necesarios
        require_once 'models/Prestamo.php';
        require_once 'helpers/pdf.php';
        require_once 'helpers/email.php';

        // Obtener los datos completos del préstamo
        $prestamo = Prestamo::obtenerPorId($id);
        if (!$prestamo) {
            die("Préstamo no encontrado");
        }

        // Generar el PDF temporalmente
        $pdfPath = generarPDFPrestamo($prestamo);

        // Enviar correo con el PDF adjunto
        $exito = enviarCorreoConPDF($prestamo['email'], $pdfPath, $prestamo['nombre'], "Resumen de {$prestamo['tipo_operacion']}");

        // Borrar archivo temporal generado
        unlink($pdfPath);

        // Guardar el resultado en sesión para mostrar mensaje
        if ($exito) {
            $_SESSION['success'] = "✅ Correo enviado correctamente a {$prestamo['email']}";
        } else {
            $_SESSION['error'] = "❌ No se pudo enviar el correo.";
        }

        // Volver al historial
        header("Location: index.php?c=prestamo&a=historial");
    }
}
