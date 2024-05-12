<?php 


require '../session/db.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

if (isset($_POST["submit"])) {
    // Get user input
    $email = $_POST['username'];
    $password = $_POST['password'];

    // Validate user input
    if (empty($email) || empty($password)) {
        echo "Please enter both email and password.";
    } else {
        // Fetch user from the database
        $stmt = $conn->prepare("SELECT * FROM doctors_table WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $row["Password"])) {
                // Password is correct, set session variables
                $_SESSION['doctor_id'] = $row["doctor_id"];
                $_SESSION['username'] = $row["Email"];
                $_SESSION["first_name"] = $row["First_Name"];

                // Redirect to the dashboard or any other page
                header("Location: doctor-dashboard.php");
                exit();
            } else {
                echo "<script>alert('Incorrect password.')</script>";
            }
        } else {
            echo "<script>alert('User not found.')</script>";
        }

        $stmt->close();
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Login | MediFlowHub</title>


    <link rel="icon" href="images/logo.png" type="image/png">



    <link rel="stylesheet" type="text/css" href="style/doctor-login.css">
    <link rel="stylesheet" href="../users/style/transitions.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">




    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- FOR ICONS-->
     <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">


</head>

<body>
   


    <div class="login-container">

    

        <form method="post">

          <h1>Doctor - Login</h1>
                
                <div class="input-box">
                    <input type="text" id="username" name="username" placeholder="Email" required>
                    <i class='bx bxs-envelope'></i>  
                </div>
                
                <div class="input-box">
                        
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <i class="fa fa-eye-slash toggle" id="eye-password" onclick="togglePasswordVisibility('password', 'eye-password')"></i>
                </div>
            
       
             

                <div class="Forgot-btn">
                  
                    <a href="forgotpassword.php">Forgot password?</a>
                  </div>
                
 
       
   
            <button name="submit" class="login-button" type="submit"><span>Login</span></button>
        
        
    </form>
        
    


        </div>





        
<div class="buttons-links">

 
<a class="btn1" href="../users/login.php">
    <span>User</span>
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