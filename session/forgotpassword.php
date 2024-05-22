<?php
session_start();


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require '../session/db.php';
require '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    // Check if the email exists in your database
    $stmt = $conn->prepare("SELECT * FROM users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        // Save the token in your database or any other storage mechanism
        $resetToken = bin2hex(random_bytes(32)); // Adjust the length as needed
    
        // Save the token in your database
        $stmt = $conn->prepare("UPDATE users SET ResetToken = ? WHERE Email = ?");
        $stmt->bind_param("ss", $resetToken, $email);
        $stmt->execute();
        $stmt->close();
    
        // Set the email in the session variable
        $_SESSION['resetEmail'] = $email;
    
        // Construct the reset password link
        $resetLink = 'https://mediflow.website/session/verify.php?token=' . $resetToken;

        // Email configuration
        $subject = 'Password Reset';
        $body = 'Click the following link to reset your password: <a href="' . $resetLink . '">Reset Password</a>';

        // Sending email
        require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
        require '../vendor/phpmailer/phpmailer/src/SMTP.php';
        require '../vendor/phpmailer/phpmailer/src/Exception.php';

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Your SMTP server
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->Username = SMTP_USERNAME; // Use the constant
            $mail->Password = SMTP_PASSWORD; // Use the constant

            // Recipients
            $mail->setFrom(SMTP_USERNAME, 'MediflowHub | Reset Password');
            $mail->addAddress($email, 'Recipient Name');

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();

            // Set success status and redirect
            $_SESSION['successMessage'] = 'Reset password link sent successfully.';
            header("Location: forgotpassword.php");
            exit();
        } catch (Exception $e) {
            // Set error status and redirect
            $_SESSION['errorMessage'] = 'Error sending the password reset link.';
            header("Location: forgotpassword.php");
            exit();
        }
    } else {
        // Set error status and redirect
        $_SESSION['errorMessage'] = 'Email not found in the database.';
        header("Location: forgotpassword.php");
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | MediFlowHub </title>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <link rel="icon" href="../users/images/logo.png" type="image/png">


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- FOR ICONS-->
     <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">

   



    
     <link rel="stylesheet" href="../users/style/forgot.css">

    <script src="otp.js" defer></script>

  






</head>
<body>

<div class="container">
       
          

<h4>Password Recovery </h4>
<p>Please enter your Email to recover your password</p>



<div id="successMessage" class="success-message"><i id="bx-check" class='bx bx-check'></i></div>

<div id="errorMessage" class="errorMessage"><i id="bx-error" class='bx bx-x-circle'></i></div>




<form action="" method="post" id="forgotForm">

        <div class="input-box">

        <input type="email" class="emailinput" name="email" placeholder="Registered Email">

        </div>
    
    <button type="submit" name="verify" class="Verify-btn">Send Password Reset Link</button>
    <input type="hidden" name="resettoken" value="<?php echo bin2hex(random_bytes(16)); ?>">

    <p class="Signup-btn"> Already have an account? <a href="../users/login.php"><span>Login</span></a></p>
</form>








  </div>




</body>


<script>



 // Check if the success parameter is present in the URL
 var successMessage = "<?php echo isset($_SESSION['successMessage']) ? $_SESSION['successMessage'] : ''; ?>";
if (successMessage !== "") {
    var successMessageDiv = document.getElementById("successMessage");
   
    successMessageDiv.textContent = successMessage;
    successMessageDiv.style.display = "block";
   
    // Scroll to the success message for better visibility
    successMessageDiv.scrollIntoView({ behavior: 'smooth' });

    // Remove the session variable to avoid displaying the message on subsequent page loads
    <?php unset($_SESSION['successMessage']); ?>
}




var errorMessage = "<?php echo isset($_SESSION['errorMessage']) ? $_SESSION['errorMessage'] : ''; ?>";
if (errorMessage !== "") {
    var errorMessageDiv = document.getElementById("errorMessage");
    errorMessageDiv.textContent = errorMessage;
    errorMessageDiv.style.display = "block";

    // Scroll to the success message for better visibility
    errorMessageDiv.scrollIntoView({ behavior: 'smooth' });

    // Remove the session variable to avoid displaying the message on subsequent page loads
    <?php unset($_SESSION['errorMessage']); ?>
}



</script>






</html>









