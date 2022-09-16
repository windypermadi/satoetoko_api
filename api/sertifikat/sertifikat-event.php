<?php
require_once('../koneksi.php');
require('fpdf/fpdf.php');

$name = $_GET['nama'];
$judul = $_GET['judul'];
$status = $_GET['status'];
$id_promo = $_GET['id_promo'];

$event = mysqli_query($conn, "SELECT * FROM ba_promo  WHERE id_promo = '$id_promo'")->fetch_assoc();
$template_event = "template/".$event['template_sertifikat'];

//$name = text to be added, $x= x cordinate, $y = y coordinate, $a = alignment , $f= Font Name, $t = Bold / Italic, $s = Font Size, $r = Red, $g = Green Font color, $b = Blue Font Color
function AddText($pdf, $text, $x, $y, $a, $f, $t, $s, $r, $g, $b) {
$pdf->SetFont($f,$t,$s);	
$pdf->SetXY($x,$y);
$pdf->SetTextColor($r,$g,$b);
$pdf->Cell(0,10,$text,0,0,$a);	
}

//Create A 4 Landscape page
// $pdf = new FPDF('L','mm','a4');
// $pdf->AddPage();
// $pdf->SetFont('Arial','B', 16);
// // $pdf->SetFontSize(0);
// $pdf->SetCreator('Bahana Digital');
// // Add background image for PDF
// $pdf->Image($template_event,0,0,0);	
// $pdf->SetTitle('eSertifikat_'.$name.'_'.$judul);

// //Add a Name to the certificate
// AddText($pdf,ucwords($name), 10,70, 'C', 'Times','B',50, 0, 51,102);
// $pdf->Output($status, 'eSertifikat_'.$name.'_'.$judul.'.pdf');

$pdf = new FPDF('L','mm','cetak');
$pdf->AddPage();
$pdf->SetFont('Arial','B', 16);
// $pdf->SetFontSize(0);
$pdf->SetCreator('Bahana Digital');
// Add background image for PDF
$pdf->Image($template_event,0,0,0);	
$pdf->SetTitle('eSertifikat_'.$name.'_'.$judul);

//Add a Name to the certificate
AddText($pdf,ucwords($name), 10,225, 'C', 'Times','B',125, 0, 51,102);
$pdf->Output($status, 'eSertifikat_'.$name.'_'.$judul.'.pdf');

// $pdf->Output();
?>