<?php
// cancel-reason.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include('../session/auth.php');
require_once '../session/session_manager.php';
require '../session/db.php';
require '../config/config.php';
require '../vendor/autoload.php';

start_secure_session();

if (!isset($_SESSION["username"])) {
    header("Location: ../users/login.php");
    exit;
}

if (!check_admin_role()) {
    header('Location: ../users/dashboard.php');
    exit();
}


function updateAppointmentStatus($appointmentId, $status)
{
    global $conn;

    // Update the appointment status to 'Cancelled' in the database
    $updateQuery = "UPDATE appointments SET Status = '$status' WHERE Appointment_ID = $appointmentId";
    $conn->query($updateQuery);
}
// Initialize variables
$appointmentId = isset($_GET['appointment_id']) ? $_GET['appointment_id'] : null;
$canceldetails = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the cancellation reason from the form
    $canceldetails = isset($_POST['canceldetails']) ? $_POST['canceldetails'] : '';

    // Validate and sanitize the input if necessary

    // Send emails to the patient and the doctor
    sendCancellationEmails($appointmentId, $canceldetails);

    // Update the appointment status to 'Cancelled' in the database
    updateAppointmentStatus($appointmentId, 'Cancelled');

    // Redirect to a success page or perform other actions as needed
    header("Location: admin-appointment.php");
    exit();
}

// Retrieve appointment details if necessary

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel - Appointment No. <?php echo $appointmentId; ?></title>


    <link rel="icon" href="images/logo.png" type="image/png">

    <link rel="stylesheet" type="text/css" href="../admin/style/cancel-reason.css">
    <link rel="stylesheet" href="style/transitions.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">



 
    
</head>
<body>

    <div class="main--content">

    <div class="header">

        <div class="Logo">


        <a href="admin-appointment.php"><img src="images/logo.png" alt="" width="50px" /></a>
                
                

        </div>


        <p>Admin <span>/ Cancellation</span></p>




        </div>

        <div class="appointment-details">
            <div class="box">
                <p>Appointment ID: <span><?php echo $appointmentId; ?></span> </p>
            </div>
        </div>

        <form action="" method="post">
            <div class="text-prescribe">
                <p>Reason to cancel: </p>
                <textarea name="canceldetails" cols="200" rows="10"></textarea>

            </div>

            <div class="button-prescribe">
                <button class="submit" type="submit">Submit</button>
            </div>
        </form>

    </div>

</body>

</html>

<?php
function sendCancellationEmails($appointmentId, $canceldetails)
{
    global $conn;

    // Retrieve user and doctor emails
    $emailQuery = "SELECT p.Email AS Patient_Email, d.Email AS Doctor_Email
                   FROM appointments a
                   JOIN users p ON a.user_id = p.user_id
                   JOIN doctors_table d ON a.doctor_id = d.doctor_id
                   WHERE a.Appointment_ID = $appointmentId";

    $emailResult = $conn->query($emailQuery);

    if ($emailResult && $emailResult->num_rows > 0) {
        $emailData = $emailResult->fetch_assoc();
        $patientEmail = $emailData['Patient_Email'];
        $doctorEmail = $emailData['Doctor_Email'];

        // Send cancellation emails
        sendCancellationEmail($patientEmail, 'Patient', $canceldetails);
        sendCancellationEmail($doctorEmail, 'Doctor', $canceldetails);
    }
}

function sendCancellationEmail($recipientEmail, $recipientType, $canceldetails)
{
    // Instantiate PHPMailer
    $mail = new PHPMailer(true);

    // Mailer configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;

    // Set email sender and recipient
    $mail->setFrom(SMTP_USERNAME, 'MediflowHub | Appointment Cancellation');
    $mail->addAddress($recipientEmail);

    // Set email content
    $mail->isHTML(true);
    $mail->Subject = 'Appointment Cancellation';

    // Customize the email content based on the recipient type and include the cancellation reason
    if ($recipientType === 'Patient') {
        $mail->Body = "Your appointment has been cancelled. Reason: $canceldetails";
    } elseif ($recipientType === 'Doctor') {
        $mail->Body = "The appointment has been cancelled. Reason: $canceldetails";
    }
    // Send the email
    try {
        $mail->send();
    } catch (Exception $e) {
        // Handle the exception as needed (log the error, display a message, etc.)
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }


}
?>
