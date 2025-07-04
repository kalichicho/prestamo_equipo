<?php
require '../libs/fpdf/fpdf.php';
require '../config/database.php';

$usuario_id = $_GET['usuario_id'] ?? null;
if (!$usuario_id) die("Usuario no especificado");

// Obtener nombre
$stmt = $conn->prepare("SELECT nombre FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$nombre_usuario = $res['nombre'] ?? 'Desconocido';

// Obtener historial
$sql = "
    SELECT p.id, p.tipo_operacion, p.fecha_prestamo, p.unidad, p.ubicacion,
           GROUP_CONCAT(CONCAT(d.tipo, ': ', d.etiqueta_empresa) SEPARATOR ', ') AS dispositivos
    FROM prestamos p
    JOIN prestamos_dispositivos pd ON pd.prestamo_id = p.id
    JOIN dispositivos d ON d.id = pd.dispositivo_id
    WHERE p.usuario_id = ?
    GROUP BY p.id ORDER BY p.fecha_prestamo DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$movimientos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, "Historial de $nombre_usuario", 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(15, 10, "ID", 1);
$pdf->Cell(25, 10, "Tipo", 1);
$pdf->Cell(25, 10, "Fecha", 1);
$pdf->Cell(60, 10, "Dispositivos", 1);
$pdf->Cell(30, 10, "Unidad", 1);
$pdf->Cell(30, 10, "Ubicación", 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 9);
foreach ($movimientos as $m) {
    $pdf->Cell(15, 8, $m['id'], 1);
    $pdf->Cell(25, 8, ucfirst($m['tipo_operacion']), 1);
    $pdf->Cell(25, 8, $m['fecha_prestamo'], 1);
    $pdf->Cell(60, 8, $m['dispositivos'], 1);
    $pdf->Cell(30, 8, $m['unidad'], 1);
    $pdf->Cell(30, 8, $m['ubicacion'], 1);
    $pdf->Ln();
}

$pdf->Output("I", "historial_$nombre_usuario.pdf");
