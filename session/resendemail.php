<?php 


session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require 'db.php';
require '../config/config.php';

if (isset($_POST["submit"])) {
    $username = $_POST["email"]; // Assuming you have an input field named "email" in your form

    // Check if the user exists
    $query = "SELECT * FROM users WHERE Email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $userData = mysqli_fetch_assoc($result);
        $token = $userData['Token'];
        $first_name = $userData['First Name'];

        // Resend verification email
        sendVerificationEmail($username, $token, $first_name);
        $_SESSION['successMessage'] = "Verification email resent successfully.";
        header("Location: resendemail.php");
        exit();
    } else {
        $_SESSION['errorMessage'] = "User not found. Please make sure you entered the correct email address.";
        header("Location: resendemail.php");
        exit();
    }
}

function sendVerificationEmail($username, $token, $first_name)
{
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Your SMTP server
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->Username = SMTP_USERNAME; // Use the constant
    $mail->Password = SMTP_PASSWORD; // Use the constant
    $mail->Subject = 'Email Verification';

    $verificationLink = 'http://mediflow.website/session/verify.php?token=' . $token;
    $mail->Body = 'Hi' . " " . $first_name . ',' . "\n" . "\n" . 
        'We just need to verify your email address before you can access our website.' . "\n" .  "\n" . 
        'To verify your email, please click this link: (' . $verificationLink . ').' . "\n" .  "\n" . 
        'Thanks! - The MediflowHub Team';

    $mail->setFrom(SMTP_USERNAME, 'MediflowHub | Email Account Verification');
    $mail->addAddress($username);

    // Send the email
    $mail->send();
}




?> 



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Email Verified</title>


  
<link rel="icon" href="../users/images/logo.png" type="image/png">




<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">

</head>
<body>


<div class="container">


  <div class="check">

  <i class='bx bx-envelope' ></i>
    
  </div>

  <p>Account not yet activated or Didnt receive an email? </p>

  <span>It may take a few minutes for the email to reach your inbox. <br> Still nothing? Re-enter your email and try again.</span>

 

  <form method="post" action="resendemail.php">

        <div class="inputbox">

            <label for="email">Email Address</label>

            <input type="email" name="email" placeholder="sample@gmail.com"></input>



        </div>



        <div class="buttons">

        <button type="submit" class="resend-email" name="submit">
            Resend Email
        </button>

        <a href="../users/login.php">Return to Site</a>



        </div>

        


  </form>


  <?php
        // Display success or error messages
        if (isset($_SESSION['successMessage'])) {
            echo "<p style='color: green;'>" . $_SESSION['successMessage'] . "</p>";
            unset($_SESSION['successMessage']);
        }
        if (isset($_SESSION['errorMessage'])) {
            echo "<p style='color: red;'>" . $_SESSION['errorMessage'] . "</p>";
            unset($_SESSION['errorMessage']);
        }
        ?>













</div>



  
</body>
</html>

<style>


*,
*::before,
*::after{
    box-sizing: border-box;
}





:root{
    
    --clr-text: #030104;
    --clr-text-lessopacity: #62645B;

    --clr-nav-bar-color: #62645B;
    --clr-background-color: #292D32;

    --clr-primary-color: #01BCD6;
    --clr-button-color: #6C70DC;
    
    --clr-light: #FFF;
    --clr-dark: #000;


    --fw-light: 300;
    --fw-regular: 400;
    --fw-medium: 500;
    --fw-semibold: 600;
    --fw-bold: 700;

    


    

}


body {
  

  margin: 0;
  padding: 0;
  font-family: 'Poppins', sans-serif;
  font-size: 16px;
  line-height: 1.5;

  background-color: var(--clr-primary-color);






}

.container {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);

  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;

  background-color: white;
  padding: 48px;
  border-radius: 4px;
}


.check {
    background-color: var(--clr-primary-color) ;
    height: 56px;
    width: 56px;
    padding: 12px;
    border-radius: 120px;

    display: flex;
    align-items: center;
    justify-content: center;
}

.check i {
  margin: 0;
  font-size: 32px;
  color: var(--clr-light);
  font-weight: var(--fw-light);
}


p {
  font-size: 20px;
  margin: 0;
  margin-top: 32px;
  font-weight: var(--fw-semibold);
}

span {
  font-size: 1rem;
  text-align: center;
  color: var(--clr-text-lessopacity);
}


.inputbox {
    margin-top: 24px;
    width: 500px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.inputbox input {

    outline: none;
    border: none;
    background-color: #f5f5f5;
    padding: 12px 12px;
  font-size: 16px;

}

.buttons {
    margin-top: 24px;
   
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 24px;
}


.resend-email {
    border: none;
    outline: none;
    border-radius: 4px;
    padding: 12px;
    background-color: var(--clr-primary-color);
    color: white;
    font-size: 16px;

    font-family: 'Poppins', 'sans-serif';
}


.buttons a {
    text-decoration: none;
    color: var(--clr-primary-color);
}



.success-message {
    display: none;
    position: absolute;
    left: 50%;
    bottom: 20px;
    transform: translate(-50%, -50%);
    margin: 0 auto;
    color: #270;
    background-color: #DFF2BF;
    padding: 10px;
    border-radius: 5px;
}




.error-message {
    display: none;
    position: absolute;
    left: 50%;
    bottom: 20px;
    transform: translate(-50%, -50%);
    margin: 0 auto;
    color: #D8000C;
    background-color: #FFBABA;
    padding: 10px;
    border-radius: 5px;
}






</style>