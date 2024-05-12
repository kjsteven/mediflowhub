<?php

session_start();


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './config/config.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['Name'];
    $email = $_POST['Email'];
    $message = $_POST['Message'];

    
    require './vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require './vendor/phpmailer/phpmailer/src/SMTP.php';
    require './vendor/phpmailer/phpmailer/src/Exception.php';

    // Create a new PHPMailer instance
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
        $mail->setFrom($email, $name);
        $mail->addAddress('mediflowhub@gmail.com');  // Add your email address here

        // Content
        $mail->isHTML(false);  // Set to true if you want to send HTML-formatted emails
        $mail->Subject = 'New Contact Form Submission';
        $mail->Body = "Name: $name\nEmail: $email\n\nMessage: $message";

        // Send the email
        $mail->send();

        // You may want to redirect the user to a thank-you page after processing the form
        header('Location: ./index/thank-you.html');
        exit();
    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediflowHub Organization</title>

    
    <link rel="stylesheet" type="text/css" href="./index/style.css">
    <link rel="icon" href="./users/images/logo.png" type="image/png">

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Spectral:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
</head>
<body>


<header class="site-header">


    <div class="top-header">


            <div class="hotlines-name">

                    <p>Brgy. Wawa Health Center</p>
                    
            <!--
                    <div class="info">
                        <i class='bx bxs-phone'></i>
                        <p>#</p>
                    </div>  -->
                 
                    <div class="info">
                        <i class='bx bx-current-location'></i>
                        <p>Brgy. Wawa, Taguig City, Philippines</p>
                    </div> 
                   


            </div>


            <div class="social-icons">
                <a href="#"><i class='bx bxl-facebook'></i></a>
                <a href=""><i class='bx bxl-instagram'></i></a> 
            </div>





    </div>




    <div class="bottom-header">

        <img src="./index/images/MediFlowHub.png" alt="">

        <nav class="nav">

            <ul class="nav_list">
                <li class="nav_list-item"><a href="#main-section" class="nav_link">Home</a></li>
                <li class="nav_list-item"><a href="#second-section" class="nav_link">About us</a></li>
                <li class="nav_list-item"><a href="#third-section" class="nav_link">Services</a></li>
                <li class="nav_list-item"><a href="#fourth-section" class="nav_link">Community</a></li>
            </ul>

        </nav>




        <a href="./users/login.php" class="Sign-inBtn">Sign In</a>





    </div>
        
 

 

</header>


<section class="main-section" id="main-section">




        <h1>Protect Your Health <br> 
            &  Take Care Of Your Health</h1>
        
            <p>with high-quality and affordable in-person care, get the care you <br>
                all need whenever you need it</p>
        
        
            <div class="a-links">

                
                <a href="./users/signup.php" class="btn">Get Started!</a>

                <button class="cta">
                    <span class="hover-underline-animation"> Join Our Team </span>
                    <svg viewBox="0 0 46 16" height="10" width="30" xmlns="http://www.w3.org/2000/svg" id="arrow-horizontal">
                        <path transform="translate(30)" d="M8,0,6.545,1.455l5.506,5.506H-30V9.039H12.052L6.545,14.545,8,16l8-8Z" data-name="Path 10" id="Path_10"></path>
                    </svg>
                </button>
        

        
            </div>
        





 








</section>




<section class="second-section" id="second-section">


    <div class="left-info">

        <p class="left-title-info">
            Why MediflowHub?
        </p>


        <h2>We wish to give patients
            in (Brgy name) affordable 
            health care. </h2>


        <p class="left-description">
            The concept of "MediFlow" emerges as a light of innovation in an age where efficiency, 
            as well as accuracy, are essential in healthcare. This innovative approach to health 
            services is dedicated to making systems that transcend traditional barriers, focusing 
            on reducing redundancies, minimizing delays, and enhancing the overall effectiveness 
            of healthcare delivery. 
        </p>





    </div>



    <div class="right-info">

        <img src="./index/images/Illustration1.png" alt="">



    </div>




</section>



<section class="third-section" id="third-section">


    <h2>Health Care Services. We make it easy.</h2>


    <div class="container">

        <div class="box">

            <img src="./index/images/Services.png" alt="">

            <p>Symptom Check</p>

            <a href="">Read on our site<i class='bx bx-right-arrow-alt'></i></a>



        </div>

        <div class="box">

            <img src="./index/images/Services(2).png" alt="" height="230px">

            <p>Medicine Prescription</p>

            <a href="">Visit Site<i class='bx bx-right-arrow-alt'></i></a>



        </div>

        <div class="box">

            <img src="./index/images/Services(3).png" alt="" height="230px">
 
            <p>24/7 Hotline and Email Service </p>

            <a href="">Contact us<i class='bx bx-right-arrow-alt'></i></a>



        </div>






    </div>





</section>





<section class="fourth-section" id="fourth-section">

        <div class="contact-form">


                    <img src="./index/images/illustrationemail.png" alt="" width="600px">

                    <form action="" method="post">

                            
                            <h3>Get in touch</h3>


                            
                            <input type="text" placeholder="Name" name="Name">  
                            <input type="email" placeholder="Email" name="Email">
                            <textarea name="Message" id="" cols="30" rows="10" placeholder="Message"></textarea>


                            <button>Send</button>
                           


                    </form>





        </div>


   




</section>



<footer class="footer">
  	 <div class="container1">
  	 	<div class="row">
  	 		<div class="footer-col">
  	 			<h4>Company</h4>
  	 			<ul>
  	 				<li><a href="#second-section">about us</a></li>
  	 				<li><a href="#third-section">our services</a></li>
  	 				
  	 				
  	 			</ul>
  	 		</div>
  	 		<div class="footer-col">
  	 			<h4>Information</h4>
  	 			<ul>
  	 				<li><a href="#">Supports</a></li>
  	 				<li><a href="./index/terms-conditions.html">Terms & Conditions</a></li>
                    <li><a href="./index/privacypolicy.html">Privacy Policy</a></li>
  	 				
  	 			</ul>
  	 		</div>
  	 		<div class="footer-col">
  	 			<h4>Quick Links</h4>
  	 			<ul>
  	 				<li><a href="./index/developers.html">Developers</a></li>
  	 				<li><a href="fourth-section">Contact us</a></li>
  	 				<li><a href="#">Features</a></li>
  	 			
  	 			</ul>
  	 		</div>
  	 		<div class="footer-col">
  	 			<h4>follow us</h4>
  	 			<div class="social-links">
  	 				<a href="#"><i class='bx bxl-facebook' ></i></a>
  	 				
  	 				<a href="#"><i class='bx bxl-instagram' ></i></a>
  	 				
  	 			</div>
  	 		</div>
  	 	</div>
  	 </div>


     
       <p>Copyright © 2023-2024 <span>Developed by CINS student of, University of Makati with Love</span></p>
  </footer>



    
</body>


<script src="./index/script.js"></script>
</html>


