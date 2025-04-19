<?php
require('libs/fpdf.php');
include '../internet/connect_ka.php';
// Connect to the database


// Fetch pets data from the database
$sql = "SELECT petname, type, email, schedule_date FROM pets";
$result = $conn->query($sql);

$pets = [];
while ($row = $result->fetch_assoc()) {
    $pets[] = $row;
}

$conn->close();

// Create a new PDF instance
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Title (Letter Header)
$pdf->Cell(0, 10, "Pet Schedule Confirmation", 0, 1, 'C');
$pdf->Ln(10);

// Start the body of the letter
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, "Dear Pet Owner,", 0, 1);

// Add schedule information
$pdf->Ln(5);
$pdf->Cell(0, 10, "Please find below the schedule information for your pets:", 0, 1);

$pdf->Ln(10);

// Add a table with pet details
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 10, "Pet Name", 1);
$pdf->Cell(30, 10, "Type", 1);
$pdf->Cell(40, 10, "Email", 1);
$pdf->Cell(40, 10, "Schedule Date", 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);

// Loop through pets data and add rows to the table
foreach ($pets as $pet) {
    $pdf->Cell(30, 10, $pet['petname'], 1);
    $pdf->Cell(30, 10, $pet['type'], 1);
    $pdf->Cell(40, 10, $pet['email'], 1);
    $pdf->Cell(40, 10, $pet['schedule_date'] != '' ? $pet['schedule_date'] : 'Not Set', 1);
    $pdf->Ln();
}

$pdf->Ln(10);

// Ending the letter
$pdf->Cell(0, 10, "Thank you for your cooperation.", 0, 1);

$pdf->Ln(5);

// Footer
$pdf->Cell(0, 10, "Best regards,", 0, 1);
$pdf->Cell(0, 10, "Your Pet Welfare Team", 0, 1);

// Output the PDF
$pdf->Output('D', 'pet_schedule_letter.pdf');
?>
