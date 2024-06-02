<?php

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require '../session/db.php'; // Include your database connection file
require '../config/config.php';

if (isset($_POST["submit"])) {
    $last_name = $_POST["Last-name"];
    $first_name = $_POST["First-name"];
    $phonenumber = $_POST["phone-number"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confpassword = $_POST["confirm-password"];
    $nowtime = date("Y-m-d");

    // Set password expiration date to 90 days from now
    $passwordExpiration = date("Y-m-d", strtotime("+90 days"));

    // Check if the checkbox is checked
    $agreeTerms = isset($_POST["agree-terms"]) && $_POST["agree-terms"] === "on";

    if (!$agreeTerms) {
        $_SESSION['errorMessage'] = "Please agree to the Terms and Conditions.";
        header("Location: signup.php");
        exit();
    }

    // Check if the user already exists
    $query = "SELECT * FROM users WHERE Email = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['errorMessage'] = "Username already exists.";
        header("Location: signup.php");
        exit();
    }

    // Validate the username as a valid email address
    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['errorMessage'] = "Username is not a valid email address.";
        header("Location: signup.php");
        exit();
    }

    // Remove spaces and non-numeric characters from the phone number
    $phonenumber = preg_replace('/[^0-9]/', '', $phonenumber);

    // Check if the phone number is exactly 11 digits long and starts with "09"
    if (strlen($phonenumber) !== 11 || substr($phonenumber, 0, 2) !== '09') {
        $_SESSION['errorMessage'] = "Phone number is not a valid mobile number.";
        header("Location: signup.php");
        exit();
    }

    $first_name = mb_convert_case($first_name, MB_CASE_TITLE);
    $last_name = mb_convert_case($last_name, MB_CASE_TITLE);
    $username = strtolower($username);

    if ($password === $confpassword) {
        // Hash the password for security (you should use a proper hashing method)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Generate a unique token for email verification
        $token = md5($username);

        // Insert user data into the database with the token and password expiration date
        $query = "INSERT INTO users (`Last Name`, `First Name`, Email, password, `Phone Number`, `Role`, `Token`, `Date_Added`, `password_expiration`) VALUES (?, ?, ?, ?, ?, 'User', ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);

        // Bind the parameters
        mysqli_stmt_bind_param($stmt, "ssssssss", $last_name, $first_name, $username, $hashedPassword, $phonenumber, $token, $nowtime, $passwordExpiration);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        // Check if the insertion was successful
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            // Send verification email
            sendVerificationEmail($username, $token, $first_name);
            $_SESSION['successMessage'] = "Registration Successful, A verification link has been sent to your email account";
            header("Location: signup.php");
            exit();
        } else {
            $_SESSION['errorMessage'] = "Error registering user";
            header("Location: signup.php");
            exit();
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['errorMessage'] = "Password does not match";
        header("Location: signup.php");
        exit();
    }
}


function sendVerificationEmail($username, $token, $first_name)
{
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    //Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Your SMTP server
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->Username = SMTP_USERNAME; // Use the constant
    $mail->Password = SMTP_PASSWORD; // Use the constant
    $mail->Subject = 'Email Verification';

 

  

    $verificationLink = 'https://mediflow.website/session/verify.php?token=' . $token;
    // $verificationLink = 'https://localhost/Capstone_PhpFiles/session/verify.php?token=' . $token;
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
    <title>Signup | MediFlowHub</title>
    <link rel="icon" href="images/Logo.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="style/Signup.css">
    <link rel="stylesheet" href="style/transitions.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FOR ICONS-->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>
    <div class="login-container">
        <form action="signup.php" method="post">
            <h1>Signup</h1>

            <div class="full-name">
                <div class="full-name-box">
                    <input class="input-field" type="text" id="Last-name" name="Last-name" placeholder="Last Name" required>
                
                </div>
                <div class="full-name-box">
                    <input class="input-field" type="text" id="First-name" name="First-name" placeholder="First Name" required>
              
                </div>
                
            </div>
         
            <div class="input-box">
                <input class="input-field" type="phone" id="phone-number" name="phone-number" placeholder="Phone Number" required>
                <i class='bx bxs-phone' ></i>
            </div>

            <div class="input-box">
                <input class="input-field" type="text" id="username" name="username" placeholder="Email" required>
                <i class='bx bxs-envelope'></i>  
            </div>

            <div class="input-box">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class="fa fa-eye-slash toggle" id="eye-password" onclick="togglePasswordVisibility('password', 'eye-password')"></i>
            
            </div>

            <div id="password-strength" class="password-strength"></div>

            <div class="input-box">
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm Password" required>
                <i class="fa fa-eye-slash toggle" id="eye-confirm-password" onclick="togglePasswordVisibility('confirm-password', 'eye-confirm-password')"></i>
            </div>

       
          






            <div class="agree-terms">
                <input type="checkbox" name="agree-terms">
                <label for="agree-terms">I agree to the <a href="https://sites.google.com/view/mediflow/home" target="_blank">Terms and Conditions</a></label>
            </div>

 
            
            
            <button name="submit" class="Signup-btn" type="submit" id="signupButton" disabled><span>Signup</span></button>

            <p class="Login-btn"> Already have an Account? <a href="login.php"><span>Login</span></a></p>


            
        <div id="successMessage" class="success-message"><i class='bx bx-check'></i> Registration Successful,
        Please check your email for Verification</div>

        <div id="errorMessage" class="error-message"><i class='bx bxs-x-circle'></i> </div>

        </form>


        
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


<script>
    $(document).ready(function() {
        $('#password').on('input', function() {
            checkPasswordStrength($(this).val());
        });

        function checkPasswordStrength(password) {
            // Reset the password strength indicator and enable signup button
            $('#password-strength').html('');
            enableSignupButton();

            // Minimum length
            if (password.length < 12) {
                $('#password-strength').append('<span style="color:red;">Minimum 12 characters</span>');
                disableSignupButton();
                return;
            }

            // Letters and numbers
            if (!password.match(/([a-zA-Z])/) || !password.match(/([0-9])/)) {
                $('#password-strength').append('<span style="color:red;">Include both letters and numbers</span>');
                disableSignupButton();
                return;
            }

            // Special characters
            if (!password.match(/([!@#$%^&*()_+{}\[\]:;<>,.?~\\/-])/)) {
                $('#password-strength').append('<span style="color:red;">Include at least one special character</span>');
                disableSignupButton();
                return;
            }

            // If all criteria are met, display strong password indicator
            $('#password-strength').append('<span style="color:green;">Strong password!</span>');
        }

        function enableSignupButton() {
            var signupButton = document.getElementById("signupButton");
            signupButton.disabled = false;
        }

        function disableSignupButton() {
            var signupButton = document.getElementById("signupButton");
            signupButton.disabled = true;
        }
    });
</script>


<script>
        var successMessage = "<?php echo isset($_SESSION['successMessage']) ? $_SESSION['successMessage'] : ''; ?>";
        if (successMessage !== "") {
            var successMessageDiv = document.getElementById("successMessage");
            successMessageDiv.textContent = successMessage;
            successMessageDiv.style.display = "block";

            // Scroll to the success message for better visibility
            successMessageDiv.scrollIntoView({
                behavior: 'smooth'
            });

            // Remove the session variable to avoid displaying the message on subsequent page loads
            <?php unset($_SESSION['successMessage']); ?>;
        }

        var errorMessage = "<?php echo isset($_SESSION['errorMessage']) ? $_SESSION['errorMessage'] : ''; ?>";

        if (errorMessage !== "") {
            var errorMessageDiv = document.getElementById("errorMessage");
            errorMessageDiv.textContent = errorMessage;
            errorMessageDiv.style.display = "block";

            // Scroll to the error message for better visibility
            errorMessageDiv.scrollIntoView({
                behavior: 'smooth'
            });

            <?php unset($_SESSION['errorMessage']); ?>;
        }
    </script>

</body>
</html>

