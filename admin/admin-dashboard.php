<?php
include('../session/auth.php');
require_once '../session/session_manager.php';
require '../session/db.php';

start_secure_session();


// Set HTTP headers to prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

echo '<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>';



if (!isset($_SESSION["username"])) {

    header("Location: ../users/login.php"); 
    exit;
}

if (!check_admin_role()) {
    // Redirect to the user dashboard or show an error message
    header('Location: ../users/dashboard.php');
    exit();
}



$firstName = $_SESSION["first_name"];






$sqlUsers = "SELECT COUNT(user_id) AS total_users FROM users";
$resultUsers = $conn->query($sqlUsers);
$rowUsers = $resultUsers->fetch_assoc();
$totalUsers = $rowUsers['total_users'];

$sqlDoctors = "SELECT COUNT(doctor_id) AS total_doctors FROM doctors_table";
$resultDoctors = $conn->query($sqlDoctors);
$rowDoctors = $resultDoctors->fetch_assoc();
$totalDoctors = $rowDoctors['total_doctors'];


$sqlPatients= "SELECT COUNT(Patient_id) AS total_doctors FROM patients_table";
$resultPatients = $conn->query($sqlPatients);
$rowPatients = $resultPatients->fetch_assoc();
$totalPatients = $rowPatients['total_doctors'];


// Fetch the counts from the third table
$sqlAppointments = "SELECT COUNT(Appointment_ID) AS total_appointments FROM appointments";
$resultAppointments = $conn->query($sqlAppointments);
$rowAppointments = $resultAppointments->fetch_assoc();
$totalAppointments = $rowAppointments['total_appointments'];




?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>


    <link rel="icon" href="images/logo.png" type="image/png">

    <link rel="stylesheet" type="text/css" href="style/admin-dashboard.css">
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

                <li class="active">
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


                
        

                <li>
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
                <a href="backup.php">
                    <i class='bx bxs-data'></i>
                    <span>Backup</span>
                </a>
                </li>


                <li>
                <a href="error.php">
                    <i class='bx bxs-data'></i>
                    <span>Error Logs</span>
                </a>
                </li>

                <li>
                <a href="eventlogs.php">
                    <i class='bx bxs-data'></i>
                    <span>Event Logs</span>
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



    <div class="first-container">

        <h1>Welcome, <?php echo $firstName; ?> | Admin </h1>


        <div class="inside-container">

            <div class="card-container">

                <div class="card-box">


                        <div class="topbox">

                            <i class='bx bxs-user'></i>

                                <div class="text-box">
                                    
                                    <p class="text-patients">Registered Users:  <span><?php echo $totalUsers; ?></span></p>
                                    
                                </div>



                        </div>

                        <div class="bottombox">

                            <a href="admin-viewallusers.php" target="_blank">View Details</a>

                                <i class='bx bx-chevron-right'></i>

                                
                        </div>

                       


                </div>

                <div class="card-box">


                    <div class="topbox">

                        <i class='bx bxs-group'></i>

                            <div class="text-box">
                            
                                <p class="text-patients">Total Doctors:  <span><?php echo $totalDoctors; ?></span></p>
                            
                            </div>


                    </div>

                    <div class="bottombox">

                    <a href="admin-viewalldoctor.php">View Details</a>
                    <i class='bx bx-chevron-right'></i>


                    </div>



                    </div>


                <div class="card-box">


                <div class="topbox">

                    <i class='bx bxs-bed' ></i>

                        <div class="text-box">
                           
                            <p class="text-patients">Total Patients:  <span><?php echo $totalPatients; ?></span></span></p>
                        
                        </div>


                        </div>


                        <div class="bottombox">

                        <a href="admin-viewallpatient.php">View Details</a>
                        <i class='bx bx-chevron-right'></i>


                        </div>



                </div>



                <div class="card-box">


                    <div class="topbox">

                    <i class='bx bxs-book-content'></i>

                        <div class="text-box">
                           
                            <p class="text-patients">Total Appointments:  <span><?php echo $totalAppointments; ?></span></span></p>
                         
                        </div>

                        </div>


                        <div class="bottombox">

                            <a href="admin-appointment.php">View Details</a>
                            <i class='bx bx-chevron-right'></i>


                            </div>



                </div>



                
            </div>






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
