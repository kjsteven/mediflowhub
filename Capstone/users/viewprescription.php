<?php

require '../session/db.php';
require_once '../session/session_manager.php';
require_once '../TCPDF-main/tcpdf.php';

// Retrieve the Appointment_ID from URL parameters
if (!isset($_GET['appointment_id'])) {
    // Handle error - appointment_id not provided
    die("Appointment ID not provided.");
}

$appointmentId = $_GET['appointment_id'];

$query = "SELECT a.Appointment_ID, 
                 p.Last_Name AS Patient_Last_Name, p.First_Name AS Patient_First_Name, p.Date_of_Birth,
                 p.Gender, p.Phone,
                 d.Last_Name AS Doctor_Last_Name, d.First_Name AS Doctor_First_Name, d.Specialty, d.Fee,
                 a.Status, a.time_slot, a.Date, a.Prescription, a.Doctor_Signature
          FROM appointments a
          JOIN patients_table p ON a.Patient_id = p.Patient_id
          JOIN doctors_table d ON a.doctor_id = d.doctor_id
          WHERE a.Appointment_ID = $appointmentId";
$result = $conn->query($query);

if ($result === false || $result->num_rows === 0) {
    // Handle error - appointment not found
    die("Appointment not found.");
}

$appointmentDetails = $result->fetch_assoc();

// Create a new PDF document
$pdf = new TCPDF();
$pdf->SetAutoPageBreak(true, 10);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('times', '', 12);

// Logo position in the top left
$pdf->Image('../users/images/Mediflowhub.jpg', 10, 20, 30);

// Set font for doctor details
$pdf->SetFont('times', 'B', 16);

// Move to the right of the logo for doctor details
$pdf->SetXY(50, 15);
$pdf->Cell(0, 10, 'Dr. ' . $appointmentDetails['Doctor_First_Name'] . ' ' . $appointmentDetails['Doctor_Last_Name'], 0, 1, 'C');
$pdf->Cell(0, 10, $appointmentDetails['Specialty'], 0, 1, 'C');
// Add a line after the doctor details
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());


// Patient Information using MultiCell for flex-like layout
$pdf->SetFont('times', '', 12);
$pdf->Ln(5);
$pdf->MultiCell(0, 10, 'Patient Name: ' . $appointmentDetails['Patient_First_Name'] . ' ' . $appointmentDetails['Patient_Last_Name'], 0, 'L');
$pdf->MultiCell(0, 10, 'Date of Birth: ' . $appointmentDetails['Date_of_Birth'], 0, 'L');
$pdf->MultiCell(0, 10, 'Gender: ' . $appointmentDetails['Gender'], 0, 'L');
$pdf->MultiCell(0, 10, 'Contact Number: ' . $appointmentDetails['Phone'], 0, 'L');

// Prescription content
// Prescription header
$pdf->SetFont('times', 'B', 18);
$pdf->Ln(10);
$pdf->Cell(0, 10, 'PRESCRIPTION', 0, 1, 'C');
$pdf->SetFont('times', '', 12);
$pdf->Ln(5);
$pdf->MultiCell(0, 10, $appointmentDetails['Prescription'], 0, 'L');


// Add a line before the signature
$pdf->Ln(10);
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());

// Retrieve the doctor's signature path from the database
$doctorSignaturePath = $appointmentDetails['Doctor_Signature']; // Change this to the actual field name in your database

// Add the doctor's signature image in the bottom right
if (!empty($doctorSignaturePath) && file_exists($doctorSignaturePath)) {
    $pdf->Image($doctorSignaturePath, 160, $pdf->GetY() + 5, 30);

    // Add a word below the signature
    $pdf->SetXY(160, $pdf->GetY() + 35); // Adjust the Y-coordinate based on your layout
    $pdf->SetFont('times', '', 12);
    $pdf->Cell(30, 5, 'Doctor\'s Signature', 0, 1, 'C');
} else {
    // If no signature path is found or the file doesn't exist, you can display a placeholder or handle it as needed.
    $pdf->Cell(0, 10, 'Doctor\'s Signature', 0, 1, 'R');
}


// Additional details if needed

// Save the PDF file (you can also use 'D' to force download)
$pdf->Output('prescription_' . $appointmentId . '.pdf', 'I');

?>
