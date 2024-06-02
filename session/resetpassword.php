<?php
session_start();

require '../session/db.php';

// Check if the reset token is provided in the URL
if (isset($_GET['resettoken'])) {
    $resetToken = $_GET['resettoken'];

    // Check if the email address is stored in the session
    if (isset($_SESSION['resetEmail'])) {
        $resetEmail = $_SESSION['resetEmail'];

        // Check if the form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $password = $_POST["password"];
            $confirm_password = $_POST["confirm_password"];

            // Validate the password and confirm password
            if ($password != $confirm_password) {
                $_SESSION['errorMessage'] = "Password and Confirm Password do not match.";
                header("Location: resetpassword.php?resettoken=$resetToken");
                exit();
            }

            // Check if the new password matches any in the password history
            $stmt = $conn->prepare("
                SELECT hashed_password FROM password_history 
                WHERE user_id = (SELECT user_id FROM users WHERE Email = ?)
            ");
            $stmt->bind_param("s", $resetEmail);
            $stmt->execute();
            $stmt->bind_result($hashedPasswordFromHistory);
            while ($stmt->fetch()) {
                if (password_verify($password, $hashedPasswordFromHistory)) {
                    $stmt->close();
                    $_SESSION['errorMessage'] = "You cannot use a previous password. Please choose a different password.";
                    header("Location: resetpassword.php?resettoken=$resetToken");
                    exit();
                }
            }
            $stmt->close();

            // Hash the new password before storing it in the database
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Update the user's password and set the new password expiration date in the database
            $passwordExpiration = date('Y-m-d', strtotime('+90 days'));
            $stmt = $conn->prepare("UPDATE users SET Password = ?, Failed_Attempts = 0, Last_Failed_Attempt = NULL, password_expiration = ? WHERE Email = ?");
            $stmt->bind_param("sss", $hashedPassword, $passwordExpiration, $resetEmail);
            $stmt->execute();
            $stmt->close();

            // Maintain only the last 5 passwords in the password history
            $stmt = $conn->prepare("
                SELECT id FROM password_history 
                WHERE user_id = (SELECT user_id FROM users WHERE Email = ?) 
                ORDER BY created_at DESC LIMIT 5, 1
            ");
            $stmt->bind_param("s", $resetEmail);
            $stmt->execute();
            $stmt->bind_result($oldestId);
            if ($stmt->fetch()) {
                $stmt->close();
                $stmt = $conn->prepare("DELETE FROM password_history WHERE id <= ?");
                $stmt->bind_param("i", $oldestId);
                $stmt->execute();
            } else {
                $stmt->close();
            }

            // Insert the new password into the password history
            $stmt = $conn->prepare("
                INSERT INTO password_history (user_id, hashed_password) 
                VALUES ((SELECT user_id FROM users WHERE Email = ?), ?)
            ");
            $stmt->bind_param("ss", $resetEmail, $hashedPassword);
            $stmt->execute();
            $stmt->close();

            // Set session variables or cookies for successful password change if needed
            $_SESSION['successMessage'] = "Password reset successfully!";
            header("Location: resetpassword.php?resettoken=$resetToken");
            exit();
        }
    } else {
        // Email address not found in the session
              // Email address not found in the session
              $_SESSION['errorMessage'] = "Error: Email not found in session.";
              header("Location: forgotpassword.php");
              exit();
          }
      } else {
          // Reset token not provided
          $_SESSION['errorMessage'] = "Error: Reset token not found.";
          header("Location: forgotpassword.php");
          exit();
      }
    ?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | MediFlowHub </title>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <link rel="icon" href="../users/images/logo.png" type="image/png">


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- FOR ICONS-->
     <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">

     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">



    
    <link rel="stylesheet" href="../users/style/forgot.css">


    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>


</head>
<body>

<div class="container">
    <h4>Reset Password </h4>
    <?php $resetEmail = isset($_SESSION['resetEmail']) ? $_SESSION['resetEmail'] : ''; ?>
    <p>Enter a new password for <span><?php echo $resetEmail; ?></span></p>

    <div id="successMessage" class="success-message"><i id="bx-check" class='bx bx-check'></i> </div>
    <div id="errorMessage" class="errorMessage"><i id="bx-error" class='bx bx-x-circle'></i> </div>

    <form action="" method="post" id="forgotForm">


     


            <div class="input-box">
                <input type="password" id="password" class="emailinput" name="password" placeholder="Password" >
                <i class="fa fa-eye-slash toggle" id="eye-password" onclick="togglePasswordVisibility('password', 'eye-password')"></i>
            </div>

            <div id="password-strength" class="password-strength"></div>


            <div class="input-box">               
                <input type="password" id="confirm-password" class="emailinput" name="confirm_password" placeholder="Confirm Password" >
                <i class="fa fa-eye-slash toggle" id="eye-confirm-password" onclick="togglePasswordVisibility('confirm-password', 'eye-confirm-password')"></i>
            </div>


           

            


        <button type="submit" name="verify" class="Verify-btn" id="Reset-btn">Reset Password</button>


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
            // Reset the password strength indicator
            $('#password-strength').html('');

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

            $('#password-strength').append('<span style="color:green;">Strong password!</span>');

            // Enable the signup button if the password is strong
            enableSignupButton();
        }

        function enableSignupButton() {
            var signupButton = document.getElementById("Reset-btn");
            signupButton.disabled = false;
        }

        function disableSignupButton() {
            var signupButton = document.getElementById("Reset-btn");
            signupButton.disabled = true;
        }
    });
</script>






</html>



