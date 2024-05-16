<?php
// viewpatient.php

include('../session/auth.php');
require_once '../session/session_manager.php';
require '../session/db.php';

session_start();

if (!isset($_SESSION["username"])) {

    header("Location: ../users/login.php"); 
    exit;
}

if (!check_admin_role()) {
    // Redirect to the user dashboard or show an error message
    header('Location: ../users/dashboard.php');
    exit();
}



// Check if the patient ID is provided in the URL
if (!isset($_GET['doctor_id'])) {
    header("Location: doctor-viewalldoctor.php");
    exit();
}

$doctorID = $_GET['doctor_id'];



// Fetch patient details based on the provided ID
$sql = "SELECT * FROM doctors_table WHERE doctor_id = $doctorID ";
$result = $conn->query($sql);
$doctorDetails = $result->fetch_assoc();

// Close the database connection
$conn->close();








?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor | View Patient </title>


    <link rel="icon" href="images/logo.png" type="image/png">

    <link rel="stylesheet" type="text/css" href="style/admin-viewdoctor.css">
    <link rel="stylesheet" href="style/transitions.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">




 
    
</head>
<body>

    <div id="sidebar" class="sidebar">
        <div class="logo">
            <img src="images/MediFlowHub.png" alt="">

            <i class='bx bx-x' id="close-sidebar"></i>
        </div>

        
            <ul class="menu">


                <p>HOME</p>

                <li>
                    <a href="admin-dashboard.php" >
                        <i class='bx bxs-dashboard'></i>
                        <span>Dashboard</span>
                    </a>
                </li>


                
            
                <li>
                    <a href="admin-appointment.php">
                        <i class='bx bxs-time-five' ></i>
                        <span>Appointments</span>
                    </a>
                </li>


                
        

                <li  class="active">
                <button class="dropdown-btn">
                        <i class='bx bxs-user-rectangle' ></i>
                        <span>Doctors</span>
                        <i class='bx bxs-chevron-down'></i>
                    </button>

                    <div class="dropdown-container">
                            <a href="admin-adddoctor.php">Add/Delete Doctor</a>
                            <a href="admin-viewalldoctor.php">View All Doctor</a>
                          
                    </div>

                </li>


                <li>
                    <a href="admin-viewallpatient.php">
                        <i class='bx bx-plus-medical' ></i>
                        <span>Patients</span>
                    </a>
                </li>


                <p>DATABASE</p>


                <li>
                    <a href="admin-viewallpatient.php">
                    <i class='bx bxs-data'></i>
                        <span>Backup</span>
                    </a>
                </li>



                
               

          

                <li class="logout">
                    <a href="logout.php" id="logout-link">
                        <i class='bx bx-log-out'></i>
                        <span>Logout</span>
                    </a>
                </li>






            </ul>




    
    </div>


<div class="main--content">



    <div class="header--wrapper">
                    
                    <div class="menu-search">
                    
                                <i class='bx bx-menu' id="menu-toggle"></i>

                                

                    </div>
                    
               
                    
                </div>

        <div class="inside-container">


                    <div class="profile-view">

                            <img src="https://i.pinimg.com/564x/63/53/d9/6353d9fff14cc31af369dd0254fd8c97.jpg" alt="" width="80px" height="80px">

                            <p class="name"> <?php echo 'Doctor ID: ' . $doctorDetails ['doctor_id'] ; ?>  </p>


                    </div>


                    <div class="information-view">

                            <p class="text-view">Full Name : <span class="information"> <?php echo $doctorDetails['Last_Name'] . ', ' . $doctorDetails['First_Name']; ?></span></p>
                            <p class="text-view">Email : <span class="information"> <?php echo $doctorDetails['Email']; ?></span></p>
                            <p class="text-view">Specialty : <span class="information"> <?php echo $doctorDetails['Specialty']; ?></span></p>
                            <p class="text-view">Phone : <span class="information"> <?php echo $doctorDetails['Phone_Number']; ?></span></p>
                            

                            <p class="text-view"><span class="information"></span></p>




                    </div>


             


                    <div class="backbtn">

                    <a href="admin-viewalldoctor.php">Back to View All Doctor</a>

                    </div>






        </div>





</div>



    <script src="script/script.js"></script>


    <script> 

    /* Loop through all dropdown buttons to toggle between hiding and showing its dropdown content - This allows the user to have multiple dropdowns without any conflict */
var dropdown = document.getElementsByClassName("dropdown-btn");
var i;

for (i = 0; i < dropdown.length; i++) {
  dropdown[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var dropdownContent = this.nextElementSibling;
    if (dropdownContent.style.display === "block") {
      dropdownContent.style.display = "none";
    } else {
      dropdownContent.style.display = "block";
    }
  });
}


    </script>







 
</body>
</html>
