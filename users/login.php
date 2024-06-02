<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require '../session/db.php';
require '../config/config.php';
require '../admin/EventLogger.php';

session_start();

if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION["user_id"])) {
    if ($_SESSION["role"] == "Admin") {
        header("Location: ../admin/admin-dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit;
}

if (isset($_GET['timeout']) && $_GET['timeout'] === 'true') {
    echo "<script>alert('Session Timeout. Please login again.')</script>";
}

// Define lockout policy parameters
$maxFailedAttempts = 5; // Maximum allowed failed attempts
$lockoutDuration = 30 * 60; // Lockout duration in seconds (30 minutes)

if (isset($_POST["submit"])) {
    $username = filter_var($_POST["username"], FILTER_VALIDATE_EMAIL);
    $password = $_POST["password"];

    if (!$username) {
        echo "<script>alert('Username is not a valid email address.')</script>";
        exit;
    }

    // Check if user is locked out
    $query = "SELECT Failed_Attempts, Last_Failed_Attempt, password_expiration FROM users WHERE Email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($failedAttempts, $lastFailedAttempt, $passwordExpiration);
    $stmt->fetch();

    // Check lockout conditions
    if ($failedAttempts >= $maxFailedAttempts && strtotime($lastFailedAttempt) > (time() - $lockoutDuration)) {
        echo "<script>alert('Account is locked for 30 minutes, due to multiple failed login attempts. Please change your password or try again later.')</script>";
        exit;
    }

    // Proceed with login attempt
    $query = "SELECT * FROM users WHERE Email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        error_log("Database query error: " . $conn->error);
        echo "<script>alert('An unexpected error occurred. Please try again.');</script>";
        exit;
    }

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Check if the user is activated
        if ($row["Activated"] == 0) {
            header("Location: ../session/resendemail.php");
            exit;
        }

        $hashedPassword = $row["Password"];

        // Verify password
        if (password_verify($password, $hashedPassword)) {
            // Check password expiration
            $currentDate = date('Y-m-d');
            if ($passwordExpiration < $currentDate) {
                $_SESSION['errorMessage'] = "Your password has expired. Please reset your password.";
                header("Location: ../session/resetpassword.php"); // Redirect to your password reset page
                exit;
            }

            // Reset failed attempts on successful login
            $query = "UPDATE users SET Failed_Attempts = 0, Last_Failed_Attempt = NULL WHERE Email = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $username);
            $stmt->execute();

            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION["username"] = $row["Email"];
            $_SESSION["first_name"] = $row["First Name"];
            $_SESSION["role"] = $row["Role"];

            // Log successful login
            $eventLogger = new EventLogger();
            $eventLogger->logLoginEvent($_SESSION["user_id"]);

            if ($row["OTP_used"] == 0) {
                // Generate and send a new OTP
                $otp = mt_rand(100000, 999999);
                $otpExpiration = date('Y-m-d H:i:s', strtotime('+1 month'));

                // Send OTP via email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = SMTP_HOST;
                    $mail->SMTPAuth = true;
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;
                    $mail->Username = SMTP_USERNAME; 
                    $mail->Password = SMTP_PASSWORD; 

                    $mail->setFrom(SMTP_USERNAME, 'MediflowHub | OTP Verification');
                    $mail->addAddress($username);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'OTP Code for Login';
                    $mail->Body    = 'Your OTP is: ' . $otp;

                    $mail->send();
                    echo "<script>alert('OTP sent to your email.');</script>";

                    // Update OTP and expiration in database
                    $lastInsertedUserId = $row["user_id"];
                    $query = "UPDATE users SET OTP = ?, OTP_expiration = ? WHERE user_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("sss", $otp, $otpExpiration, $lastInsertedUserId);
                    $stmt->execute();

                    // Store OTP in a cookie
                    setcookie("otp", $otp, time() + 30 * 24 * 60 * 60); // 1 month expiration
                    setcookie("otp_expiration", $otpExpiration, time() + 30 * 24 * 60 * 60);

                    header("Location: otp.php");
                    exit;
                } catch (Exception $e) {
                    echo "<script>alert('Error sending OTP. Please try again. Error: " . $e->getMessage() . "');</script>";
                }
            } else {
                // Redirect based on user role
                if ($_SESSION["role"] == "Admin") {
                    header("Location: ../admin/admin-dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit;
            }
        } else {
            // Incorrect password: increment failed attempts and update last failed attempt timestamp
            $failedAttempts = $failedAttempts + 1;
            $query = "UPDATE users SET Failed_Attempts = ?, Last_Failed_Attempt = NOW() WHERE Email = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $failedAttempts, $username);
            $stmt->execute();

            echo "<script>alert('Incorrect password.')</script>";
        }
    } else {
        echo "<script>alert('User not found. Please register.')</script>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | MediFlowHub</title>


    <link rel="icon" href="images/logo.png" type="image/png">



    <link rel="stylesheet" type="text/css" href="style/Login.css">
    <link rel="stylesheet" href="style/transitions.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">




    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- FOR ICONS-->
     <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">


</head>

<body>
   


    <div class="login-container">

    

        <form action="login.php" method="post">

          <h1>Login</h1>
                
                <div class="input-box">
                    <input type="text" id="username" name="username" placeholder="Email" required>
                    <i class='bx bxs-envelope'></i>  
                </div>
                
                <div class="input-box">
                        
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <i class="fa fa-eye-slash toggle" id="eye-password" onclick="togglePasswordVisibility('password', 'eye-password')"></i>
                </div>
            
       
             

                <div class="Forgot-btn">
                  
                    <a href="../session/forgotpassword.php">Forgot password?</a>
                  </div>
                
 
       
   
            <button name="submit" class="login-button" type="submit"><span>Login</span></button>
            <p class="Signup-btn"> Don't have an account yet? <a href="signup.php"><span>Register</span></a></p>
        
    </form>
        
    


        </div>



<div class="buttons-links">

 
            <a class="btn1" href="../doctor/doctor-login.php">
                <span>Employee</span>
                <svg width="34" height="34" viewBox="0 0 74 74" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="37" cy="37" r="35.5" stroke="white" stroke-width="3"></circle>
                    <path d="M25 35.5C24.1716 35.5 23.5 36.1716 23.5 37C23.5 37.8284 24.1716 38.5 25 38.5V35.5ZM49.0607 38.0607C49.6464 37.4749 49.6464 36.5251 49.0607 35.9393L39.5147 26.3934C38.9289 25.8076 37.9792 25.8076 37.3934 26.3934C36.8076 26.9792 36.8076 27.9289 37.3934 28.5147L45.8787 37L37.3934 45.4853C36.8076 46.0711 36.8076 47.0208 37.3934 47.6066C37.9792 48.1924 38.9289 48.1924 39.5147 47.6066L49.0607 38.0607ZM25 38.5L48 38.5V35.5L25 35.5V38.5Z" fill="white"></path>
                </svg>
            </a>

    

</div>



        <script>
          function togglePasswordVisibility(inputId, eyeId) {
              const passwordInput = document.getElementById(inputId);
              const eyeIcon = document.getElementById(eyeId);
  
              if (passwordInput.type === "password") {
                  passwordInput.type = "text";
                  eyeIcon.classList.remove("fa-eye-slash");
                  eyeIcon.classList.add("fa-eye");
              } else {
                  passwordInput.type = "password";
                  eyeIcon.classList.remove("fa-eye");
                  eyeIcon.classList.add("fa-eye-slash");
              }
          }
      </script>

<script src="script/script.js"></script>

</body>




</html>