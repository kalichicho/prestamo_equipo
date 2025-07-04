<?php
require_once 'libs/fpdf/fpdf.php';

// 📄 Genera y devuelve la ruta a un PDF temporal con los datos del préstamo
function generarPDFPrestamo($prestamo)
{
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, utf8_decode('Registro de ' . strtoupper($prestamo['tipo_operacion'])), 0, 1, 'C');
    $pdf->Ln(8);

    // Datos generales
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 8, utf8_decode('Empleado: ' . $prestamo['nombre']), 0, 1);
    $pdf->Cell(0, 8, utf8_decode('Fecha: ' . $prestamo['fecha_prestamo']), 0, 1);
    $pdf->Cell(0, 8, utf8_decode('Unidad: ' . $prestamo['unidad']), 0, 1);
    $pdf->Cell(0, 8, utf8_decode('Ubicación: ' . $prestamo['ubicacion']), 0, 1);
    $pdf->Ln(5);

    // Tabla de dispositivos
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, utf8_decode('Dispositivos:'), 0, 1);

    $anchoTipo = 35;
    $anchoEtiqueta = 35;
    $anchoMarca = 45;
    $anchoModelo = 75;

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell($anchoTipo, 8, 'Tipo', 1);
    $pdf->Cell($anchoEtiqueta, 8, 'Etiqueta', 1);
    $pdf->Cell($anchoMarca, 8, 'Marca', 1);
    $pdf->Cell($anchoModelo, 8, 'Modelo', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 11);
    foreach ($prestamo['dispositivos'] as $d) {
        $pdf->Cell($anchoTipo, 8, utf8_decode($d['tipo']), 1);
        $pdf->Cell($anchoEtiqueta, 8, $d['etiqueta_empresa'], 1);
        $pdf->Cell($anchoMarca, 8, utf8_decode($d['marca']), 1);
        $y = $pdf->GetY();
        $x = $pdf->GetX();
        $pdf->MultiCell($anchoModelo, 8, utf8_decode($d['modelo']), 1);
        $pdf->SetXY($x + $anchoModelo + $anchoMarca + $anchoEtiqueta + $anchoTipo, $y); // Salto automático corregido
    }

    $pdf->Ln(15);

    // Firma del técnico
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 8, utf8_decode('Firma por la Unidad de Sistemas de la Información:'), 0, 1);
    $firmaTecnico = 'public/firmas/firma_tecnico_' . $prestamo['id'] . '.png';
    if (file_exists($firmaTecnico)) {
        $pdf->Image($firmaTecnico, $pdf->GetX(), $pdf->GetY(), 50, 20);
        $pdf->Ln(22);
    } else {
        $pdf->Ln(15);
        $pdf->Cell(0, 8, '______________________________', 0, 1);
    }

    // Firma del empleado
    $pdf->Ln(10);
    $pdf->Cell(0, 8, utf8_decode('Firma del empleado:'), 0, 1);
    $firmaEmpleado = 'public/firmas/firma_empleado_' . $prestamo['id'] . '.png';
    if (file_exists($firmaEmpleado)) {
        $pdf->Image($firmaEmpleado, $pdf->GetX(), $pdf->GetY(), 50, 20);
        $pdf->Ln(22);
    } else {
        $pdf->Ln(15);
        $pdf->Cell(0, 8, '______________________________', 0, 1);
    }

    // Nota
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'I', 9);
    $nota = "NOTA: El material informático indicado arriba está inventariado y asignado EXCLUSIVAMENTE al usuario. En caso de modificación o baja, debe comunicarse con el área de Sistemas. Conserve esta hoja como constancia. En el campo modelo puede haber información NO clara";
    $pdf->MultiCell(0, 6, utf8_decode($nota), 0, 'L');

    // Guardar temporal
    $archivo = tempnam(sys_get_temp_dir(), 'prestamo_');
    $pdf->Output('F', $archivo);
    return $archivo;
}
