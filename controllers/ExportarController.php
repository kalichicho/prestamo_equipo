<?php

require_once 'models/Dispositivo.php';

class ExportarController
{
    // 📄 Exporta estadísticas a PDF
    public function pdf()
{
    require_once 'libs/fpdf/fpdf.php';

    $tipo = $_GET['tipo'] ?? '';
    $fecha_inicio = $_GET['fecha_inicio'] ?? '';
    $fecha_fin = $_GET['fecha_fin'] ?? '';

    $stats = Dispositivo::obtenerEstadisticasFiltradas($tipo, $fecha_inicio, $fecha_fin);
    $stats['sin_asignar'] = Dispositivo::contarSinAsignar($tipo);

    $pdf = new FPDF();
    $pdf->AddPage();

    // Títulos
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, utf8_decode('Reporte de Estadísticas de Dispositivos'), 0, 1, 'C');
    $pdf->Ln(8);

    $pdf->SetFont('Arial', '', 12);

    if ($tipo) {
        $pdf->Cell(0, 8, utf8_decode('Tipo filtrado: ') . utf8_decode(ucfirst($tipo)), 0, 1);
    }
    if ($fecha_inicio && $fecha_fin) {
        $pdf->Cell(0, 8, utf8_decode('Rango de fechas: ') . $fecha_inicio . ' a ' . $fecha_fin, 0, 1);
    }
    $pdf->Ln(5);

    // Tabla
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(80, 10, utf8_decode('Indicador'), 1, 0, 'C', true);
    $pdf->Cell(50, 10, utf8_decode('Valor'), 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 11);

    $pdf->Cell(80, 8, utf8_decode('Total dispositivos'), 1);
    $pdf->Cell(50, 8, $stats['total'] ?? 0, 1, 1);

    $pdf->Cell(80, 8, utf8_decode('Activos'), 1);
    $pdf->Cell(50, 8, $stats['activos'] ?? 0, 1, 1);

    $pdf->Cell(80, 8, utf8_decode('Dados de baja'), 1);
    $pdf->Cell(50, 8, $stats['bajas'] ?? 0, 1, 1);

    $pdf->Cell(80, 8, utf8_decode('Actualmente prestados'), 1);
    $pdf->Cell(50, 8, $stats['prestados'] ?? 0, 1, 1);

    $pdf->Cell(80, 8, utf8_decode('Sin asignar'), 1);
    $pdf->Cell(50, 8, $stats['sin_asignar'] ?? 0, 1, 1);

    $pdf->Ln(10);

    // Nota final
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->MultiCell(0, 6, utf8_decode('Reporte generado automáticamente desde el sistema de control de dispositivos.'), 0, 'C');

    $pdf->Output('I', 'estadisticas_filtradas.pdf');
}




    // 📊 Exporta estadísticas a Excel
    public function excel()
    {
        $tipo = $_GET['tipo'] ?? '';
        $fecha_inicio = $_GET['fecha_inicio'] ?? '';
        $fecha_fin = $_GET['fecha_fin'] ?? '';

        // Obtener estadísticas filtradas
        $stats = Dispositivo::obtenerEstadisticasFiltradas($tipo, $fecha_inicio, $fecha_fin);
        $stats['sin_asignar'] = Dispositivo::contarSinAsignar($tipo);

        // Cabeceras para descarga
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="estadisticas_filtradas.xls"');

        // Contenido Excel
        echo "<table border='1' cellpadding='5'>";
        echo "<tr style='background-color: #f2f2f2; font-weight: bold;'>
            <th>Indicador</th><th>Valor</th>
          </tr>";
        echo "<tr><td>Total dispositivos</td><td>" . ($stats['total'] ?? 0) . "</td></tr>";
        echo "<tr><td>Activos</td><td>" . ($stats['activos'] ?? 0) . "</td></tr>";
        echo "<tr><td>Dados de baja</td><td>" . ($stats['bajas'] ?? 0) . "</td></tr>";
        echo "<tr><td>Actualmente prestados</td><td>" . ($stats['prestados'] ?? 0) . "</td></tr>";
        echo "<tr><td>Sin asignar</td><td>" . ($stats['sin_asignar'] ?? 0) . "</td></tr>";
        echo "</table>";
    }
}
