<?php
require_once 'libs/fpdf/fpdf.php';
require_once 'helpers/auth.php';

class PdfController
{
    // 📄 Genera un PDF con los datos de un préstamo o devolución
    public function prestamo()
    {
        // Verifica si el usuario es técnico
        if (!esTecnico()) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }

        // Verifica que se pase un ID por GET
        if (!isset($_GET['id'])) {
            die("ID de préstamo no especificado");
        }

        $id = intval($_GET['id']);
        require 'config/database.php';

        // 🔍 Obtener datos del préstamo y del usuario
        $sql = "
            SELECT p.*, u.nombre AS usuario
            FROM prestamos p
            JOIN usuarios u ON p.usuario_id = u.id
            WHERE p.id = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $prestamo = $stmt->get_result()->fetch_assoc();

        // 🔍 Obtener dispositivos relacionados con el préstamo
        $sql2 = "
            SELECT d.tipo, d.etiqueta_empresa, d.marca, d.modelo
            FROM prestamos_dispositivos pd
            JOIN dispositivos d ON d.id = pd.dispositivo_id
            WHERE pd.prestamo_id = ?
        ";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $dispositivos = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

        // 🧾 Crear el PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Hoja de ' . strtoupper($prestamo['tipo_operacion']), 0, 1, 'C');
        $pdf->Ln(5);

        // 📌 Datos del préstamo
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Empleado: ' . $prestamo['usuario'], 0, 1);
        $pdf->Cell(0, 10, 'Fecha: ' . $prestamo['fecha_prestamo'], 0, 1);
        $pdf->Cell(0, 10, 'Unidad: ' . $prestamo['unidad'], 0, 1);
        $pdf->Cell(0, 10, 'Ubicacion: ' . $prestamo['ubicacion'], 0, 1);
        $pdf->Ln(10);

        // 📋 Lista de dispositivos asociados
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Dispositivos:', 0, 1);
        $pdf->SetFont('Arial', '', 11);
        foreach ($dispositivos as $d) {
            $linea = "- {$d['tipo']} | {$d['etiqueta_empresa']} | {$d['marca']} {$d['modelo']}";
            $pdf->Cell(0, 8, $linea, 0, 1);
        }

        // ✍️ Firma
        $pdf->Ln(20);
        $pdf->Cell(0, 10, 'Firma del empleado: ___________________________', 0, 1);

        // 📤 Mostrar el PDF directamente en el navegador
        $pdf->Output('I', 'prestamo_' . $id . '.pdf');
    }

    //ver la hoja de prestamo o devolucion en un pdf sin eviarlo por correo
    public function ver()
    {
        if (!isset($_GET['id'])) {
            die('ID de préstamo no especificado');
        }

        require 'config/database.php';
        require_once 'helpers/pdf.php';

        $prestamo_id = intval($_GET['id']);

        // Obtener los datos del préstamo
        $sql = "
        SELECT p.*, u.nombre
        FROM prestamos p
        JOIN usuarios u ON u.id = p.usuario_id
        WHERE p.id = ?
    ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $prestamo_id);
        $stmt->execute();
        $prestamo = $stmt->get_result()->fetch_assoc();

        // Obtener los dispositivos del préstamo
        $stmt = $conn->prepare("
        SELECT d.*
        FROM dispositivos d
        JOIN prestamos_dispositivos pd ON pd.dispositivo_id = d.id
        WHERE pd.prestamo_id = ?
    ");
        $stmt->bind_param("i", $prestamo_id);
        $stmt->execute();
        $prestamo['dispositivos'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Generar el PDF (ya actualizado con nuevo estilo)
        $archivo = generarPDFPrestamo($prestamo);

        // Enviar PDF al navegador directamente
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="hoja_prestamo_' . $prestamo_id . '.pdf"');
        readfile($archivo);
        unlink($archivo); // borrar temporal
        exit;
    }
}
