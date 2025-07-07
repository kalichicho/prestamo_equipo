<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'libs/phpMailer/PHPMailer.php';
require_once 'libs/phpMailer/SMTP.php';
require_once 'libs/phpMailer/Exception.php';

// 📬 Envía el PDF del préstamo al correo del usuario
function enviarCorreoConPDF($correoDestino, $rutaPDF, $nombre = "Empleado", $asunto = "Resumen de préstamo")
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = '0abeff377fd4e1';
        $mail->Password = '60c07b4c8058bd';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 2525;

        $mail->setFrom('tucorreo@empresa.com', 'Soporte Técnico');
        $mail->addAddress($correoDestino, $nombre);
        $mail->Subject = $asunto;
        $mail->Body = "Adjunto encontrarás el resumen del préstamo o devolución registrado.";

        $mail->addAttachment($rutaPDF, 'resumen_prestamo.pdf');
        $mail->send();

        return true;
    } catch (Exception $e) {
        error_log("❌ Error al enviar correo: {$mail->ErrorInfo}");
        return false;
    }
}
