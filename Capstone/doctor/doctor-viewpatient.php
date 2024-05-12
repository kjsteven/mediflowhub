<?php
// viewpatient.php

require '../session/db.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctor-login.php");
    exit();
}

// Check if the patient ID is provided in the URL
if (!isset($_GET['patient_id'])) {
    header("Location: doctor-viewallpatient.php");
    exit();
}

$patientId = $_GET['patient_id'];

// Fetch appointment details based on the patient ID
$sqlAppointments = "SELECT * FROM appointments WHERE Patient_id = $patientId";
$resultAppointments = $conn->query($sqlAppointments);

// Fetch patient details based on the provided ID
$sql = "SELECT * FROM patients_table WHERE Patient_id = $patientId";
$result = $conn->query($sql);
$patientDetails = $result->fetch_assoc();

// Close the database connection
$conn->close();



$birthdate = new DateTime($patientDetails['Date_of_Birth']);
$today = new DateTime();
$age = $today->diff($birthdate)->y;


$weight = $patientDetails['Weight'];
$height = $patientDetails['Height'];

$bmi = $weight / pow($height, 2);
$roundedBmi = round($bmi, 2);





?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor | View Patient </title>


    <link rel="icon" href="images/logo.png" type="image/png">

    <link rel="stylesheet" type="text/css" href="style/doctor-viewpatient.css">
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

                <li >
                    <a href="doctor-dashboard.php" >
                       <i class='bx bxs-dashboard'></i>
                         <span>Dashboard</span>
                    </a>
                </li>

                
                <li class="active">
                    <a href="doctor-appointment.php">
                        <i class='bx bxs-time-five'></i>
                        <span>Appointments</span>
                    </a>
                </li>


                
                <li>
                    <a href="doctor-all.php">
                        <i class='bx bxs-user-rectangle' ></i>
                        <span>Doctors</span>
                    </a>
                </li>


          

            
       

                <li>    
                    <a href="profile.php">
                        <i class='bx bxs-cog' ></i>
                        <span>Settings</span>
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

                            <p class="name"> <?php echo 'Patient ID: ' . $patientDetails['Patient_id'] ; ?>  </p>


                    </div>


                    <div class="information-view">

                            <p class="text-view">Full Name : <span class="information"> <?php echo $patientDetails['Last_Name'] . ', ' . $patientDetails['First_Name']; ?></span></p>
                            <p class="text-view">Age :  <span class="information"> <?php echo $age; ?> years old </span></p>
                            <p class="text-view">Date of Birth : <span class="information">  <?php echo date('F j, Y', strtotime($patientDetails['Date_of_Birth'])); ?></span></p>
                            <p class="text-view">Phone : <span class="information"> <?php echo $patientDetails['Phone']; ?></span></p>
                            



                            <p class="text-view">Height : <span class="information"> <?php echo $patientDetails['Height']; ?></span></p>
                            <p class="text-view">Weight : <span class="information"> <?php echo $patientDetails['Weight']; ?></span></p>
                            <p class="text-view">BMI : <span class="information"> <?php echo $roundedBmi ?></span></p>
                           

                            <p class="text-view"><span class="information"></span></p>




                    </div>


                    <div class="information-view">

                    <h2>Appointments</h2>

                    <?php while ($appointmentDetails = $resultAppointments->fetch_assoc()) : ?>
    <?php if ($appointmentDetails['Status'] == 'Completed') : ?>
        <p class="text-view">Appointment ID : <span class="information"><?php echo $appointmentDetails['Appointment_ID']; ?></span></p>
        <p class="text-view">Prescription : <span class="information"><?php echo $appointmentDetails['Prescription']; ?></span></p>
        <hr>
    <?php endif; ?>
<?php endwhile; ?>


           

                    </div>



                    <div class="backbtn">

                    <a href="doctor-appointment.php">Back to Appointments</a>

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
