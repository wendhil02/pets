<?php
session_start();
include '../internet/connect_ka.php';
require_once('../vendor/tecnickcom/tcpdf/tcpdf.php'); // Include TCPDF
$zip = new ZipArchive(); // For creating ZIP file

// Check if the report ID is provided
if (isset($_GET['id'])) {
    $report_id = $_GET['id'];

    // Fetch the report details from the database
    $sql = "SELECT * FROM cruelty_reports_archive WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $report_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the report exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Create PDF document
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Pet Welfare Protection System');
        $pdf->SetTitle('Animal Cruelty Report');
        $pdf->SetMargins(15, 10, 15);

        // Add a page to PDF
        $pdf->AddPage();

        // Title & Logo (Replace 'logo.png' with your actual logo path)
        $pdf->Image('logo/logo.png', 15, 10, 20); // Adjust logo size
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'PET WELFARE PROTECTION SYSTEM', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 8, 'OFFICIAL ANIMAL CRUELTY REPORT', 0, 1, 'C');
        $pdf->Ln(10); // Space before content

        // Case ID & Date
        $pdf->SetFillColor(255, 255, 255); // Ensure white background
        $pdf->Cell(40, 8, 'Report Date:', 1, 0, 'L', 0);
        $pdf->Cell(120, 8, date('F d, Y', strtotime($row['archived_at'])), 1, 1, 'L', 0);

        // Reporter & Incident Details
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(40, 8, 'Reporter Email:', 1, 0, 'L', 1);
        $pdf->Cell(120, 8, $row['reporter_email'], 1, 1, 'L');
        $pdf->Cell(40, 8, 'Incident Location:', 1, 0, 'L', 1);
        $pdf->Cell(120, 8, $row['incident_location'], 1, 1, 'L');
        $pdf->Cell(40, 8, 'Incident Date:', 1, 0, 'L', 1);
        $pdf->Cell(120, 8, date('F d, Y h:i A', strtotime($row['incident_datetime'])), 1, 1, 'L');

        // Description
        $pdf->Cell(40, 8, 'Description:', 1, 0, 'L', 1);
        $pdf->MultiCell(120, 8, $row['incident_description'], 1, 'L');

        // Evidence Section (File Attachment)
        if (!empty($row['evidence_path'])) {
            $pdf->Cell(40, 8, 'Evidence:', 1, 0, 'L', 1);
            $pdf->MultiCell(120, 8, 'Attached file: ' . basename($row['evidence_path']), 1, 'L');

            // Add evidence files to the ZIP file
            $evidence_files = explode(',', $row['evidence_path']);
            $zip_filename = 'report_' . $report_id . '_evidence.zip';
            if ($zip->open($zip_filename, ZipArchive::CREATE) === TRUE) {
                foreach ($evidence_files as $file) {
                    $file_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $file;
                    if (file_exists($file_path)) {
                        $zip->addFile($file_path, basename($file_path)); // Add each file to ZIP
                    }
                }
                $zip->close();
            }
        }

        // Additional Sections like Laws, Agreement
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'Relevant Animal Cruelty Laws', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(0, 6, "1. RA 8485 - The Animal Welfare Act: Prohibits abuse, neglect, and maltreatment of animals.\n2. RA 9482 - Anti-Rabies Act: Ensures humane treatment of stray animals.\n3. Section 6 of the Animal Welfare Act: Outlines penalties for violations.", 0, 'L');

        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'Confidentiality & Agreement', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(0, 6, "This document is strictly confidential. The information contained in this report is for legal and investigative purposes only. Unauthorized disclosure or misuse of this report is punishable under applicable laws.", 0, 'L');

        $pdf->Ln(15);

        // Signature Section
        $page_width = $pdf->getPageWidth();
        $line_width = 120;
        $gap = 10;
        $start_x = ($page_width - (2 * $line_width) - $gap) / 2;

        $pdf->SetX($start_x);
        $pdf->Cell($line_width, 8, '_________________________', 0, 0, 'C');
        $pdf->SetX($start_x + $line_width + $gap);
        $pdf->Cell($line_width, 8, '_________________________', 0, 1, 'C');

        $pdf->SetX($start_x);
        $pdf->Cell($line_width, 8, 'Investigating Officer', 0, 0, 'C');
        $pdf->SetX($start_x + $line_width + $gap);
        $pdf->Cell($line_width, 8, 'Reporting Party', 0, 1, 'C');

        // Footer
        $pdf->SetY(-15);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 10, 'Page ' . $pdf->PageNo(), 0, 0, 'C');

        // Output the PDF as a download
        $pdf_output = 'report_' . $report_id . '.pdf';
        $pdf->Output($pdf_output, 'D'); // D for download

        // Offer ZIP download after PDF generation
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zip_filename . '"');
        header('Content-Length: ' . filesize($zip_filename));
        readfile($zip_filename);

        // Optionally, delete the ZIP file after download
        unlink($zip_filename);
    } else {
        echo "Report not found.";
    }

    $stmt->close();
}

$conn->close();
?>

