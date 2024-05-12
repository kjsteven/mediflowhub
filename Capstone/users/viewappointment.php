<?php

require '../session/db.php';
require_once '../session/session_manager.php';
require_once '../TCPDF-main/tcpdf.php'; // Include the TCPDF library



if (!isset($_GET['appointment_id'])) {
    // Handle error - appointment_id not provided
    die("Appointment ID not provided.");
}

$appointmentId = $_GET['appointment_id'];

$query = "SELECT a.Appointment_ID, 
                 p.Last_Name AS Patient_Last_Name, p.First_Name AS Patient_First_Name, 
                 d.Last_Name AS Doctor_Last_Name, d.First_Name AS Doctor_First_Name, d.Fee,
                 a.Status, a.time_slot, a.Date, a.Prescription
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







// Create a PDF document
$pdf = new TCPDF();


// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        $image_file = 'images/ilovetaguig.jpg';
        $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
        $this->Cell(0, 15, 'Brgy. Wawa Health Center', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('MediflowHub');
$pdf->SetTitle('Appointment Details');
$pdf->SetSubject('Details');


// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}



$pdf->AddPage();

// Set font for the rest of the content
$pdf->SetFont('helvetica', '', 12);



// Output appointment details in the PDF
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Appointment Details', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Appointment ID: ' . $appointmentDetails['Appointment_ID'], 0, 1);
$pdf->Cell(0, 10, 'Patient: ' . $appointmentDetails['Patient_Last_Name'] . ', ' . $appointmentDetails['Patient_First_Name'], 0, 1);
$pdf->Cell(0, 10, 'Doctor: ' . $appointmentDetails['Doctor_Last_Name'] . ', ' . $appointmentDetails['Doctor_First_Name'], 0, 1);
$pdf->Cell(0, 10, 'Status: ' . $appointmentDetails['Status'], 0, 1);
$pdf->Cell(0, 10, 'Fee: ' . $appointmentDetails['Fee'], 0, 1);

$pdf->Cell(0, 10, 'Date and Time: ' . date('F j, Y', strtotime($appointmentDetails['Date'])) . ', ' . $appointmentDetails['time_slot'], 0, 1);
$pdf->Ln(10);
$pdf->MultiCell(0, 10, 'Prescription: ' . $appointmentDetails['Prescription'], 0, 'L');

// Output the PDF
$fileName = 'appointment_' . $appointmentDetails['Appointment_ID'] . '_' . date('F j, Y') . '.pdf';
$pdf->Output($fileName, 'I');




?>